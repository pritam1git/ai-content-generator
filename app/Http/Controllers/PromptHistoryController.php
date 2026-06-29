<?php

namespace App\Http\Controllers;

use App\Models\PromptHistory;
use Illuminate\Http\Request;

class PromptHistoryController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type'); // optional filter: chat, blog, product

        $query = PromptHistory::where('user_id', $request->user()->id)->latest();

        if ($type && in_array($type, ['chat', 'blog', 'product'])) {
            $query->where('type', $type);
        }

        $histories = $query->paginate(10);

        return view('history.index', compact('histories', 'type'));
    }

    public function destroy(Request $request, PromptHistory $history)
    {
        // Ensure user can only delete their own history
        if ($history->user_id !== $request->user()->id) {
            abort(403);
        }

        $history->delete();

        return redirect()->back()->with('success', 'History deleted successfully.');
    }
}
