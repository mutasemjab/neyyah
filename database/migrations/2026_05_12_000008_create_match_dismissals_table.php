<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('match_dismissals');

        Schema::create('match_dismissals', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUlid('dismissed_user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('dismissed_at')->useCurrent();

            $table->unique(['user_id', 'dismissed_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_dismissals');
    }
};
