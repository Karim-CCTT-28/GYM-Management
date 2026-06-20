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
                'user_name' => 'required',
                'recipient' => 'required',
                'clause' => 'required',
                'amount' => 'required|numeric|min:0',
            ]);

            $user = User::where('user_name', $request->user_name)->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found'
                ], 404);
            }

            $sessionReport = SessionReport::where('user_id', $user->id)
                ->whereDate('created_date', Carbon::today())
                ->first();

            if (!$sessionReport) {
                return response()->json([
                    'status' => false,
                    'message' => 'No session report found for today'
                ], 404);
            }


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

            $user_name = session("user_name");

            $user = User::where('user_name', $user_name)->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found'
                ], 404);
            }

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

        $expense->update([
            'isDeleted'=>true
        ]);

        SessionReportController::updateBalance();
        return response()->json([
            'status' => true,
            'message' => 'Expense deleted successfully'
        ]);
    }


    public function update(Request $request, $id)
    {
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