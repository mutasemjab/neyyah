<?php

namespace App\Http\Controllers\Api;

use App\Models\Block;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlockController extends ApiController
{
    /**
     * List users blocked by the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $blocks = Block::with('blocked')
            ->where('blocker_id', $request->user()->id)
            ->latest()
            ->get()
            ->map(fn (Block $b) => [
                'id'           => (string) $b->id,
                'blocked_user' => $b->blocked ? [
                    'id'           => $b->blocked->id,
                    'display_name' => $b->blocked->display_name ?: 'مستخدم',
                    'age'          => $b->blocked->birth_date
                        ? Carbon::parse($b->blocked->birth_date)->age
                        : null,
                ] : null,
                'created_at'   => $b->created_at?->toIso8601String(),
            ]);

        return $this->success($blocks);
    }

    /**
     * Block a user.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => ['required', 'string', 'exists:users,id'],
        ], [
            'user_id.exists' => 'المستخدم غير موجود.',
        ]);

        $auth   = $request->user();
        $userId = $request->user_id;

        if ($userId === $auth->id) {
            return $this->error('لا يمكنك حظر نفسك.', 422);
        }

        Block::firstOrCreate([
            'blocker_id' => $auth->id,
            'blocked_id' => $userId,
        ]);

        return $this->success(null, 'تم حظر المستخدم بنجاح.', 201);
    }

    /**
     * Unblock a user (idempotent).
     */
    public function destroy(Request $request, string $userId): JsonResponse
    {
        Block::where('blocker_id', $request->user()->id)
            ->where('blocked_id', $userId)
            ->delete();

        return $this->success([], 'تم إلغاء الحظر بنجاح.');
    }
}
