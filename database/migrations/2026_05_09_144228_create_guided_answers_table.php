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
        Schema::create('guided_answers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('conversation_id')
                ->constrained()->cascadeOnDelete();

            $table->foreignId('question_id')
                ->constrained('guided_questions')->restrictOnDelete();

            $table->foreignId('user_id')
                ->constrained()->cascadeOnDelete();

            $table->text('answer')->comment('Min 20 chars enforced at API level');

            // Flagging / moderation
            $table->boolean('is_flagged')->default(false);
            $table->text('flag_reason')->nullable();

            $table->timestamps();

            // One answer per (conversation, question, user)
            $table->unique(
                ['conversation_id', 'question_id', 'user_id'],
                'uq_guided_answer'
            );

            $table->index(['conversation_id', 'question_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('guided_answers');
    }
};
