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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->enum('type', [
                'new_request',          // وردك طلب تعارف
                'request_accepted',     // تم قبول طلبك
                'request_declined',     // تم رفض طلبك
                'new_message',          // رسالة جديدة
                'partner_answered',     // أجاب الطرف الآخر على سؤال
                'chat_unlocked',        // فُتحت المحادثة الحرة
                'daily_suggestions',    // اقتراحات اليوم جاهزة
                'profile_incomplete',   // اكمل ملفك
                'chat_expiring',        // المحادثة ستنتهي قريباً
                'stage_advanced',       // انتقلتم لمرحلة جديدة
                'system',               // رسائل إدارية
            ]);

            $table->string('title_ar');
            $table->text('body_ar');

            // Polymorphic reference to the related entity
            $table->string('related_type')->nullable(); // 'match_request', 'conversation', 'message'
            $table->unsignedBigInteger('related_id')->nullable();

            // Push
            $table->string('fcm_token')->nullable();
            $table->boolean('push_sent')->default(false);
            $table->timestamp('push_sent_at')->nullable();

            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'read_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};
