<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\Subscriber;
use App\Models\SubscriptionType;
use App\Models\SessionReport;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Throwable;
class SubscriptionController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subscriptions = Subscription::with('subscriber')->get();

        return view('Subscriptions.Index', compact('subscriptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $subscriber = Subscriber::findOrFail($request->subscriber_id);

        return view('Subscriptions.Create', compact('subscriber'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        try {



            $request->validate([
                'user' => 'required',
                'subscriber_id' => 'required|exists:subscribers,id',
                'subscription_type_id' => 'required|exists:subscription_types,id',
                'start_date' => 'required|date',
            ]);

            $type = SubscriptionType::findOrFail($request->subscription_type_id);

            $startDate = Carbon::parse($request->start_date);

            $endDate = match ($type->duration_unit) {
                'day' => $startDate->copy()->addDays($type->duration),
                'week' => $startDate->copy()->addWeeks($type->duration),
                'month' => $startDate->copy()->addMonths($type->duration),
                'year' => $startDate->copy()->addYears($type->duration),
            };

            if ($endDate->toDateString() < now()->toDateString()) {
                return response()->json([
                    'success' => false,
                    'message' => 'تاريخ انتهاء الاشتراك لا يمكن أن يكون في الماضي.'
                ], 422);
            }

            $existingSubscription = Subscription::where('subscriber_id', $request->subscriber_id)
                ->whereDate('start_date', '<=', $startDate)
                ->whereDate('end_date', '>=', $startDate)
                ->first();

            if ($existingSubscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'هذا المشترك لديه اشتراك فعال خلال هذا التاريخ.'
                ], 401);
            }


            $session = SessionReport::where('user_id', session('user_id'))
                ->whereDate('created_date', Carbon::today()->toDateString())->first();


            Subscription::create([
                'subscriber_id' => $request->subscriber_id,
                'subscription_type_id' => $type->id,
                'start_date' => $request->start_date,
                'end_date' => $endDate->format('Y-m-d'),
                'created_by' => session('user_name') ?? 'System',
                'session_report_id' => $session->id
            ]);

            SessionReportController::updateBalance();
            return response()->json([
                'success' => true,
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $subscription = Subscription::with('subscriber')
            ->findOrFail($id);

        return view('Subscriptions.Show', compact('subscription'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $subscription = Subscription::findOrFail($id);

        $subscribers = Subscriber::all();

        return view('Subscriptions.Edit', compact(
            'subscription',
            'subscribers'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $request->validate([

            'subscriber_id' => 'required|exists:subscribers,id',

            'start_date' => 'required|date',

            'end_date' => 'required|date',

        ]);


        $subscription = Subscription::findOrFail($id);

        $subscription->update([

            'subscriber_id' => $request->subscriber_id,

            'start_date' => $request->start_date,

            'end_date' => $request->end_date,

        ]);

        SessionReportController::updateBalance();
        return redirect('/subscriptions');
        }
        
        /**
         * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $subscription = Subscription::findOrFail($id);
        
        $subscription->delete();
        
        SessionReportController::updateBalance();
        return redirect('/subscriptions');
    }





}