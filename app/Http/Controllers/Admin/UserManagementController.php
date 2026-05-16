<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
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
        if (!$this->admin()->can('users-index')) {
            return redirect()->back()->with('error', __('messages.Access Denied'));
        }

        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('phone', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $data         = $query->latest()->paginate(PAGINATION_COUNT);
        $searchQuery  = $request->search;
        $statusFilter = $request->status;

        return view('admin.users.index', compact('data', 'searchQuery', 'statusFilter'));
    }

    public function show(string $id)
    {
        if (!$this->admin()->can('users-index')) {
            return redirect()->back()->with('error', __('messages.Access Denied'));
        }

        $user = User::with([
            'profileImages', 'interests', 'intentCard',
            'privacySettings', 'identityVerification',
        ])->findOrFail($id);

        $sentCount     = $user->sentRequests()->count();
        $receivedCount = $user->receivedRequests()->count();
        $convoCount    = $user->allConversations()->count();
        $blocksCount   = $user->blocks()->count();
        $reportsCount  = $user->reports()->count();

        return view('admin.users.show', compact(
            'user', 'sentCount', 'receivedCount', 'convoCount', 'blocksCount', 'reportsCount'
        ));
    }

    public function updateStatus(Request $request, string $id)
    {
        if (!$this->admin()->can('users-edit')) {
            return redirect()->back()->with('error', __('messages.Access Denied'));
        }

        $request->validate([
            'status' => 'required|in:pending,active,suspended,banned',
        ]);

        User::findOrFail($id)->update(['status' => $request->status]);

        return redirect()->back()->with('success', __('messages.User_Status_Updated'));
    }
}
