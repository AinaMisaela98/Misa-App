<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    // =========================
    // USERS LIST
    // =========================
    public function users()
{
    $authId = Auth::id();

    if (!$authId) {
        return collect(); // 🔥 raha tsy login dia tsy misy users
    }

    return User::query()
        ->where('id', '!=', $authId) // 🔥 hide auth user
        ->select('id', 'name')
        ->orderBy('name')
        ->get();
}

    // =========================
    // GET MESSAGES
    // =========================
    public function messages($id)
    {
        return Message::where(function ($q) use ($id) {
                $q->where('sender_id', Auth::id())
                  ->where('receiver_id', $id);
            })
            ->orWhere(function ($q) use ($id) {
                $q->where('sender_id', $id)
                  ->where('receiver_id', Auth::id());
            })
            ->orderBy('created_at')
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'message' => $msg->message,
                    'sender_id' => $msg->sender_id,
                    'receiver_id' => $msg->receiver_id,
                    'is_me' => $msg->sender_id === Auth::id(),
                    'created_at' => $msg->created_at->format('H:i'),
                ];
            });
    }

    // =========================
    // SEND MESSAGE
    // =========================
    public function send(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string'
        ]);

        $msg = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        return [
            'id' => $msg->id,
            'message' => $msg->message,
            'sender_id' => $msg->sender_id,
            'receiver_id' => $msg->receiver_id,
            'is_me' => true,
            'created_at' => $msg->created_at->format('H:i'),
        ];
    }

    // =========================
    // UPDATE MESSAGE
    // =========================
    public function updateMessage(Request $request, $id)
{
    $request->validate([
        'message' => 'required|string'
    ]);

    $msg = Message::where('id', $id)
        ->where('sender_id', Auth::id()) // 🔥 IMPORTANT SECURITY RULE
        ->first();

    if (!$msg) {
        return response()->json([
            'error' => 'Unauthorized'
        ], 403);
    }

    $msg->update([
        'message' => $request->message
    ]);

    return [
        'id' => $msg->id,
        'message' => $msg->message
    ];
}

    // =========================
    // DELETE MESSAGE
    // =========================
    public function deleteMessage($id)
{
    $msg = Message::where('id', $id)
        ->where('sender_id', Auth::id()) // 🔥 ONLY sender
        ->first();

    if (!$msg) {
        return response()->json([
            'error' => 'Unauthorized'
        ], 403);
    }

    $msg->delete();

    return response()->json(['success' => true]);
}
    // =========================
    // DELETE CONVERSATION
    // =========================
    public function deleteConversation($userId)
{
    Message::where(function ($q) use ($userId) {
        $q->where('sender_id', Auth::id())
          ->where('receiver_id', $userId);
    })
    ->delete();

    return response()->json(['success' => true]);
}
}