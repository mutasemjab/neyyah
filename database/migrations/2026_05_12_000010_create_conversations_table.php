<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user1_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUlid('user2_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUlid('request_id')->nullable()->unique()->constrained('marriage_requests')->nullOnDelete();
            $table->string('stage', 20)->default('taaaruf');
            $table->tinyInteger('questions_completed_u1')->default(0);
            $table->tinyInteger('questions_completed_u2')->default(0);
            $table->boolean('is_chat_unlocked')->default(false);
            $table->string('firebase_channel_id', 200)->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->unique(['user1_id', 'user2_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
