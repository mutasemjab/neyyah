<?php

namespace App\Console\Commands\Coins;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class ResetDailyCoins extends Command
{
    protected $signature = 'coins:reset-daily';

    protected $description = 'Reset daily free coins for all users and send FCM notification.';

    public function __construct(private NotificationService $notificationService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Resetting daily free coins for all users...');

        User::query()->chunk(500, function ($users) {
            $users->each(function (User $user) {
                $user->wallet?->update([
                    'daily_free_used'     => 0,
                    'daily_free_reset_at' => now()->addDay()->startOfDay(),
                ]);

                $this->notificationService->send(
                    $user,
                    'daily_coins_ready',
                    'عملاتك المجانية جاهزة!',
                    'يمكنك الآن المطالبة بعملاتك المجانية اليومية.',
                    ['type' => 'daily_coins']
                );
            });
        });

        $this->info('Daily coins reset completed.');

        return Command::SUCCESS;
    }
}
