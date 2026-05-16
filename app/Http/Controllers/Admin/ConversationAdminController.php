<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Conversation;
use Illuminate\Http\Request;

class ConversationAdminController extends Controller
{
    /** @return Admin */
    private function admin(): Admin
    {
        /** @var Admin $admin */
        $admin = auth('admin')->user();
        return $admin;
    }

    public function index(Request $request)
    {
        if (!$this->admin()->can('conversations-index')) {
            return redirect()->back()->with('error', __('messages.Access Denied'));
        }

        $query = Conversation::with('user1', 'user2');

        if ($request->filled('stage')) {
            $query->where('stage', $request->stage);
        }

        if ($request->filled('chat_unlocked')) {
            $query->where('is_chat_unlocked', $request->chat_unlocked === '1');
        }

        $data        = $query->latest()->paginate(PAGINATION_COUNT);
        $stageFilter = $request->stage;

        return view('admin.conversations.index', compact('data', 'stageFilter'));
    }

    public function show(string $id)
    {
        if (!$this->admin()->can('conversations-index')) {
            return redirect()->back()->with('error', __('messages.Access Denied'));
        }

        $conversation = Conversation::with('user1', 'user2', 'request')->findOrFail($id);

        $answersCount = $conversation->answers()->count();

        return view('admin.conversations.show', compact('conversation', 'answersCount'));
    }
}
