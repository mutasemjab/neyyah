<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\UpdateIntentCardRequest;
use App\Http\Requests\Api\UpdatePrivacyRequest;
use App\Http\Requests\Api\UpdateProfileRequest;
use App\Http\Requests\Api\UploadImageRequest;
use App\Http\Resources\PrivacySettingResource;
use App\Http\Resources\UserResource;
use App\Models\Block;
use App\Models\IntentCard;
use App\Models\MatchDismissal;
use App\Models\PrivacySetting;
use App\Models\ProfileImage;
use App\Models\User;
use App\Services\MatchingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends ApiController
{
    public function __construct(private MatchingService $matchingService)
    {
    }

    /**
     * Return current authenticated user profile.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load([
            'profileImages',
            'interests',
            'intentCard',
            'privacySettings',
            'wallet',
            'matchFilters',
            'matchmakerProfile',
            'consultantProfile',
        ]);

        return $this->success(new UserResource($user));
    }

    /**
     * Update current user profile.
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->except('interests');

        $user->update($data);

        // Update interests if provided
        if ($request->has('interests')) {
            $user->interests()->delete();
            foreach ($request->interests as $label) {
                $user->interests()->create(['label' => $label]);
            }
        }

        $this->matchingService->recalculateAndSave($user);

        $user->load(['profileImages', 'interests', 'intentCard', 'privacySettings']);

        return $this->success(new UserResource($user), 'تم تحديث الملف الشخصي بنجاح.');
    }

    /**
     * Upload a profile image.
     */
    public function uploadImage(UploadImageRequest $request): JsonResponse
    {
        $user = $request->user();

        // Check max 5 images
        if ($user->profileImages()->count() >= 5) {
            return $this->error('لا يمكن إضافة أكثر من 5 صور.', 422);
        }

        $image = $request->file('image');

        // Generate unique filenames
        $filename        = uniqid('profile_', true);
        $extension       = $image->getClientOriginalExtension();
        $originalPath    = "assets/admin/uploads/profiles/{$filename}.{$extension}";
        $blurredPath     = "assets/admin/uploads/profiles/{$filename}_blurred.{$extension}";

        $uploadDir = base_path('assets/admin/uploads/profiles');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Check if Intervention Image is available
        if (class_exists(\Intervention\Image\Facades\Image::class) || class_exists(\Intervention\Image\ImageManager::class)) {
            try {
                // Resize original (max 1200px)
                $img = \Intervention\Image\Facades\Image::make($image)
                    ->resize(1200, 1200, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })
                    ->save(base_path($originalPath));

                // Create blurred copy
                \Intervention\Image\Facades\Image::make($image)
                    ->resize(1200, 1200, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })
                    ->blur(15)
                    ->save(base_path($blurredPath));
            } catch (\Exception $e) {
                // Fallback: just move the file
                $image->move(base_path('assets/admin/uploads/profiles'), "{$filename}.{$extension}");
                $blurredPath = null;
            }
        } else {
            // No Intervention Image: just move the file
            $image->move(base_path('assets/admin/uploads/profiles'), "{$filename}.{$extension}");
            $blurredPath = null;
        }

        $sortOrder = $user->profileImages()->max('sort_order') + 1;

        $profileImage = ProfileImage::create([
            'user_id'     => $user->id,
            'url'         => '/' . $originalPath,
            'blurred_url' => $blurredPath ? '/' . $blurredPath : null,
            'sort_order'  => $sortOrder,
        ]);

        $this->matchingService->recalculateAndSave($user);

        return $this->success([
            'image' => [
                'url'         => $profileImage->url,
                'blurred_url' => $profileImage->blurred_url,
                'sort_order'  => $profileImage->sort_order,
            ],
        ], 'تم رفع الصورة بنجاح.');
    }

    /**
     * Delete a profile image by sort_order.
     */
    public function deleteImage(Request $request, int $order): JsonResponse
    {
        $user  = $request->user();
        $image = $user->profileImages()->where('sort_order', $order)->first();

        if (!$image) {
            return $this->error('الصورة غير موجودة.', 404);
        }

        // Delete files
        $urlPath     = base_path(ltrim($image->url, '/'));
        $blurredPath = $image->blurred_url ? base_path(ltrim($image->blurred_url, '/')) : null;

        if (file_exists($urlPath)) {
            unlink($urlPath);
        }

        if ($blurredPath && file_exists($blurredPath)) {
            unlink($blurredPath);
        }

        $image->delete();

        $this->matchingService->recalculateAndSave($user);

        return $this->success([], 'تم حذف الصورة بنجاح.');
    }

    /**
     * Update or create intent card.
     */
    public function updateIntentCard(UpdateIntentCardRequest $request): JsonResponse
    {
        $user = $request->user();

        $user->intentCard()->updateOrCreate(
            ['user_id' => $user->id],
            $request->validated()
        );

        $this->matchingService->recalculateAndSave($user);

        $user->load('intentCard');

        return $this->success(new UserResource($user), 'تم تحديث بطاقة النية بنجاح.');
    }

    /**
     * Update or create privacy settings.
     */
    public function updatePrivacy(UpdatePrivacyRequest $request): JsonResponse
    {
        $user = $request->user();

        $settings = $user->privacySettings()->updateOrCreate(
            ['user_id' => $user->id],
            $request->validated()
        );

        return $this->success(new PrivacySettingResource($settings), 'تم تحديث إعدادات الخصوصية بنجاح.');
    }

    /**
     * Show a specific user profile with compatibility.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $auth      = $request->user();
        $candidate = User::with(['profileImages', 'interests', 'intentCard', 'privacySettings'])
            ->find($id);

        if (!$candidate) {
            return $this->error('المستخدم غير موجود.', 404);
        }

        // Block check: either party has blocked the other
        $isBlocked = Block::where(function ($q) use ($auth, $id) {
            $q->where('blocker_id', $auth->id)->where('blocked_id', $id);
        })->orWhere(function ($q) use ($auth, $id) {
            $q->where('blocker_id', $id)->where('blocked_id', $auth->id);
        })->exists();

        if ($isBlocked) {
            return $this->error('المستخدم غير موجود.', 404);
        }

        $compatibility = $this->matchingService->calculateCompatibility($auth, $candidate);
        $proximity     = $this->matchingService->getProximityLabel($auth, $candidate);

        return $this->success([
            'profile'               => new UserResource($candidate),
            'compatibility_score'   => $compatibility['score'],
            'compatibility_factors' => $compatibility['factors'],
            'proximity'             => $proximity,
        ]);
    }

    /**
     * Setup profile: update profile + intent card at once.
     */
    public function setup(Request $request): JsonResponse
    {
        $user = $request->user();

        // Update profile fields
        $profileData = $request->only([
            'display_name', 'birth_date', 'gender', 'city', 'bio',
            'religiosity_level', 'education_level', 'income_range',
            'is_smoker', 'marriage_timeline', 'is_ready_for_marriage',
            'latitude', 'longitude', 'firebase_uid',
        ]);

        if (!empty($profileData)) {
            $user->update(array_filter($profileData, fn ($v) => $v !== null));
        }

        // Update interests
        if ($request->has('interests') && is_array($request->interests)) {
            $user->interests()->delete();
            foreach ($request->interests as $label) {
                $user->interests()->create(['label' => $label]);
            }
        }

        // Update intent card
        $intentData = $request->only([
            'children_intent', 'open_to_working_partner',
            'living_preference', 'target_timeline', 'additional_notes',
        ]);

        if (!empty($intentData)) {
            $user->intentCard()->updateOrCreate(['user_id' => $user->id], $intentData);
        }

        $this->matchingService->recalculateAndSave($user);

        $user->load(['profileImages', 'interests', 'intentCard', 'privacySettings']);

        return $this->success(new UserResource($user), 'تم إعداد الملف الشخصي بنجاح.');
    }
}
