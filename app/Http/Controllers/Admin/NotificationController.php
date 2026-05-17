<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminBroadcast;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    private function admin(): Admin
    {
        /** @var Admin $admin */
        $admin = auth('admin')->user();
        return $admin;
    }

    public function index()
    {
        $broadcasts = AdminBroadcast::latest()->paginate(10);

        return view('admin.notifications.index', compact('broadcasts'));
    }

    public function store(Request $request, NotificationService $notificationService)
    {
        $request->validate([
            'title_ar'   => 'required|string|max:255',
            'body_ar'    => 'required|string',
            'target'     => 'required|in:all,specific',
            'user_ids'   => 'required_if:target,specific|array|min:1',
            'user_ids.*' => 'exists:users,id',
        ]);

        if ($request->target === 'all') {
            $users = User::all();
        } else {
            $users = User::whereIn('id', $request->user_ids)->get();
        }

        foreach ($users as $user) {
            $notificationService->send(
                $user,
                'admin_broadcast',
                $request->title_ar,
                $request->body_ar,
            );
        }

        AdminBroadcast::create([
            'title_ar'   => $request->title_ar,
            'body_ar'    => $request->body_ar,
            'target'     => $request->target,
            'user_ids'   => $request->target === 'specific' ? $request->user_ids : null,
            'user_count' => $users->count(),
            'sent_by'    => $this->admin()->id,
        ]);

        return redirect()->route('admin.notifications.index')
            ->with('success', __('messages.Notification_Sent'));
    }

    public function searchUsers(Request $request)
    {
        $q = $request->get('q', '');

        $users = User::select('id', 'display_name', 'phone', 'gender', 'status')
            ->when($q, function ($query) use ($q) {
                $query->where('display_name', 'like', "%{$q}%")
                      ->orWhere('phone', 'like', "%{$q}%");
            })
            ->limit(50)
            ->get();

        return response()->json($users);
    }
}
