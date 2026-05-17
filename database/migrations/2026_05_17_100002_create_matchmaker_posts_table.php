<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matchmaker_posts', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('matchmaker_id')->constrained('matchmakers')->cascadeOnDelete();
            $table->enum('gender', ['male', 'female']);
            $table->unsignedTinyInteger('age_from');
            $table->unsignedTinyInteger('age_to');
            $table->string('city')->nullable();
            $table->string('nationality_ar')->nullable();
            $table->string('profession_ar')->nullable();
            $table->string('religiosity_level')->nullable();
            $table->string('education_level')->nullable();
            $table->text('bio_ar');
            $table->text('requirements_ar');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('interests_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matchmaker_posts');
    }
};
