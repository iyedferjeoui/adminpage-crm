<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function index()
    {
        return view('chat');
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
            'session_id' => ['required', 'string'],
        ]);

        $user = $request->user();

        $response = Http::timeout(60)
            ->withHeaders(['ngrok-skip-browser-warning' => 'true'])
            ->post(config('services.n8n.chat_webhook'), [
                'chatInput' => $validated['message'],
                'sessionId' => $validated['session_id'],
                'userId' => $user->id,
                'userName' => $user->name,
                'userEmail' => $user->email,
            ]);

        if ($response->failed()) {
            return response()->json([
                'error' => 'Failed to reach the assistant. Please try again.',
            ], 502);
        }

        $data = $response->json();

        return response()->json([
            'reply' => $data['output'] ?? $data['reply'] ?? $data[0]['output'] ?? 'No response received.',
        ]);
    }
}
