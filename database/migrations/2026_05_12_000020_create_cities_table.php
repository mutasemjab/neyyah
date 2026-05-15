<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name_ar', 60);
            $table->string('name_en', 60);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
