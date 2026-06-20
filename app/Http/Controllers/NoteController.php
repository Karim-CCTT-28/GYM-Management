<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function index()
    {
        $notes = Note::where('user_id', session('user_id'))
            ->whereDate('created_at', Carbon::today())
            ->get();

        // return response()->json(['n' => $notes]);
        return view('Notes', compact('notes'));
    }

    public function store(Request $request)
    {
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

        return response()->json([
            'status' => true,
            'message' => 'Deleted'
        ]);
    }
}