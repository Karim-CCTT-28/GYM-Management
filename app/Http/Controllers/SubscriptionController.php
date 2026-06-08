<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\Subscriber;
use Illuminate\Http\Request;

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

        $request->validate([

            'subscriber_id' => 'required|exists:subscribers,id',

            'start_date' => 'required|date',

            'end_date' => 'required|date',

        ]);


        Subscription::create([

            'subscriber_id' => $request->subscriber_id,

            'start_date' => $request->start_date,

            'end_date' => $request->end_date,

        ]);


        return redirect('/subscriptions');
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


        return redirect('/subscriptions');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $subscription = Subscription::findOrFail($id);

        $subscription->delete();

        return redirect('/subscriptions');
    }
}