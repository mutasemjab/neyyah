<?php

namespace App\Services;

use App\Models\CoinTransaction;
use App\Models\CoinsWallet;
use App\Models\User;

class CoinService
{
    /**
     * Deduct coins from user wallet. Returns false if insufficient balance.
     */
    public function spend(User $user, int $amount, string $description): bool
    {
        $wallet = $user->wallet;

        if (!$wallet || $wallet->balance < $amount) {
            return false;
        }

        $newBalance  = $wallet->balance - $amount;
        $dailyUsed   = $wallet->daily_free_used ?? 0;
        $dailyRemaining = max(0, 5 - $dailyUsed);
        $fromFree    = min($amount, $dailyRemaining);

        $wallet->update([
            'balance'         => $newBalance,
            'daily_free_used' => $dailyUsed + $fromFree,
        ]);

        CoinTransaction::create([
            'user_id'        => $user->id,
            'amount'         => -$amount,
            'type'           => 'spend',
            'description_ar' => $description,
            'balance_after'  => $newBalance,
        ]);

        return true;
    }

    /**
     * Add coins to user wallet.
     */
    public function earn(User $user, int $amount, string $type, string $description): void
    {
        $wallet = $user->wallet;

        if (!$wallet) {
            $wallet = CoinsWallet::create(['user_id' => $user->id, 'balance' => 0]);
        }

        $newBalance = $wallet->balance + $amount;

        $wallet->update(['balance' => $newBalance]);

        CoinTransaction::create([
            'user_id'        => $user->id,
            'amount'         => $amount,
            'type'           => $type,
            'description_ar' => $description,
            'balance_after'  => $newBalance,
        ]);
    }

    /**
     * Reset daily free coins counter.
     */
    public function resetDailyFree(User $user): void
    {
        $wallet = $user->wallet;

        if ($wallet) {
            $wallet->update([
                'daily_free_used'     => 0,
                'daily_free_reset_at' => now()->addDay()->startOfDay(),
            ]);
        }
    }

    /**
     * Claim daily free coins (5 coins per day).
     * Returns false if already claimed today.
     */
    public function claimDailyFree(User $user): bool
    {
        $wallet = $user->wallet;

        if (!$wallet) {
            $wallet = CoinsWallet::create(['user_id' => $user->id, 'balance' => 0]);
        }

        // Check if reset time has passed or not set
        $canClaim = !$wallet->daily_free_reset_at || now()->gte($wallet->daily_free_reset_at);

        if (!$canClaim) {
            return false;
        }

        $amount     = 5;
        $newBalance = $wallet->balance + $amount;

        $wallet->update([
            'balance'             => $newBalance,
            'daily_free_used'     => 0,
            'daily_free_reset_at' => now()->addDay()->startOfDay(),
        ]);

        CoinTransaction::create([
            'user_id'        => $user->id,
            'amount'         => $amount,
            'type'           => 'daily_free',
            'description_ar' => 'عملات مجانية يومية',
            'balance_after'  => $newBalance,
        ]);

        return true;
    }
}
