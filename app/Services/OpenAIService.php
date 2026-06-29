<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class OpenAIService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://api.openai.com/v1';
    protected string $model = 'gpt-4o-mini';

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
    }

    /**
     * General purpose chat completion call.
     *
     * @param array $messages Array of ['role' => 'user|system|assistant', 'content' => '...']
     * @param float $temperature
     * @return string
     */
    public function chat(array $messages, float $temperature = 0.7): string
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(60)
                ->post("{$this->baseUrl}/chat/completions", [
                    'model' => $this->model,
                    'messages' => $messages,
                    'temperature' => $temperature,
                ]);

            if ($response->failed()) {
                Log::error('OpenAI API Error: ' . $response->body());
                throw new Exception('OpenAI API request failed: ' . $response->status());
            }

            $data = $response->json();

            return $data['choices'][0]['message']['content'] ?? 'No response generated.';
        } catch (Exception $e) {
            Log::error('OpenAIService Exception: ' . $e->getMessage());
            throw new Exception('Something went wrong while contacting OpenAI: ' . $e->getMessage());
        }
    }

    /**
     * Helper: simple single prompt with optional system instruction.
     */
    public function generate(string $prompt, string $systemInstruction = 'You are a helpful assistant.', float $temperature = 0.7): string
    {
        return $this->chat([
            ['role' => 'system', 'content' => $systemInstruction],
            ['role' => 'user', 'content' => $prompt],
        ], $temperature);
    }
}
