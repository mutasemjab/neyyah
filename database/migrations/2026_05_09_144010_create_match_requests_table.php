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
        Schema::create('match_requests', function (Blueprint $table) {
             $table->id();

            $table->foreignId('from_user_id')
                ->constrained('users')->cascadeOnDelete();

            $table->foreignId('to_user_id')
                ->constrained('users')->cascadeOnDelete();

            // The 3-step form answers
            $table->text('reason_for_interest')
                ->comment('لماذا أنت مهتم — min 25 chars');
            $table->text('life_goals')
                ->comment('أهداف الحياة');
            $table->text('marriage_expectations')
                ->comment('توقعات الزواج');

            $table->enum('status', [
                'pending',
                'accepted',
                'declined',
                'expired',      // Auto-expired after N days
                'cancelled',    // Sender withdrew
            ])->default('pending');

            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('declined_at')->nullable();
            $table->timestamp('expires_at')->nullable()
                ->comment('Set to now()+7days on creation');

            // If accepted, the resulting conversation (FK added in a later migration)
            $table->unsignedBigInteger('conversation_id')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // A user can only have one active request per pair
            $table->unique(['from_user_id', 'to_user_id'], 'uq_match_request_pair');

            $table->index(['to_user_id', 'status']);
            $table->index(['from_user_id', 'status']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('match_requests');
    }
};
