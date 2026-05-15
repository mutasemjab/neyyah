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
        Schema::create('user_intent_cards', function (Blueprint $table) {
             $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->unique();

            $table->enum('children_intent', [
                'yes',      // يريد الأطفال
                'no',       // لا يريد
                'open',     // منفتح
            ])->default('yes');

            $table->boolean('open_to_working_partner')->default(true);

            $table->enum('living_preference', [
                'own_home',     // مسكن مستقل
                'with_family',  // مع العائلة
                'flexible',     // مرن
            ])->default('own_home');

            $table->enum('target_timeline', [
                'three_months',
                'six_months',
                'one_year',
                'when_right',
            ])->default('six_months');

            $table->text('additional_notes')->nullable()
                ->comment('Optional free-text notes on intent card');

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
        Schema::dropIfExists('user_intent_cards');
    }
};
