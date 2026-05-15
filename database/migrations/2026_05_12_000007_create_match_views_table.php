<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('match_views', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('viewer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUlid('viewed_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('viewed_at')->useCurrent();

            $table->index(['viewer_id', 'viewed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_views');
    }
};
