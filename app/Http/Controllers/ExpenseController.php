<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SessionReport;
use Carbon\Carbon;
class ExpenseController extends Controller
{


    public function store(Request $request)
    {

        try {


            $request->validate([
                'recipient' => 'required',
                'clause' => 'required',
                'amount' => 'required|numeric|min:0',
            ]);


            // i try to see if User exist or not
            $user = User::where('id', session('user_id'))->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // get the session report for  user in the current day
            $sessionReport = SessionReport::where('user_id', $user->id)
                ->whereDate('created_date', Carbon::today())
                ->first();

            if (!$sessionReport) {
                return response()->json([
                    'status' => false,
                    'message' => "the serve didn't make session report for this user ,  
                                    so you need to call Karim (:"
                ], 500);
            }


            //  i get the balance here , to know if we have enough  money or not
            $net_total = SessionReport::whereDate('created_at', Carbon::today())
                ->sum('net_total');
            if ($request->amount > $net_total) {
                return response()->json([
                    'status' => false,
                    'message' => 'لا يوجد رصيد كافي'
                ], 400);
            }

            Expense::create([
                'session_report_id' => $sessionReport->id,
                'recipient' => $request->recipient,
                'clause' => $request->clause,
                'amount' => $request->amount,
            ]);

            // this function calculate the user balance , i use it with incoming and outcoming            
            SessionReportController::updateBalance();

            return response()->json([
                'status' => true,
                'message' => 'Expense created successfully'
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function index()
    {

        try {

            $user_id = session("user_id");

            $user = User::where('id', $user_id)->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // get the session report for  user in the current day
            $sessionReport = SessionReport::where('user_id', $user->id)
                ->whereDate('created_date', Carbon::today())
                ->first();

            if (!$sessionReport) {
                return response()->json([
                    'status' => false,
                    'message' => 'No session report found for today'
                ], 404);
            }

            $expenses = Expense::where(
                'session_report_id',
                $sessionReport->id
            )->get();





            return view("Expenses", ["expenses" => $expenses]);
        } catch (\Throwable $e) {
            \Log::info("D:" . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);

        // soft delete as U want doctor (:

        $expense->delete();
        // $expense->update([
        //     'isDeleted' => true
        // ]);

        // this function calculate the user balance , i use it with incoming and outcoming
        SessionReportController::updateBalance();
        return response()->json([
            'status' => true,
            'message' => 'Expense deleted successfully'
        ]);
    }


    public function update(Request $request, $id)
    {

    // just a normal update operation
        $expense = Expense::findOrFail($id);

        $request->validate([
            'recipient' => 'required|string|max:255',
            'clause' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        $expense->update([
            'recipient' => $request->recipient,
            'clause' => $request->clause,
            'amount' => $request->amount,
        ]);

        SessionReportController::updateBalance();
        return response()->json([
            'status' => true,
            'message' => 'Expense updated successfully',
            'data' => $expense
        ]);
    }



    public function show($id)
    {

        $row = Expense::findOrFail($id);

        return response()->json($row);
    }
}