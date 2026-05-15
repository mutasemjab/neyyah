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
        Schema::create('user_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('path');
            $table->string('blurred_path')->nullable()->comment('Pre-blurred version for privacy');
            $table->string('thumbnail_path')->nullable();

            $table->unsignedTinyInteger('sort_order')->default(0)
                ->comment('0 = main photo');
            $table->boolean('is_approved')->default(false)
                ->comment('Admin must approve before showing');
            $table->boolean('is_visible')->default(true);
            $table->enum('visibility', [
                'blurred',      // Shown blurred to all
                'partial',      // Unblurred only after mutual accept
                'public',       // Fully visible (rare — admin setting)
            ])->default('blurred');

            $table->timestamps();

            $table->index(['user_id', 'sort_order']);
            $table->index('is_approved');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_photos');
    }
};
