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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->unique();

            // ── Basic Info ──────────────────────────
            $table->string('display_name', 30)->comment('First name only, for privacy');
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female']);

            // ── Location ────────────────────────────
            $table->string('city', 60);
            $table->string('country', 60)->default('السعودية');
            // Fuzzy location — we store exact but expose only approx label
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamp('location_updated_at')->nullable();

            // ── Religion ────────────────────────────
            $table->enum('religiosity', [
                'very_religious',
                'religious',
                'moderate',
                'cultural',
            ]);

            // ── Education & Career ───────────────────
            $table->enum('education', [
                'high_school',
                'diploma',
                'bachelor',
                'master',
                'phd',
            ]);
            $table->string('job_title', 80)->nullable();
            $table->string('employer', 80)->nullable();

            // ── Financial ───────────────────────────
            $table->enum('income_range', [
                'below_5k',
                'r5_to_10k',
                'r10_to_20k',
                'r20_to_40k',
                'above_40k',
            ])->nullable();

            // ── Lifestyle ───────────────────────────
            $table->boolean('is_smoker')->default(false);
            $table->enum('marriage_timeline', [
                'three_months',
                'six_months',
                'one_year',
                'when_right',
            ]);

            // ── Free text ───────────────────────────
            $table->text('bio')->nullable()->comment('Max 300 chars enforced in app');

            // ── Calculated scores ───────────────────
            $table->decimal('completion_pct', 4, 2)->default(0.00)
                ->comment('0.00–1.00, recalculated on each save');
            $table->decimal('seriousness_score', 4, 2)->default(0.00)
                ->comment('Algo: completion + activity + responses + ready_badge');

            $table->boolean('is_ready_for_marriage')->default(false);

            $table->timestamps();

            $table->index('gender');
            $table->index('city');
            $table->index('is_ready_for_marriage');
            $table->index('seriousness_score');
            $table->index(['latitude', 'longitude']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_profiles');
    }
};
