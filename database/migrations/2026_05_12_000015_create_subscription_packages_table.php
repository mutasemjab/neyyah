<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('subscription_packages');

        Schema::create('subscription_packages', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name_ar', 60);
            $table->integer('coins_per_month');
            $table->decimal('price_jd', 8, 3);
            $table->boolean('is_popular')->default(false);
            $table->json('features_ar');
            $table->tinyInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_packages');
    }
};
