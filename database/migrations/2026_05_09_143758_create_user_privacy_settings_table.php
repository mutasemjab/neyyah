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
        Schema::create('user_privacy_settings', function (Blueprint $table) {
              $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->unique();

            $table->boolean('hide_from_contacts')->default(true)
                ->comment('Exclude user from results if viewer has their phone number');
            $table->boolean('anonymous_browsing')->default(false)
                ->comment('Do not log profile views');
            $table->boolean('blur_images')->default(true)
                ->comment('Always serve blurred_path instead of original');
            $table->boolean('hide_real_name')->default(true)
                ->comment('Show only first initial to non-accepted users');
            $table->boolean('show_last_active')->default(false);
            $table->boolean('allow_location_detect')->default(true)
                ->comment('If false, skip location-proximity matching');

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
        Schema::dropIfExists('user_privacy_settings');
    }
};
