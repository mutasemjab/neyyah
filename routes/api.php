<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\CoinsController;
use App\Http\Controllers\Api\ConversationController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\MatchingController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RequestController;
use App\Http\Controllers\Api\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // ── Public routes ─────────────────────────────
    Route::post('auth/send-otp',   [AuthController::class, 'sendOtp'])->middleware('throttle:5,1');
    Route::post('auth/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::get('cities',           [CityController::class, 'index']);

    // ── Protected routes ──────────────────────────
    Route::middleware(['auth:api', 'update.last.active', 'throttle:60,1'])->group(function () {

        Route::post('auth/logout',        [AuthController::class, 'logout']);
        Route::post('auth/refresh-token', [AuthController::class, 'refreshToken']);

        // Profile
        Route::prefix('profile')->group(function () {
            Route::get('me',                    [ProfileController::class, 'me']);
            Route::put('me',                    [ProfileController::class, 'update']);
            Route::post('me/images',            [ProfileController::class, 'uploadImage']);
            Route::delete('me/images/{order}',  [ProfileController::class, 'deleteImage']);
            Route::put('me/intent-card',        [ProfileController::class, 'updateIntentCard']);
            Route::put('me/privacy',            [ProfileController::class, 'updatePrivacy']);
            Route::post('setup',                [ProfileController::class, 'setup']);
            Route::get('{id}',                  [ProfileController::class, 'show']);
        });

        // Routes requiring profile completion >= 50%
        Route::middleware('profile.complete')->group(function () {

            // Matching
            Route::prefix('matching')->group(function () {
                Route::get('suggestions',                [MatchingController::class, 'suggestions']);
                Route::post('suggestions/{id}/view',     [MatchingController::class, 'view']);
                Route::post('suggestions/{id}/dismiss',  [MatchingController::class, 'dismiss']);
                Route::get('filters',                    [MatchingController::class, 'getFilters']);
                Route::put('filters',                    [MatchingController::class, 'updateFilters']);
            });

            // Marriage requests
            Route::prefix('requests')->group(function () {
                Route::get('incoming',    [RequestController::class, 'incoming']);
                Route::get('sent',        [RequestController::class, 'sent']);
                Route::post('',           [RequestController::class, 'store']);
                Route::get('{id}',        [RequestController::class, 'show']);
                Route::put('{id}/accept', [RequestController::class, 'accept']);
                Route::put('{id}/reject', [RequestController::class, 'reject']);
            });
        });

        // Conversations
        Route::prefix('conversations')->group(function () {
            Route::get('', [ConversationController::class, 'index']);

            Route::middleware('conversation.participant')->group(function () {
                Route::get('{id}',                         [ConversationController::class, 'show']);
                Route::get('{id}/questions',               [ConversationController::class, 'questions']);
                Route::post('{id}/questions/{qid}/answer', [ConversationController::class, 'answer']);
                Route::put('{id}/stage',                   [ConversationController::class, 'updateStage']);
                Route::put('{id}/last-activity',           [ConversationController::class, 'updateActivity']);
            });
        });

        // Coins
        Route::prefix('coins')->group(function () {
            Route::get('wallet',         [CoinsController::class, 'wallet']);
            Route::post('claim-daily',   [CoinsController::class, 'claimDaily']);
            Route::post('earn',          [CoinsController::class, 'earn'])->middleware('throttle:10,60');
            Route::get('transactions',   [CoinsController::class, 'transactions']);
        });

        // Subscriptions
        Route::prefix('subscriptions')->group(function () {
            Route::get('packages',       [SubscriptionController::class, 'packages']);
            Route::get('current',        [SubscriptionController::class, 'current']);
            Route::post('subscribe',     [SubscriptionController::class, 'subscribe']);
        });

        // Notifications
        Route::prefix('notifications')->group(function () {
            Route::get('',               [NotificationController::class, 'index']);
            Route::put('read-all',       [NotificationController::class, 'markAllRead']);
            Route::put('{id}/read',      [NotificationController::class, 'markRead']);
        });

        // Devices
        Route::prefix('devices')->group(function () {
            Route::post('token',         [DeviceController::class, 'store']);
            Route::delete('token',       [DeviceController::class, 'destroy']);
        });
    });
});
