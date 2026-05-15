<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop all tables that have FK constraints referencing the old integer users.id.
        // This is required so MySQL allows us to drop and recreate users with a ULID PK.
        // Tables being replaced by v2 migrations are simply dropped here; admin-only tables
        // (blocks, reports, identity_verifications) are recreated by _000021–_000023.
        \DB::statement('SET FOREIGN_KEY_CHECKS=0');

        Schema::dropIfExists('guided_answers');
        Schema::dropIfExists('guided_questions');
        Schema::dropIfExists('conversations');
        Schema::dropIfExists('match_requests');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('blocks');
        Schema::dropIfExists('reports');
        Schema::dropIfExists('identity_verifications');
        Schema::dropIfExists('daily_suggestions');
        Schema::dropIfExists('user_intent_cards');
        Schema::dropIfExists('user_privacy_settings');
        Schema::dropIfExists('user_interests');
        Schema::dropIfExists('user_photos');
        Schema::dropIfExists('user_profiles');

        // The knet_transactions table is kept but its FK to the old users.id must be removed.
        if (Schema::hasTable('knet_transactions')) {
            Schema::table('knet_transactions', function (Blueprint $table) {
                $table->dropForeign('knet_transactions_user_id_foreign');
            });
        }

        \DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Now no table references users.id — safe to drop and recreate.
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('phone', 20)->unique();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('display_name', 60)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('gender', 10)->nullable();
            $table->string('city', 60)->nullable();
            $table->text('bio')->nullable();
            $table->string('religiosity_level', 20)->nullable();
            $table->string('education_level', 20)->nullable();
            $table->string('income_range', 20)->nullable();
            $table->boolean('is_smoker')->default(false);
            $table->string('marriage_timeline', 20)->nullable();
            $table->decimal('completion_pct', 5, 2)->default(0);
            $table->decimal('seriousness_score', 5, 2)->default(0);
            $table->boolean('is_ready_for_marriage')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamp('last_active_at')->nullable();
            $table->string('firebase_uid', 128)->nullable()->unique();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
