<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ConversationController extends Controller
{
    // public function index()
    // {
    //     $conversations = Conversation::all();
    //     return response()->json($conversations);
    // }

    public function index(Request $request)
    {
        // $conversations = Conversation::with(['messages', 'contact'])
        //     ->where('business_id', $request->user()->business_id)
        //     ->get();

        $conversations = Conversation::with(['messages', 'contact'])
            ->where('business_id', 1)
            ->get();

        return response()->json($conversations);
    }

    public function store(Request $request)
    {
        $request->validate([
            'contact_id' => 'required|exists:contacts,id',
            'business_id' => 'required|exists:businesses,id',
            'status' => 'required|string',
        ]);

        $conversation = Conversation::create([
            'contact_id' => $request->contact_id,
            'business_id' => $request->business_id, // Include business_id
            'start_time' => now(),
            'end_time' => now()->addHours(24),
            'status' => $request->status,
            'uuid' => (string) Str::uuid(),
        ]);

        return response()->json($conversation, 201);
    }

    public function show(Conversation $conversation)
    {
        return response()->json($conversation);
    }

    public function update(Request $request, Conversation $conversation)
    {
        $request->validate([
            'status' => 'sometimes|string',
            'end_time' => 'sometimes|date',
        ]);

        $conversation->update($request->only(['status', 'end_time']));
        return response()->json($conversation);
    }

    public function destroy(Conversation $conversation)
    {
        $conversation->delete();
        return response()->json(null, 204);
    }
}
