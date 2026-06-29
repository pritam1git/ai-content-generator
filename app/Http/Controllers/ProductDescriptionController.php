<?php

namespace App\Http\Controllers;

use App\Models\PromptHistory;
use App\Services\OpenAIService;
use Illuminate\Http\Request;

class ProductDescriptionController extends Controller
{
    protected OpenAIService $openAI;

    public function __construct(OpenAIService $openAI)
    {
        $this->openAI = $openAI;
    }

    public function index(Request $request)
    {
        $histories = PromptHistory::where('user_id', $request->user()->id)
            ->where('type', 'product')
            ->latest()
            ->take(10)
            ->get();

        return view('product.index', compact('histories'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'features' => 'required|string|max:1000',
            'audience' => 'nullable|string|max:255',
        ]);

        try {
            $audience = $request->audience ?: 'general customers';

            $prompt = "Write a compelling, persuasive e-commerce product description for the product: \"{$request->product_name}\".
Key features/details: {$request->features}.
Target audience: {$audience}.
Make it engaging, highlight benefits (not just features), and end with a short call-to-action.
Keep it between 100-200 words.";

            $result = $this->openAI->generate(
                $prompt,
                'You are an expert e-commerce copywriter who writes high-converting product descriptions.',
                0.75
            );

            PromptHistory::create([
                'user_id' => $request->user()->id,
                'type' => 'product',
                'prompt' => $request->product_name,
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
