<?php

namespace App\Http\Controllers;

use App\Models\PromptHistory;
use App\Services\OpenAIService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    protected OpenAIService $openAI;

    public function __construct(OpenAIService $openAI)
    {
        $this->openAI = $openAI;
    }

    public function index(Request $request)
    {
        $histories = PromptHistory::where('user_id', $request->user()->id)
            ->where('type', 'chat')
            ->latest()
            ->take(20)
            ->get()
            ->reverse(); // oldest to newest for chat display

        return view('chat.index', compact('histories'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:3000',
        ]);

        try {
            $reply = $this->openAI->generate(
                $request->message,
                'You are a helpful, friendly AI assistant. Give clear and concise answers.'
            );

            PromptHistory::create([
                'user_id' => $request->user()->id,
                'type' => 'chat',
                'prompt' => $request->message,
                'response' => $reply,
            ]);

            return response()->json([
                'success' => true,
                'reply' => $reply,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
