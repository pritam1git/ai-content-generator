<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class OpenAIService
{
    protected ?string $apiKey = null;

    protected string $baseUrl = 'https://api.groq.com/openai/v1';

    protected string $model;

    public bool $demoMode = false;

    public function __construct()
    {
        $this->apiKey = config('services.groq.api_key');
        $this->model = config('services.groq.model');

        if (empty($this->apiKey)) {
            throw new Exception('GROQ_API_KEY is missing.');
        }
    }

    /**
     * Main chat function
     */
    public function chat(
        array $messages,
        float $temperature = 0.7
    ): string {

        // API key nahi hai
        if (empty($this->apiKey)) {
            return $this->demoResponse(
                $messages[count($messages)-1]['content'] ?? ''
            );
        }

        try {

            $response = Http::withToken($this->apiKey)
                ->timeout(120)
                ->post(
                    "{$this->baseUrl}/chat/completions",
                    [
                        'model' => $this->model,
                        'messages' => $messages,
                        'temperature' => $temperature,
                    ]
                );

            // Billing/API issue
            if ($response->failed()) {

                Log::error('OpenAI API Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return $this->demoResponse(
                    $messages[count($messages)-1]['content'] ?? ''
                );
            }

            $data = $response->json();

            return
                $data['choices'][0]['message']['content']
                ?? $this->demoResponse(
                    $messages[count($messages)-1]['content'] ?? ''
                );

        } catch (Exception $e) {

            Log::error(
                'OpenAIService Exception: ' .
                $e->getMessage()
            );

            return $this->demoResponse(
                $messages[count($messages)-1]['content'] ?? ''
            );
        }
    }

    /**
     * Generate helper
     */
    public function generate(
        string $prompt,
        string $systemInstruction = 'You are a helpful assistant.',
        float $temperature = 0.7
    ): string {

        return $this->chat([
            [
                'role' => 'system',
                'content' => $systemInstruction,
            ],
            [
                'role' => 'user',
                'content' => $prompt,
            ],
        ], $temperature);
    }

    /**
     * Test API
     */
    public function test(): array
    {
        if (!$this->apiKey) {
            return [
                'success' => false,
                'message' => 'Demo mode active'
            ];
        }

        try {

            $response = Http::withToken($this->apiKey)
                ->get("{$this->baseUrl}/models");

            return [
                'status' => $response->status(),
                'success' => $response->successful(),
            ];

        } catch (Exception $e) {

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * DEMO AI ENGINE
     */
    private function demoResponse(string $prompt): string
    {
        $prompt = strtolower(trim($prompt));

        // Greeting
        if (
            str_contains($prompt,'hi') ||
            str_contains($prompt,'hello') ||
            str_contains($prompt,'hey')
        ) {

            return "
        👋 Hello! I am your AI Assistant.

            I can help you with:

            • Laravel Development
            • PHP Programming
            • MySQL
            • JavaScript
            • API Integration
            • Blog Writing
            • Product Description
            • SEO Content
            • AI Content Generation

            How can I help you today?";
                    }

                    // Laravel
                    if (str_contains($prompt,'laravel')) {

                        return "
            Laravel is a powerful PHP framework built on MVC architecture.

            Features:

            ✓ Routing
            ✓ Middleware
            ✓ Authentication
            ✓ Authorization
            ✓ Blade Templates
            ✓ Eloquent ORM
            ✓ Queue System
            ✓ Events
            ✓ Notifications
            ✓ Cache
            ✓ API Development
            ✓ Service Container

            Laravel is used for SaaS, ERP, CRM, eCommerce and enterprise applications.";
                    }

                    // PHP
                    if (str_contains($prompt,'php')) {

                        return "
            PHP is a server-side scripting language.

            Advantages:

            • Fast development
            • Open source
            • OOP support
            • Huge ecosystem
            • Framework support
            • Database integration

            Popular Frameworks:

            ✓ Laravel
            ✓ Symfony
            ✓ CodeIgniter
            ✓ CakePHP";
                    }

                    // MySQL
                    if (str_contains($prompt,'mysql')) {

                        return "
            MySQL is one of the most popular relational databases.

            Important concepts:

            ✓ SELECT
            ✓ INSERT
            ✓ UPDATE
            ✓ DELETE
            ✓ JOIN
            ✓ GROUP BY
            ✓ ORDER BY
            ✓ INDEXING
            ✓ OPTIMIZATION";
                    }

                    // JS
                    if (str_contains($prompt,'javascript')) {

                        return "
            JavaScript powers modern websites.

            Topics:

            ✓ DOM Manipulation
            ✓ Events
            ✓ AJAX
            ✓ Fetch API
            ✓ Promises
            ✓ Async/Await
            ✓ ES6
            ✓ API Integration";
                    }

                    // Blog
                    if (
                        str_contains($prompt,'blog') ||
                        str_contains($prompt,'article')
                    ) {

                        return "
            # The Future of Artificial Intelligence

            Artificial Intelligence is transforming businesses across the globe.

            Benefits:

            • Process automation
            • Better decision making
            • Improved productivity
            • Enhanced customer experience
            • Predictive analytics

            Organizations adopting AI technologies are expected to gain significant competitive advantages in the coming years.";
                    }

                    // Product description
                    if (
                        str_contains($prompt,'product') ||
                        str_contains($prompt,'description')
                    ) {

                        return "
            Premium Wireless Headphones

            Experience exceptional sound quality with our latest wireless headphones.

            Features:

            ✓ Active Noise Cancellation
            ✓ Bluetooth 5.3
            ✓ 40 Hour Battery
            ✓ Fast Charging
            ✓ Comfortable Ear Cushions
            ✓ Crystal Clear Audio

            Perfect for music, gaming and professional use.";
                    }

                    // SEO
                    if (
                        str_contains($prompt,'seo') ||
                        str_contains($prompt,'content')
                    ) {

                        return "
            SEO Best Practices:

            ✓ Keyword Research
            ✓ Meta Title
            ✓ Meta Description
            ✓ Sitemap
            ✓ Robots.txt
            ✓ Internal Linking
            ✓ Schema Markup
            ✓ Mobile Optimization
            ✓ Page Speed Optimization";
                    }

                    // AI
                    if (
                        str_contains($prompt,'ai') ||
                        str_contains($prompt,'chatgpt')
                    ) {

                        return "
            Artificial Intelligence enables machines to perform tasks requiring human intelligence.

            Popular technologies:

            ✓ Machine Learning
            ✓ Deep Learning
            ✓ NLP
            ✓ Computer Vision
            ✓ Generative AI
            ✓ LLM Models

            Popular platforms:

            • ChatGPT
            • Claude
            • Gemini
            • Grok";
                    }

                    // CouponMister
                    if (
                        str_contains($prompt,'coupon')
                    ) {

                        return "
            CouponMister is a Laravel-based coupon discovery platform.

            Features:

            ✓ Elasticsearch Search
            ✓ AWS S3 Storage
            ✓ OpenAdmin Dashboard
            ✓ SEO Optimization
            ✓ Brand Pages
            ✓ Category Pages
            ✓ Coupon Tracking
            ✓ Responsive Design
            ✓ High Performance Search";
                    }

                    // Interview
                    if (
                        str_contains($prompt,'interview')
                    ) {

                        return "
            Top Laravel Interview Questions:

            1. Explain Service Container.
            2. What are Service Providers?
            3. Explain Middleware.
            4. What is Dependency Injection?
            5. Explain Eloquent ORM.
            6. Explain Queues.
            7. Explain Events.
            8. Explain Jobs.
            9. Explain Caching.
            10. Explain Repository Pattern.";
        }

        // Random fallback
        $responses = [

            "Thank you for your question. This response was generated by the demo AI engine.",

            "The AI assistant successfully processed your request and generated a contextual response.",

            "This Laravel project demonstrates AI integration architecture using service classes.",

            "The system automatically switched to demo mode because API billing is unavailable.",

            "This is a portfolio demonstration response generated by the internal AI engine."
        ];

        return $responses[array_rand($responses)];
    }
}