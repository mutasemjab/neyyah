<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('session_reviews', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('session_id')->constrained('consultation_sessions')->cascadeOnDelete();
            $table->foreignUlid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUlid('consultant_id')->constrained('consultants')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating'); // 1-5
            $table->text('review_ar')->nullable();
            $table->timestamps();

            $table->unique('session_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_reviews');
    }
};
