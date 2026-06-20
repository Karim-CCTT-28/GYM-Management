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





            $user = User::where('id', session("user_id"))->first();
            $user_vector = $user->vector;
            // Api py here
            $response = Http::attach(
                'image',
                file_get_contents($request->file('image')),
                'face.jpg'
            )->post('http://127.0.0.1:5001/check-face', ['functionID' => 1, 'user_vector' => json_encode($user_vector)]);

            $request->file('image')->move(public_path('uploads'), 'temp.jpg');


            if (!$response->json()['success']) {
                return response()->json(['message' => $response->json()['message']], 400);
            }

            $same_person = $response->json()['same_person'];
            session([

                'user_name' => $user->user_name
            ]);
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

        return view('Users.Create');
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {

        try {


            $request->validate([
                'user_name' => 'required',
                'password' => 'required',
                'vector' => 'required'
            ]);

            User::create([
                'user_name' => $request->user_name,
                'hashed_password' => bcrypt($request->password),
                'role' => 'E',
                'vector' => json_decode($request->vector, true)
            ]);



            return response()->json([
                'success' => true,
                'message' => 'User created successfully'
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
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
        $user = User::findOrFail($id);

        return view('Users.Edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'user_name' => 'required',
            'password' => 'required'
        ]);

        $user = User::findOrFail($id);

        $user->update([
            'user_name' => $request->user_name,
            'password' => $request->password,
        ]);

        return redirect('/employees');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = User::findOrFail($id);

            $user->update([
                'isDeleted' => true
            ]);

            return response()->json(['user'=>$user,'message' => 'User Deleted successfully']);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }



    public function getEmployees()
    {
        $emps = User::select()->where('role', 'E')
        ->where('isDeleted' , false)->get();


        return view('Users.Employees', ['emps' => $emps]);
    }
}
