<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
class UserController extends Controller
{


    public function checkFace(Request $request)
    {
        $request->validate([
            'user' => 'required',
            'image' => 'required|image'
        ]);


        $imagePath = $request->file('image')->store('faces', 'public');

       
        // Api py here
        $response = Http::attach(
            'image',
            file_get_contents($request->file('image')),
            $request->file('image')->getClientOriginalName()
            )->post('http://127.0.0.1:5000/check-face');
            
            $matched = true;

        return response()->json([
            'success' => true,
            'matched' => $matched,
            'image' => $imagePath
        ]);

    }






    public function login(Request $request)
    {

        Log::info('LOGIN REQUEST:', $request->all());
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

        return response()->json([
            'success' => true,
            'message' => 'Login success'
        ]);
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
