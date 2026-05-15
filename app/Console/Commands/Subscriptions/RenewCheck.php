<?php

namespace App\Console\Commands\Subscriptions;

use App\Models\Subscription;
use Illuminate\Console\Command;

class RenewCheck extends Command
{
    protected $signature = 'subscriptions:renew-check';

    protected $description = 'Deactivate expired subscriptions.';

    public function handle(): int
    {
        $this->info('Checking for expired subscriptions...');

        $count = Subscription::where('ends_at', '<=', now())
            ->where('is_active', true)
            ->update(['is_active' => false]);

        $this->info("Deactivated {$count} expired subscription(s).");

        return Command::SUCCESS;
    }
}
