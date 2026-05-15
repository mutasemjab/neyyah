<?php

namespace App\Http\Middleware;

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

        return $next($request);
    }
}
