<?php

namespace App\Console\Commands\Conversations;

use App\Models\Conversation;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class ExpireChats extends Command
{
    protected $signature = 'conversations:expire';

    protected $description = 'Expire unlocked chats that have passed their expiry time.';

    public function __construct(private NotificationService $notificationService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Expiring chats...');

        $expiredConversations = Conversation::where('expires_at', '<=', now())
            ->where('is_chat_unlocked', true)
            ->get();

        foreach ($expiredConversations as $conversation) {
            $conversation->update(['is_chat_unlocked' => false]);

            // Notify both users
            $user1 = User::find($conversation->user1_id);
            $user2 = User::find($conversation->user2_id);

            $message = 'انتهت مدة المحادثة. يمكنكما تجديدها للاستمرار.';

            if ($user1) {
                $this->notificationService->send(
                    $user1,
                    'chat_expired',
                    'انتهت مدة المحادثة',
                    $message,
                    ['conversation_id' => $conversation->id]
                );
            }

            if ($user2) {
                $this->notificationService->send(
                    $user2,
                    'chat_expired',
                    'انتهت مدة المحادثة',
                    $message,
                    ['conversation_id' => $conversation->id]
                );
            }
        }

        $this->info("Expired {$expiredConversations->count()} conversations.");

        return Command::SUCCESS;
    }
}
