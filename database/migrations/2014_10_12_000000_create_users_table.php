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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 20)->unique();
            $table->string('country_code', 5)->default('+966');
            $table->rememberToken();
            // Account status
            $table->enum('status', [
                'pending',      // OTP not verified yet
                'active',       // Normal active account
                'suspended',    // Temporarily suspended
                'banned',       // Permanently banned
                'deleted',      // Soft-deleted / deactivated
            ])->default('pending');

            $table->boolean('is_profile_complete')->default(false);
            $table->boolean('is_verified')->default(false);         // Identity verified
            $table->boolean('is_admin')->default(false);

            // Timestamps
            $table->timestamp('phone_verified_at')->nullable();
            $table->timestamp('last_active_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('last_active_at');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
