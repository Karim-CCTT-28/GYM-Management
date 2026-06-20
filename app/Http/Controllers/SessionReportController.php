<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\SessionReport;
use App\Models\Subscription;
use App\Models\Expense;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Throwable;

class SessionReportController extends Controller
{
    public function show()
    {
        try {
   
            $user = User::where('user_name', session("user_name"))
                ->select(['id', 'user_name'])
                ->first();

            if (!$user) {
                return response()->json([
                    'message' => 'User not found'
                ], 404);
            }

            $today = Carbon::today()->toDateString();

            $report = $user->sessionReports()
                ->whereDate('created_date', $today)
                ->with([
                    'user' => function ($query) {
                        $query->select(['id', 'user_name']);
                    },
                    'subscriptions.subscriber' => function ($query) {
                        $query->select(['id', 'name', 'phone']);
                    },
                    'subscriptions.subscriptionType',
                    'expenses'
                ])
                ->first();

            if (!$report) {
                return response()->json([
                    'message' => 'No session report found for today'
                ], 404);
            }

            return response()->json(
                $this->buildReportData($report),
                200
            );

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

    public static function updateBalance()
    {
        $subscriptionsBalance = \DB::table('subscriptions')
            ->join('subscription_types', 'subscriptions.subscription_type_id', '=', 'subscription_types.id')
            ->join('session_reports', 'subscriptions.session_report_id', '=', 'session_reports.id')
            ->whereDate('subscriptions.created_at', \Carbon\Carbon::today())
            ->where('session_reports.user_id', session("user_id"))
            ->sum('subscription_types.price');

        $expenses = \DB::table('expenses')
            ->join('session_reports', 'expenses.session_report_id', '=', 'session_reports.id')
            ->whereDate('expenses.created_at', \Carbon\Carbon::today())
            ->where('session_reports.user_id', session("user_id"))
            ->sum('expenses.amount');

        $water_balance = SessionReport::whereDate('created_date', \Carbon\Carbon::today())
            ->where('user_id', session("user_id"))
            ->sum('water_balance');

        $net_total = $subscriptionsBalance + $water_balance - $expenses;

        SessionReport::whereDate('created_date', \Carbon\Carbon::today())
            ->where('user_id', session("user_id"))
            ->update([
                'net_total' => $net_total
            ]);
    }

    public function index()
    {
        $reports = SessionReport::with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($report) {
                return [
                    'id' => $report->id,
                    'user_name' => $report->user->user_name,
                    'net_total' => $report->net_total,
                    'created_at' => $report->created_at->format('Y-m-d H:i'),
                ];
            });

        return view('Reports.Index', compact('reports'));
    }

    public function adminShow($id)
    {
        $report = SessionReport::with([
            'user' => function ($query) {
                $query->select(['id', 'user_name']);
            },
            'subscriptions.subscriber' => function ($query) {
                $query->select(['id', 'name', 'phone']);
            },
            'subscriptions.subscriptionType',
            'expenses'
        ])->findOrFail($id);

        $data = $this->buildReportData($report);


        return view('Reports.Show', $data);
    }

    private function buildReportData($report)
    {
        $subscriptions = $report->subscriptions;
        $expenses = $report->expenses;

        $subscriptionTotal = $subscriptions->sum(function ($subscription) {
            return $subscription->subscriptionType->price ?? 0;
        });

        $expenseTotal = $expenses->sum('amount');
        $waterBalance = $report->water_balance ?? 0;

        $finalBalance = $subscriptionTotal + $waterBalance - $expenseTotal;

        return [
            'report' => $report,
            'subscriptionTotal' => $subscriptionTotal,
            'expenseTotal' => $expenseTotal,
            'waterBalance' => $waterBalance,
            'finalBalance' => $finalBalance,
        ];
    }
}