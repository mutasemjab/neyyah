<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('phone', 20)->unique();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('display_name', 60)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('gender', 10)->nullable();
            $table->string('city', 60)->nullable();
            $table->text('bio')->nullable();
            $table->string('religiosity_level', 20)->nullable();
            $table->string('education_level', 20)->nullable();
            $table->string('income_range', 20)->nullable();
            $table->boolean('is_smoker')->default(false);
            $table->string('marriage_timeline', 20)->nullable();
            $table->decimal('completion_pct', 5, 2)->default(0);
            $table->decimal('seriousness_score', 5, 2)->default(0);
            $table->boolean('is_ready_for_marriage')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamp('last_active_at')->nullable();
            $table->string('firebase_uid', 128)->nullable()->unique();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
