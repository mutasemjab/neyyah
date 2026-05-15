<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conversations', function (Blueprint $table) {
             $table->id();

            // Always store smaller ID first to avoid duplicates
            $table->foreignId('user_a_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('user_b_id')->constrained('users')->cascadeOnDelete();

            // Source request
            $table->foreignId('match_request_id')
                ->nullable()->constrained('match_requests')->nullOnDelete();

            // Stage of the relationship
            $table->enum('stage', [
                'taaaruf',      // تعارف (1) — guided questions phase
                'ihtimam',      // اهتمام (2) — free chat unlocked
                'jiddiyya',     // جدية (3)
                'family',       // تدخل الأهل (4)
                'khitba',       // خطبة (5)
            ])->default('taaaruf');

            // Guided questions progress
            $table->unsignedTinyInteger('questions_completed_a')->default(0);
            $table->unsignedTinyInteger('questions_completed_b')->default(0);
            $table->unsignedTinyInteger('total_questions')->default(5);

            // Free chat is unlocked when BOTH have answered all questions
            $table->boolean('is_chat_unlocked')->default(false);
            $table->timestamp('chat_unlocked_at')->nullable();

            // Chat expiry (14 days from unlock, can be renewed)
            $table->timestamp('chat_expires_at')->nullable();

            // Last activity for sorting conversations list
            $table->timestamp('last_activity_at')->nullable();

            // Unread counters per user
            $table->unsignedInteger('unread_count_a')->default(0);
            $table->unsignedInteger('unread_count_b')->default(0);

            $table->enum('status', [
                'active',
                'paused',       // User paused the conversation
                'ended',        // Either party ended it
                'archived',     // Admin archived
            ])->default('active');

            $table->timestamps();
            $table->softDeletes();

            // Only one conversation per pair
            $table->unique(['user_a_id', 'user_b_id'], 'uq_conversation_pair');

            $table->index('last_activity_at');
            $table->index(['user_a_id', 'status']);
            $table->index(['user_b_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conversations');
    }
};
