<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('privacy_settings', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->boolean('hide_from_contacts')->default(true);
            $table->boolean('anonymous_browsing')->default(false);
            $table->boolean('blur_images')->default(true);
            $table->boolean('hide_real_name')->default(true);
            $table->boolean('show_last_active')->default(false);
            $table->boolean('allow_location_detect')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('privacy_settings');
    }
};
