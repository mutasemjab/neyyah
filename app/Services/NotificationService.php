<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Create an in-app notification and send FCM push.
     */
    public function send(
        User $user,
        string $type,
        string $titleAr,
        string $bodyAr,
        array $data = []
    ): AppNotification {
        $notification = AppNotification::create([
            'user_id'  => $user->id,
            'type'     => $type,
            'title_ar' => $titleAr,
            'body_ar'  => $bodyAr,
            'data'     => $data,
            'is_read'  => false,
        ]);

        $this->sendFcm($user, $titleAr, $bodyAr, $data);

        return $notification;
    }

    /**
     * Send FCM push notification to all user devices.
     */
    public function sendFcm(User $user, string $title, string $body, array $data = []): void
    {
        $tokens = $user->deviceTokens()->pluck('token')->toArray();

        foreach ($tokens as $token) {
            $this->sendFcmToToken($token, $title, $body, $data);
        }
    }

    /**
     * Send FCM to a specific device token.
     */
    private function sendFcmToToken(string $deviceToken, string $title, string $body, array $data = []): void
    {
        try {
            $credentialsPath = storage_path(config('firebase.credentials', 'firebase-credentials.json'));

            if (!file_exists($credentialsPath)) {
                Log::warning('Firebase credentials not found at: ' . $credentialsPath);

                return;
            }

            $client = new \Google\Client();
            $client->setAuthConfig($credentialsPath);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

            $httpClient = $client->authorize();

            $projectId  = json_decode(file_get_contents($credentialsPath), true)['project_id'];
            $url        = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

            $httpClient->post($url, [
                'json' => [
                    'message' => [
                        'token'        => $deviceToken,
                        'notification' => [
                            'title' => $title,
                            'body'  => $body,
                        ],
                        'data' => array_map('strval', $data),
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('FCM send failed: ' . $e->getMessage());
        }
    }
}
