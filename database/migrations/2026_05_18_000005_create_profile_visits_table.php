<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profile_visits', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('visitor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUlid('visited_user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('visited_at')->useCurrent();

            $table->index(['visited_user_id', 'visited_at']);
            $table->index(['visitor_id', 'visited_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profile_visits');
    }
};
