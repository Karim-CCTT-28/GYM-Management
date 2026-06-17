<?php

namespace App\Http\Controllers;
use App\Models\SessionReport;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Throwable;

class SessionReportController extends Controller
{
    public function show(Request $request)
    {

        try {


            $request->validate([
                'user_name' => 'required|string'
            ]);

            $user = User::where('user_name', $request->user_name)->first();

            if (!$user) {
                return response()->json([
                    'message' => 'User not found'
                ], 404);
            }

            $today = Carbon::today()->toDateString();

            $report = $user->sessionReports()
                ->whereDate('created_date', $today)
                ->with(['subscriptions.subscriber', 'subscriptions', 'subscriptions.subscriptionType', 'expenses'])
                ->first();

            // \Log::info($report);

            if (!$report) {
                return response()->json([
                    'message' => 'No session report found for today'
                ], 404);
            }

            return response()->json([
                'report' => $report
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }





    public function setWaterBalance(Request $request)
    {
        $request->validate([
            'water_balance' => 'required|numeric'
        ]);

        $report = SessionReport::whereDate(
            'created_date',
            now()->toDateString()
        )->first();

        if (!$report) {
            return response()->json([
                'message' => 'Session report not found'
            ], 404);
        }

        $report->update([
            'water_balance' => $request->water_balance
        ]);

        return response()->json([
            'message' => 'Water balance updated successfully',
            'water_balance' => $report->water_balance
        ]);
    }
}