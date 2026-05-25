<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('UPDATE conversations SET expires_at = DATE_ADD(created_at, INTERVAL 14 DAY) WHERE expires_at IS NULL');
    }

    public function down(): void
    {
        // Irreversible backfill
    }
};
