<?php

use App\Models\User;
use App\Services\MatchingService;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $service = app(MatchingService::class);

        User::chunk(100, function ($users) use ($service) {
            foreach ($users as $user) {
                $service->recalculateAndSave($user);
            }
        });
    }

    public function down(): void
    {
        // Irreversible data migration
    }
};
