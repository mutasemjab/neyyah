<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("UPDATE users SET display_name = 'مستخدم' WHERE display_name IS NULL OR display_name = ''");
    }

    public function down(): void
    {
        // Intentionally irreversible — we don't know which rows were originally null/empty
    }
};
