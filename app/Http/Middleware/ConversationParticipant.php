<?php

namespace App\Http\Middleware;

use App\Models\Block;
use App\Models\Conversation;
use Closure;
use Illuminate\Http\Request;

class ConversationParticipant
{
    public function handle(Request $request, Closure $next)
    {
        $conversationId = $request->route('id');

        if (!$conversationId) {
            return $next($request);
        }

        $conversation = Conversation::find($conversationId);

        if (!$conversation) {
            return response()->json([
                'status'  => 'error',
                'message' => 'المحادثة غير موجودة.',
                'data'    => [],
            ], 404);
        }

        $userId = auth()->id();

        if ($conversation->user1_id !== $userId && $conversation->user2_id !== $userId) {
            return response()->json([
                'status'  => 'error',
                'message' => 'غير مصرح لك بالوصول إلى هذه المحادثة.',
                'data'    => [],
            ], 403);
        }

        // For write operations, reject if the partner has blocked the current user
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH'])) {
            $partnerId = $conversation->user1_id === $userId
                ? $conversation->user2_id
                : $conversation->user1_id;

            $isBlocked = Block::where('blocker_id', $partnerId)
                ->where('blocked_id', $userId)
                ->exists();

            if ($isBlocked) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'لا يمكنك إرسال رسائل في هذه المحادثة.',
                    'data'    => [],
                ], 403);
            }
        }

        return $next($request);
    }
}
