<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\SubscribeRequest;
use App\Http\Resources\SubscriptionPackageResource;
use App\Http\Resources\SubscriptionResource;
use App\Models\Subscription;
use App\Models\SubscriptionPackage;
use App\Services\CoinService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends ApiController
{
    public function __construct(
        private CoinService $coinService,
        private NotificationService $notificationService
    ) {
    }

    /**
     * Get all active subscription packages.
     */
    public function packages(): JsonResponse
    {
        $packages = SubscriptionPackage::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return $this->success(SubscriptionPackageResource::collection($packages));
    }

    /**
     * Get current user's active subscription.
     */
    public function current(Request $request): JsonResponse
    {
        $user         = $request->user();
        $subscription = Subscription::with('package')
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->where('ends_at', '>', now())
            ->latest()
            ->first();

        if (!$subscription) {
            return $this->success(null, 'لا يوجد اشتراك نشط.');
        }

        return $this->success(new SubscriptionResource($subscription));
    }

    /**
     * Subscribe to a package.
     */
    public function subscribe(SubscribeRequest $request): JsonResponse
    {
        $user    = $request->user();
        $package = SubscriptionPackage::find($request->package_id);

        if (!$package || !$package->is_active) {
            return $this->error('الباقة المحددة غير متوفرة.', 422);
        }

        // Deactivate any existing subscriptions
        Subscription::where('user_id', $user->id)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        // Create new subscription
        $subscription = Subscription::create([
            'user_id'           => $user->id,
            'package_id'        => $package->id,
            'starts_at'         => now(),
            'ends_at'           => now()->addMonth(),
            'is_active'         => true,
            'payment_reference' => $request->payment_reference,
        ]);

        // Add coins to wallet
        if ($package->coins_per_month > 0) {
            $this->coinService->earn(
                $user,
                $package->coins_per_month,
                'subscription',
                'عملات اشتراك باقة ' . $package->name_ar
            );
        }

        // Send notification
        $this->notificationService->send(
            $user,
            'subscription_activated',
            'تم تفعيل الاشتراك',
            'مرحباً بك في باقة ' . $package->name_ar . '! استمتع بالمميزات.',
            ['package_id' => $package->id, 'subscription_id' => $subscription->id]
        );

        $subscription->load('package');

        return $this->success(new SubscriptionResource($subscription), 'تم الاشتراك بنجاح!', 201);
    }
}
