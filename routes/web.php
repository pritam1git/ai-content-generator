<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\BlogGeneratorController;
use App\Http\Controllers\ProductDescriptionController;
use App\Http\Controllers\PromptHistoryController;
use App\Services\OpenAIService;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile (Breeze default)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // AI Chat Assistant
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/send', [ChatController::class, 'send'])->name('chat.send');

    // Blog Generator
    Route::get('/blog-generator', [BlogGeneratorController::class, 'index'])->name('blog.index');
    Route::post('/blog-generator/generate', [BlogGeneratorController::class, 'generate'])->name('blog.generate');

    // Product Description Generator
    Route::get('/product-generator', [ProductDescriptionController::class, 'index'])->name('product.index');
    Route::post('/product-generator/generate', [ProductDescriptionController::class, 'generate'])->name('product.generate');

    // Prompt History
    Route::get('/history', [PromptHistoryController::class, 'index'])->name('history.index');
    Route::delete('/history/{history}', [PromptHistoryController::class, 'destroy'])->name('history.destroy');
});
Route::get('/test-openai', function (
    OpenAIService $openai
) {

    return response()->json(
        $openai->test()
    );
});
Route::get('/chat-test', function () {

    $response = Http::withToken(env('OPENAI_API_KEY'))
        ->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => 'Hello'
                ]
            ]
        ]);

    dd(
        $response->status(),
        $response->json()
    );
});

Route::get('/groq-test', function () {

    $response = \Illuminate\Support\Facades\Http::withToken(env('GROQ_API_KEY'))
        ->post('https://api.groq.com/openai/v1/chat/completions', [
            'model' => env('GROQ_MODEL'),
            'messages' => [
                [
                    'role' => 'user',
                    'content' => 'Hello, who are you?'
                ]
            ]
        ]);

    return response()->json($response->json());
});

require __DIR__.'/auth.php';
