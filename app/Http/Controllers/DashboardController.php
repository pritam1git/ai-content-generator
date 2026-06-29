<?php

namespace App\Http\Controllers;

use App\Models\PromptHistory;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $totalPrompts = PromptHistory::where('user_id', $userId)->count();
        $chatCount = PromptHistory::where('user_id', $userId)->where('type', 'chat')->count();
        $blogCount = PromptHistory::where('user_id', $userId)->where('type', 'blog')->count();
        $productCount = PromptHistory::where('user_id', $userId)->where('type', 'product')->count();

        $recentHistory = PromptHistory::where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalPrompts',
            'chatCount',
            'blogCount',
            'productCount',
            'recentHistory'
        ));
    }
}
