<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultation_sessions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUlid('consultant_id')->constrained('consultants')->cascadeOnDelete();
            $table->string('availability_id', 26)->nullable();
            $table->date('session_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('meeting_type', ['voice', 'video', 'chat']);
            $table->enum('status', [
                'pending_payment', 'confirmed', 'ongoing', 'completed', 'cancelled',
            ])->default('pending_payment');
            $table->string('payment_reference')->nullable();
            $table->decimal('price', 10, 2);
            $table->text('notes_ar')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancelled_reason_ar')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultation_sessions');
    }
};
