<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('guided_questions');

        Schema::create('guided_questions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->text('text_ar');
            $table->text('hint_ar')->nullable();
            $table->string('category_ar', 100)->nullable();
            $table->tinyInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guided_questions');
    }
};
