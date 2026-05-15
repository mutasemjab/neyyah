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
        Schema::create('guided_questions', function (Blueprint $table) {
             $table->id();
            $table->string('text_ar')->comment('The question in Arabic');
            $table->string('hint_ar')->nullable()->comment('Clarifying hint');
            $table->string('category_ar', 60)->nullable()->comment('e.g. التواصل، الأسرة');
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('version')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('guided_questions');
    }
};
