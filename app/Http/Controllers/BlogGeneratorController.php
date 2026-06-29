<?php

namespace App\Http\Controllers;

use App\Models\PromptHistory;
use App\Services\OpenAIService;
use Illuminate\Http\Request;

class BlogGeneratorController extends Controller
{
    protected OpenAIService $openAI;

    public function __construct(OpenAIService $openAI)
    {
        $this->openAI = $openAI;
    }

    public function index(Request $request)
    {
        $histories = PromptHistory::where('user_id', $request->user()->id)
            ->where('type', 'blog')
            ->latest()
            ->take(10)
            ->get();

        return view('blog.index', compact('histories'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'topic' => 'required|string|max:255',
            'tone' => 'required|string',
            'word_count' => 'required|integer|min:100|max:2000',
        ]);

        try {
            $prompt = "Write a blog post on the topic: \"{$request->topic}\".
Tone: {$request->tone}.
Approximate length: {$request->word_count} words.
Include a catchy title, introduction, well-structured body with subheadings, and a conclusion.
Format the output in Markdown.";

            $result = $this->openAI->generate(
                $prompt,
                'You are a professional content writer and SEO blog expert.',
                0.8
            );

            PromptHistory::create([
                'user_id' => $request->user()->id,
                'type' => 'blog',
                'prompt' => $request->topic,
                'response' => $result,
            ]);

            return response()->json([
                'success' => true,
                'result' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
