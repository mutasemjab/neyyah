<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marriage_requests', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('from_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUlid('to_user_id')->constrained('users')->cascadeOnDelete();
            $table->text('reason_for_interest')->nullable();
            $table->text('life_goals')->nullable();
            $table->text('marriage_expectations')->nullable();
            $table->string('status', 20)->default('pending');
            $table->timestamp('sent_at')->useCurrent();
            $table->timestamp('responded_at')->nullable();

            $table->unique(['from_user_id', 'to_user_id']);
            $table->index(['to_user_id', 'status']);
            $table->index(['from_user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marriage_requests');
    }
};
