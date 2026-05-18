<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('privacy_settings', function (Blueprint $table) {
            $table->boolean('show_age')->default(true)->after('allow_location_detect');
            $table->boolean('show_city')->default(true)->after('show_age');
            $table->boolean('show_photos_to_matches_only')->default(false)->after('show_city');
        });
    }

    public function down(): void
    {
        Schema::table('privacy_settings', function (Blueprint $table) {
            $table->dropColumn(['show_age', 'show_city', 'show_photos_to_matches_only']);
        });
    }
};
