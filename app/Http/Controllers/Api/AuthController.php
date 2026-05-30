<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\SendOtpRequest;
use App\Http\Requests\Api\VerifyOtpRequest;
use App\Http\Resources\UserResource;
use App\Models\CoinsWallet;
use App\Models\OtpCode;
use App\Models\PrivacySetting;
use App\Models\User;
use App\Services\MatchingService;
use App\Services\SmsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends ApiController
{
    public function __construct(
        private SmsService $smsService,
        private MatchingService $matchingService,
    ) {
    }

    /**
     * Send OTP to phone number.
     */
    public function sendOtp(SendOtpRequest $request): JsonResponse
    {
        $phone = $request->phone;
        $code  = (string) random_int(1000, 9999);

        OtpCode::create([
            'phone'      => $phone,
            'code'       => $code,
            'expires_at' => now()->addMinutes(5),
        ]);

        $this->smsService->send($phone, $code);

        $responseData = [];

        // In development, include code in response for testing
        if (config('app.env') !== 'production') {
            $responseData['code'] = $code;
        }

        return $this->success($responseData, 'تم إرسال رمز التحقق بنجاح.');
    }

    /**
     * Verify OTP and return auth token.
     */
    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $phone = $request->phone;
        $code  = $request->code;

        $isBypass = $code === '1111' && config('app.env') !== 'production';

        if ($isBypass) {
            $otp = null;
        } else {
            $otp = OtpCode::where('phone', $phone)
                ->where('code', $code)
                ->whereNull('used_at')
                ->where('expires_at', '>', now())
                ->latest('created_at')
                ->first();

            if (!$otp) {
                return $this->error('رمز التحقق غير صحيح أو منتهي الصلاحية.', 422);
            }

            $otp->update(['used_at' => now()]);
        }

        $isNew = false;

        // Find or create user
        $user = User::where('phone', $phone)->first();

        if (!$user) {
            $isNew = true;
            $user  = User::create([
                'phone'              => $phone,
                'phone_verified_at'  => now(),
            ]);

            // Create default wallet
            CoinsWallet::create([
                'user_id' => $user->id,
                'balance' => 5,
            ]);

            // Create default privacy settings
            PrivacySetting::create([
                'user_id' => $user->id,
            ]);
        } else {
            $user->update(['phone_verified_at' => now()]);
        }

        // Issue Passport token
        $token = $user->createToken('api-token')->accessToken;

        $user->load(['profileImages', 'interests', 'intentCard', 'privacySettings', 'wallet', 'matchmakerProfile', 'consultantProfile']);

        // Always recalculate so the login response reflects fresh completion_pct
        $this->matchingService->recalculateAndSave($user);

        return $this->success([
            'token'    => $token,
            'is_new'   => $isNew,
            'user'     => new UserResource($user),
        ], 'تم تسجيل الدخول بنجاح.');
    }

    /**
     * Revoke current token (logout).
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->token()->revoke();

        return $this->success([], 'تم تسجيل الخروج بنجاح.');
    }

    /**
     * Revoke current token and issue a new one.
     */
    public function refreshToken(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->token()->revoke();

        $token = $user->createToken('api-token')->accessToken;

        return $this->success([
            'token' => $token,
        ], 'تم تجديد الرمز بنجاح.');
    }
}
