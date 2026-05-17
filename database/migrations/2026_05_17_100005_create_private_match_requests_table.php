<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('private_match_requests', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUlid('matchmaker_id')->constrained('matchmakers')->cascadeOnDelete();
            $table->foreignUlid('package_id')->nullable()->constrained('matchmaker_packages')->nullOnDelete();
            $table->enum('status', [
                'pending_payment', 'active', 'searching',
                'candidates_sent', 'completed', 'cancelled',
            ])->default('pending_payment');
            $table->string('payment_reference')->nullable();
            $table->decimal('price', 10, 2);
            $table->json('personal_details');
            $table->json('partner_preferences');
            $table->text('notes_ar')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancelled_reason_ar')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('private_match_requests');
    }
};
