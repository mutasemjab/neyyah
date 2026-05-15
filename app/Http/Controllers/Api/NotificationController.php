<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\AppNotificationResource;
use App\Models\AppNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends ApiController
{
    /**
     * Get paginated notifications for authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $user          = $request->user();
        $notifications = AppNotification::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(20);

        $unreadCount = AppNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        return $this->success([
            'data'         => AppNotificationResource::collection($notifications->items()),
            'total'        => $notifications->total(),
            'per_page'     => $notifications->perPage(),
            'current_page' => $notifications->currentPage(),
            'last_page'    => $notifications->lastPage(),
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markRead(Request $request, string $id): JsonResponse
    {
        $user         = $request->user();
        $notification = AppNotification::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$notification) {
            return $this->error('الإشعار غير موجود.', 404);
        }

        $notification->update(['is_read' => true]);

        return $this->success([], 'تم تمييز الإشعار كمقروء.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllRead(Request $request): JsonResponse
    {
        $user = $request->user();

        AppNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return $this->success([], 'تم تمييز جميع الإشعارات كمقروءة.');
    }
}
