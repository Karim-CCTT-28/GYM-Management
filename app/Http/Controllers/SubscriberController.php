<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\Subscriber;
use App\Models\CheckIn;
use Carbon\Carbon;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;



/**
 * Public : getSubscribers
 * Employee : store , checkFace , getVector , getCheckInToday , index , show , create
 * Admin : edit , update , destroy
 */

class SubscriberController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(
                'employee',
                only: [
                    'store',
                    'checkFace',
                    'getVector',
                    'getCheckInToday',
                    'index',
                    'show',
                    'create',
                    'getSubscribers',
                ]
            ),

            new Middleware(
                'admin',
                only: [
                    'edit',
                    'update',
                    'destroy',
                ]
            ),
        ];
    }
    public function getCheckInToday()
    {



        $isExists = Subscriber::select('id')->count();
        if ($isExists == 0) {
            return response()->json(['success' => false, 'message' => 'No Subscribers Found'], 400);
        }
        try {

        // here laravel makes a left join to get check in with its subsc
            $checkInsToday = CheckIn::with('subscriber:id,name')
                ->select('id', 'subscriber_id', 'is_allow', 'created_at', 'check_in_date')
                ->whereDate('check_in_date', Carbon::today())
                ->latest()
                ->get();


            if ($checkInsToday->isEmpty()) {
                return response()->json([
                    'message' => 'no Check ins for today',
                    'subscribersToday' => []
                ], 200);
            }

            $formattedSubscribers = $checkInsToday->map(function ($checkIn) {

                return [
                    'id' => $checkIn->subscriber->id,
                    'name' => $checkIn->subscriber->name,
                    'isAllow' => $checkIn->is_allow,
                    'time' => $checkIn->created_at->format('H:i')
                ];
            });

            return response()->json([
                'subscribersToday' => $formattedSubscribers
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }







    public function checkFace(Request $request)
    {
        $isExists = Subscriber::select('id')->count();
        if ($isExists == 0) {
            return response()->json(['message' => 'No Subscribers Found'], 500);
        }

        try {

            $request->validate([
                'image' => 'required|file|mimes:jpg,jpeg,png'
            ]);



            $subscribers = Subscriber::select(
                'id',
                'vector'
            )->get()->toJson();

            // Api py here
            $response = Http::attach(
                'image',
                file_get_contents($request->file('image')->getRealPath()),
                'face.jpg'
            )->post('http://127.0.0.1:5001/check-face', ['functionID' => 2, 'subscribers' => $subscribers]);


            $data = $response->json();




            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Python server error',
                    'response' => $response->body()
                ], 500);
            }


            $sub = Subscriber::select('id', 'name')->where('id', $data['subscriber_id'])->first();

            $today = now()->toDateString();



            // $hasActiveSubscription = Subscription::where('subscriber_id', $sub->id)
            // ->where('start_date', '<=', $today)
            // ->where('end_date', '>=', $today)
            // ->exists();

            // this method is more faster 

            
            $hasActiveSubscription = \DB::table('subscriptions')
                ->where('subscriber_id', $sub->id)
                ->where('start_date', '<=', $today)
                ->where('end_date', '>=', $today)
                ->exists();

            CheckIn::create([
                'subscriber_id' => $sub->id,
                'check_in_date' => Carbon::today()->toDateString(),
                'is_allow' => $hasActiveSubscription
            ]);


            return response()->json([
                'success' => true,
            ], 200);

        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }



    }
    public function getVector($file)
    {
        $response = Http::attach(
            'image',
            file_get_contents($file->getRealPath()),
            $file->getClientOriginalName()
        )->post('http://127.0.0.1:5001/check-face', [
                    'functionID' => 3
                ]);
        if ($response->json(['success'])) {

            return $response->json(['vector']);
        }
        throw new \Exception("no vector");
    }

    public function getSubscribers()
    {
        $subscribers = Subscriber::select(
            'id',
            'vector'
        )->get();

        return response()->json($subscribers);
    }















    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $search = $request->query('search');
        if ($search) {

            $subscribers = Subscriber::where(function ($query) use ($search) {
                    $query->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('phone', 'LIKE', "%{$search}%");
                })
                ->get();

            return response()->json($subscribers);
        } else {

            $subscribers = Subscriber::select("id", "name", "phone")->orderBy("id", "desc")->get();

            return view('Subscribers.Index', compact("subscribers"));
        }

    }





    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('Subscribers.Create');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'face_image' => 'required',
        ]);


        $image = $request->file('face_image');
        $vector = $this->getVector($image);
        $s = Subscriber::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'vector' => $vector
        ]);

        $imgName = $s->id . '.' . 'jpg';

        $image->storeAs('Subscribers', $imgName, 'public');
        return redirect('/subscribers');
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $s = Subscriber::findOrFail($id);

        return view('Subscribers.Show', compact('s'));
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $subscriber = Subscriber::findOrFail($id);
        return view('Subscribers.Edit', compact('subscriber'));
    }
    /**
     * Update the specified resource in storage.
     */
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'face_image' => 'nullable'
        ]);

        $subscriber = Subscriber::findOrFail($id);

        $updateData = [
            'name' => $request->name,
            'phone' => $request->phone,
        ];

        if ($request->hasFile('face_image')) {
            $image = $request->file('face_image');

            $vector = $this->getVector($image);
            $updateData['vector'] = $vector;

            $imgName = $subscriber->id . '.jpg';
            $image->storeAs('Subscribers', $imgName, 'public');
        }

        $subscriber->update($updateData);

        return view('Success', ['path' => '/subscribers']);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //

        try {

            $s = Subscriber::findOrFail($id);


            $s->delete();
            // $s->update([
            //     'isDeleted' => true
            // ]);

            SessionReportController::updateBalance();
            return response()->json([
                'status' => true,
                'message' => 'Subscriber deleted successfully'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
