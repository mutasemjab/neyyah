<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\DeleteDeviceTokenRequest;
use App\Http\Requests\Api\DeviceTokenRequest;
use App\Models\DeviceToken;
use Illuminate\Http\JsonResponse;

class DeviceController extends ApiController
{
    /**
     * Register or update a device token.
     */
    public function store(DeviceTokenRequest $request): JsonResponse
    {
        $user = $request->user();

        DeviceToken::updateOrCreate(
            [
                'user_id' => $user->id,
                'token'   => $request->token,
            ],
            [
                'platform' => $request->platform,
            ]
        );

        return $this->success([], 'تم تسجيل الجهاز بنجاح.');
    }

    /**
     * Delete a device token.
     */
    public function destroy(DeleteDeviceTokenRequest $request): JsonResponse
    {
        $user = $request->user();

        DeviceToken::where('user_id', $user->id)
            ->where('token', $request->token)
            ->delete();

        return $this->success([], 'تم حذف رمز الجهاز بنجاح.');
    }
}
