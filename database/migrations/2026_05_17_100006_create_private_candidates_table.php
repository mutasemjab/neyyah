<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('private_candidates', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('request_id')->constrained('private_match_requests')->cascadeOnDelete();
            $table->string('candidate_user_id', 26)->nullable();
            $table->json('candidate_details');
            $table->text('note_ar')->nullable();
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('private_candidates');
    }
};
