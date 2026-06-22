<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubscriptionType;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
class SubscriptionTypesController extends Controller implements HasMiddleware
{
    //

 public static function middleware(): array
    {
        return [
            new Middleware(
                'employee',
                only: [
                    'index'
                ]
            ),

            new Middleware(
                'admin',
                only: [
                'destroy',
                'store'
                ]
            ),
        ];
    }
    public function index(Request $request)
    {
        $types = SubscriptionType::select('id', 'duration', 'price', 'duration_unit')->get();
        if ($request->has('q')) {
            return view("SubscriptionTypes.Index", ['types' => $types]);
        }

        return response()->json($types);
    }



    // public function edit(string $id)
    // {
    //     try {
    //         $type = SubscriptionType::findOrFail($id);

    //         return view('SubscriptionTypes.Edit', compact('type'));
    //     } catch (\Throwable $e) {
    //         return response()->json(['message', 'Subscription type not found']);
    //     }
    // }


    public function destroy(string $id)
    {
        try {
            $type = SubscriptionType::findOrFail($id);

            $type->delete();
            // $type->update([
            //     'isDeleted' => true
            // ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Subscription type deleted successfully!'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete: ' . $e->getMessage()
            ], 500);
        }
    }



    public function store(Request $request)
    {

        try {

            $request->validate([
                'duration' => 'required',
                'duration_unit' => 'required',
                'price' => 'required|'
            ]);


            SubscriptionType::create([
                'duration' => $request->duration,
                'duration_unit' => $request->duration_unit,
                'price' => $request->price
            ]);



            return view('Success' , ['path'=>'/subscription-types?q=1']);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);


        }

    }
}
