<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_interest_labels', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignUlid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('label', 60);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_interest_labels');
    }
};
