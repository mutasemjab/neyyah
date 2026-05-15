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
        Schema::create('daily_suggestions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()->cascadeOnDelete()
                ->comment('Who is receiving the suggestion');

            $table->foreignId('suggested_user_id')
                ->constrained('users')->cascadeOnDelete()
                ->comment('Who is being suggested');

            $table->date('suggestion_date')->comment('The calendar date for this batch');

            $table->decimal('compatibility_score', 4, 2)->default(0.00)
                ->comment('0.00–1.00, computed by matching algorithm');

            // Which compatibility factors fired (JSON array of CompatibilityType slugs)
            $table->json('compatibility_factors')->nullable()
                ->comment('e.g. ["same_city","same_values","same_timeline"]');

            // Approx distance label — never exact coordinates
            $table->enum('proximity_label', [
                'nearby',
                'same_area',
                'same_city',
                'different_city',
            ])->default('same_city');

            $table->enum('status', [
                'pending',       // Not yet seen
                'viewed',        // Card was tapped/opened
                'request_sent',  // User sent a match request
                'dismissed',     // User swiped away / clicked "تجاوز"
            ])->default('pending');

            $table->timestamp('viewed_at')->nullable();
            $table->timestamps();

            // A user should not see the same person twice on the same day
            $table->unique(['user_id', 'suggested_user_id', 'suggestion_date'], 'uq_daily_suggestion');

            $table->index(['user_id', 'suggestion_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daily_suggestions');
    }
};
