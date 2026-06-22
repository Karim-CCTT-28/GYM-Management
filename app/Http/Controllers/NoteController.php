<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class NoteController extends Controller implements HasMiddleware
{





    public static function middleware(): array
    {

        return [
            new Middleware('employee', only: [
                'index',
                'store',
                'show',
                'update',
                'destroy'
            ])

            ,
            new Middleware('admin', only: [
                'readAll'
            ])

        ];
    }








    public function readAll()
    {

        // it's used by admin only
        $notes = Note::with('user')->get();

        // return response()->json($notes);
        return view('ReadNotes', ['notes' => $notes]);
    }
    public function index()
    {

        // show employee's notes
        $notes = Note::where('user_id', session('user_id'))
            ->whereDate('created_at', Carbon::today())
            ->get();


        // return response()->json(['n' => $notes]);
        return view('Notes', compact('notes'));
    }

    public function store(Request $request)
    {
        try {


            $request->validate([
                'note' => 'required'
            ]);

            $note = Note::create([
                'user_id' => session('user_id'),
                'note' => $request->note
            ]);

            return response()->json([
                'status' => true,
                'data' => $note
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'data' => $e->getMessage()
            ]);
        }
    }

    public function show($id)
    {
        $note = Note::where('id', $id)
            ->where('user_id', session('user_id'))
            ->whereDate('created_at', Carbon::today())
            ->firstOrFail();

        return response()->json($note);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'note' => 'required'
        ]);

        $note = Note::where('id', $id)
            ->where('user_id', session('user_id'))
            ->whereDate('created_at', Carbon::today())
            ->firstOrFail();

        $note->update([
            'note' => $request->note
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Updated'
        ]);
    }

    public function destroy($id)
    {
        $note = Note::where('id', $id)
            ->where('user_id', session('user_id'))
            ->whereDate('created_at', Carbon::today())
            ->firstOrFail();


        $note->delete();
        // $note->update([
        //     'isDeleted' => true
        // ]);

        return response()->json([
            'status' => true,
            'message' => 'Deleted'
        ]);
    }
}