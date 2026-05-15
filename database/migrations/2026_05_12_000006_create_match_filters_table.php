<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('match_filters');

        Schema::create('match_filters', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->tinyInteger('min_age')->default(18);
            $table->tinyInteger('max_age')->default(45);
            $table->json('cities')->default('[]');
            $table->json('religiosity_levels')->default('[]');
            $table->json('education_levels')->default('[]');
            $table->boolean('no_smokers')->default(true);
            $table->smallInteger('max_distance_km')->nullable();
            $table->json('marriage_timelines')->default('[]');
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_filters');
    }
};
