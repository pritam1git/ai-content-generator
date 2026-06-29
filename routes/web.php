<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\BlogGeneratorController;
use App\Http\Controllers\ProductDescriptionController;
use App\Http\Controllers\PromptHistoryController;
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

require __DIR__.'/auth.php';
