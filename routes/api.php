<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BlockController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\CoinsController;
use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\SupportController;
use App\Http\Controllers\Api\VerificationController;
use App\Http\Controllers\Api\ConsultantController;
use App\Http\Controllers\Api\ConsultationSessionController;
use App\Http\Controllers\Api\ConversationController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\MatchingController;
use App\Http\Controllers\Api\MatchmakerController;
use App\Http\Controllers\Api\MatchmakerPostController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PrivateMatchRequestController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RequestController;
use App\Http\Controllers\Api\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // ── Public routes ─────────────────────────────
    Route::post('auth/send-otp',   [AuthController::class, 'sendOtp'])->middleware('throttle:5,1');
    Route::post('auth/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::get('cities',           [CityController::class, 'index']);
    Route::get('content',          [ContentController::class, 'show']);

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

        // Blocks
        Route::prefix('blocks')->group(function () {
            Route::get('',              [BlockController::class, 'index']);
            Route::post('',             [BlockController::class, 'store']);
            Route::delete('{user_id}',  [BlockController::class, 'destroy']);
        });

        // Support
        Route::post('support', [SupportController::class, 'store']);

        // Identity Verification
        Route::prefix('verification')->group(function () {
            Route::get('',    [VerificationController::class, 'status']);
            Route::post('',   [VerificationController::class, 'submit']);
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

        // ── Feature 1: Matchmaker Marketplace ────────────────────────────────

        // Public-ish matchmaker browsing (authenticated users)
        Route::prefix('matchmakers')->group(function () {
            Route::get('',           [MatchmakerController::class, 'index']);
            Route::get('my-profile', [MatchmakerController::class, 'myProfile']);
            Route::post('apply',     [MatchmakerController::class, 'apply']);
            Route::get('{id}',       [MatchmakerController::class, 'show']);
        });

        // Public post browsing
        Route::prefix('matchmaker-posts')->group(function () {
            Route::get('',              [MatchmakerPostController::class, 'index']);
            Route::get('my-interests',  [MatchmakerPostController::class, 'myInterests']);
            Route::get('{id}',          [MatchmakerPostController::class, 'show']);
            Route::post('{id}/interest', [MatchmakerPostController::class, 'sendInterest']);
        });

        // Matchmaker-only routes
        Route::middleware('is.matchmaker')->prefix('matchmaker')->group(function () {
            // Posts management
            Route::get('posts',                                      [MatchmakerPostController::class, 'myPosts']);
            Route::post('posts',                                     [MatchmakerPostController::class, 'store']);
            Route::put('posts/{id}',                                 [MatchmakerPostController::class, 'update']);
            Route::delete('posts/{id}',                              [MatchmakerPostController::class, 'destroy']);
            Route::get('posts/{id}/interests',                       [MatchmakerPostController::class, 'postInterests']);
            Route::put('posts/{postId}/interests/{interestId}',      [MatchmakerPostController::class, 'respondToInterest']);

            // Packages management
            Route::post('packages',         [PrivateMatchRequestController::class, 'storePackage']);
            Route::put('packages/{id}',     [PrivateMatchRequestController::class, 'updatePackage']);
            Route::delete('packages/{id}',  [PrivateMatchRequestController::class, 'deletePackage']);

            // Incoming private requests
            Route::get('private-requests',                             [PrivateMatchRequestController::class, 'incomingRequests']);
            Route::put('private-requests/{id}/status',                 [PrivateMatchRequestController::class, 'updateStatus']);
            Route::post('private-requests/{id}/candidates',            [PrivateMatchRequestController::class, 'addCandidate']);
        });

        // ── Feature 2: Private Matchmaking ────────────────────────────────────

        Route::prefix('private-requests')->group(function () {
            Route::post('',                                      [PrivateMatchRequestController::class, 'store']);
            Route::get('',                                       [PrivateMatchRequestController::class, 'myRequests']);
            Route::get('{id}',                                   [PrivateMatchRequestController::class, 'show']);
            Route::put('{id}/cancel',                            [PrivateMatchRequestController::class, 'cancel']);
            Route::get('{id}/candidates',                        [PrivateMatchRequestController::class, 'myCandidates']);
            Route::put('{id}/candidates/{candidateId}',          [PrivateMatchRequestController::class, 'respondToCandidate']);
        });

        // ── Feature 3: Consultation Sessions ──────────────────────────────────

        Route::prefix('consultants')->group(function () {
            Route::get('',                   [ConsultantController::class, 'index']);
            Route::get('my-profile',         [ConsultantController::class, 'myProfile']);
            Route::post('apply',             [ConsultantController::class, 'apply']);
            Route::get('{id}',               [ConsultantController::class, 'show']);
            Route::get('{id}/availability',  [ConsultantController::class, 'availability']);
            Route::post('{id}/book',         [ConsultationSessionController::class, 'book']);
        });

        Route::prefix('sessions')->group(function () {
            Route::get('',          [ConsultationSessionController::class, 'mySessions']);
            Route::get('{id}',      [ConsultationSessionController::class, 'show']);
            Route::put('{id}/cancel', [ConsultationSessionController::class, 'cancel']);
            Route::post('{id}/review', [ConsultationSessionController::class, 'submitReview']);
        });

        // Consultant-only routes
        Route::middleware('is.consultant')->prefix('consultant')->group(function () {
            Route::get('sessions',               [ConsultationSessionController::class, 'consultantSessions']);
            Route::put('sessions/{id}/status',   [ConsultationSessionController::class, 'updateSessionStatus']);
            Route::post('availability',          [ConsultantController::class, 'storeAvailability']);
            Route::delete('availability/{id}',   [ConsultantController::class, 'destroyAvailability']);
        });
    });
});
