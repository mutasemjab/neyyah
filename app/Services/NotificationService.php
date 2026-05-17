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
            $credentialsPath = base_path(env('FIREBASE_CREDENTIALS_PATH'));

            if (!file_exists($credentialsPath)) {
                Log::warning('Firebase credentials not found at: ' . $credentialsPath);
                return;
            }

            $client = new \Google\Client();
            
            $client->setAuthConfig($credentialsPath);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
            $client->useApplicationDefaultCredentials();
            $client->fetchAccessTokenWithAssertion();

            $tokenResponse = $client->getAccessToken();
            $accessToken   = $tokenResponse['access_token'];

            $projectId = json_decode(file_get_contents($credentialsPath), true)['project_id'];
            $url       = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

            $payload = json_encode([
                'message' => [
                    'token'        => $deviceToken,
                    'notification' => [
                        'title' => $title,
                        'body'  => $body,
                    ],
                    'data'    => array_map('strval', $data),
                    'android' => [
                        'priority' => 'high',
                    ],
                    'apns' => [
                        'headers' => [
                            'apns-priority' => '10',
                        ],
                        'payload' => [
                            'aps' => [
                                'sound' => 'default',
                                'badge' => 1,
                            ],
                        ],
                    ],
                ],
            ]);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer {$accessToken}",
                'Content-Type: application/json',
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

            $result = curl_exec($ch);
            $err    = curl_error($ch);
            curl_close($ch);

            if ($err || $result === false) {
                Log::error('FCM send failed (cURL): ' . $err);
                return;
            }

            $response = json_decode($result, true);

            if (!isset($response['name'])) {
                Log::error('FCM send failed (API): ' . json_encode($response));
            }
        } catch (\Exception $e) {
            Log::error('FCM send failed: ' . $e->getMessage());
        }
    }
}
