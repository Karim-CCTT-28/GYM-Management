<?php

namespace App\Http\Controllers;
use Throwable;
use Carbon\Carbon;
use App\Models\User;
use App\Models\SessionReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
class UserController extends Controller
{


    public function checkFace(Request $request)
    {



        try {
            $request->validate([
                'image' => 'required|image'
            ]);



            $user = User::where('id', session('user_id'))->first();

            $user_vector = $user->vector;
            // Api py here
            $response = Http::attach(
                'image',
                file_get_contents($request->file('image')) , 'face.jpg'
            )->post('http://127.0.0.1:5001/check-face', ['functionID' => 1, 'user_vector' => json_encode($user_vector)]);
          
          
            
            if(!$response->json()['success']){
                return response()->json(['message' => $response->json()['message']], 400);
                }
            
            $same_person = $response->json()['same_person'];

            return response()->json([
                'success' => true,
                'same_person' => $same_person,
              
            ]);

        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }


    }






    public function login(Request $request)
    {

        try {


            $request->validate([
                'user' => 'required',
                'password' => 'required'
            ]);

            $user = User::where('user_name', $request->user)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ]);
            }

            if (!Hash::check($request->password, $user->hashed_password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Wrong password'
                ]);
            }

            $todaySession = SessionReport::where('user_id', $user->id)
                ->whereDate('created_date', Carbon::today()->toDateString())
                ->first();

            \Log::info("todaySession" . $todaySession);
            if (!$todaySession) {
                SessionReport::create([
                    'user_id' => $user->id,
                    'net_total' => 0,
                    'water_balance' => 0,
                    'session_start' => Carbon::now(),
                    'session_end' => Carbon::now(),
                    'created_date' => Carbon::today()->toDateString()
                ]);
            }
            session([

                'user_name' => $request->user,
                'user_id' => $user->id
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Login success'
            ]);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }






    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $users = User::select('user_name')->get();

        return response()->json($users);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $request->validate([
            'user_name' => 'required',
            'password' => 'required',
            'image' => 'required|image'
        ]);

        $user = User::create([
            'user_name' => $request->user_name,
            'hashed_password' => bcrypt($request->password),
            'Role' => 'E'
        ]);


        // make it as jpg
        $image = imagecreatefromstring(file_get_contents($request->file('image')));
        imagejpeg($image, storage_path("app/public/faces/{$user->id}.jpeg"));

        return response()->json([
            'success' => true,
            'message' => 'User created successfully'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
