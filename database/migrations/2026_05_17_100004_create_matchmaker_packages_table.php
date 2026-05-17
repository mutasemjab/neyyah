<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matchmaker_packages', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('matchmaker_id')->constrained('matchmakers')->cascadeOnDelete();
            $table->string('name_ar');
            $table->decimal('price', 10, 2);
            $table->unsignedSmallInteger('duration_days');
            $table->unsignedTinyInteger('candidate_limit');
            $table->unsignedTinyInteger('consultation_sessions')->default(0);
            $table->boolean('priority_support')->default(false);
            $table->text('description_ar')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matchmaker_packages');
    }
};
