<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseNotificationService
{
    /**
     * Build a JWT and exchange it for an OAuth2 access token for FCM V1 API.
     * Token is cached for 58 minutes (expires in 60).
     */
    private function getAccessToken(): ?string
    {
        return Cache::remember('firebase_v1_access_token', 3480, function () {
            $credentialsPath = storage_path('app/firebase-credentials.json');

            if (!file_exists($credentialsPath)) {
                Log::warning('FCM: service account credentials file not found at ' . $credentialsPath);
                return null;
            }

            $creds = json_decode(file_get_contents($credentialsPath), true);
            if (!$creds || !isset($creds['private_key'], $creds['client_email'])) {
                Log::error('FCM: invalid service account JSON.');
                return null;
            }

            $now    = time();
            $header  = $this->base64UrlEncode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
            $payload = $this->base64UrlEncode(json_encode([
                'iss'   => $creds['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud'   => 'https://oauth2.googleapis.com/token',
                'iat'   => $now,
                'exp'   => $now + 3600,
            ]));

            $signingInput = "{$header}.{$payload}";
            $privateKey   = openssl_pkey_get_private($creds['private_key']);
            openssl_sign($signingInput, $signature, $privateKey, OPENSSL_ALGO_SHA256);
            $jwt = "{$signingInput}." . $this->base64UrlEncode($signature);

            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt,
            ]);

            if ($response->failed()) {
                Log::error('FCM: token exchange failed', ['body' => $response->body()]);
                return null;
            }

            return $response->json('access_token');
        });
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Send a push notification to a single FCM registration token via V1 API.
     */
    public function sendToToken(string $fcmToken, string $title, string $body, array $data = []): bool
    {
        if (empty($fcmToken)) {
            return false;
        }

        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return false;
        }

        $projectId = config('services.firebase.project_id');
        $url       = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        try {
            Log::info('FCM: Sending notification', [
                'token' => substr($fcmToken, 0, 20) . '...',
                'title' => $title,
                'body' => $body,
                'data' => $data,
            ]);

            // Include status in the tag so each event gets its own notification
            // (e.g. "on the way" stays visible alongside "delivered")
            $tag = isset($data['order_id'])
                ? 'order-' . $data['order_id'] . '-' . ($data['status'] ?? 'update')
                : 'vnf-order';

            // Use data-only payload — the service worker's onBackgroundMessage
            // handler shows exactly ONE browser notification. Using webpush.notification
            // together with the Firebase compat SDK causes a duplicate because both
            // Chrome's push stack and the SDK independently call showNotification().
            $response = Http::withToken($accessToken)->post($url, [
                'message' => [
                    'token' => $fcmToken,
                    'data'  => array_merge(array_map('strval', $data), [
                        'title' => $title,
                        'body'  => $body,
                        'tag'   => $tag,
                        'link'  => url('/customer/orders'),
                        'icon'  => '/images/logo.png',
                    ]),
                    'webpush' => [
                        'headers' => [
                            'Urgency' => 'high',
                        ],
                    ],
                ],
            ]);

            if ($response->failed()) {
                Log::error('FCM V1 send failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                    'token'  => substr($fcmToken, 0, 20) . '...',
                ]);
                return false;
            }

            return true;

        } catch (\Exception $e) {
            Log::error('FCM V1 exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Notify a customer (User model) of a delivery status update.
     */
    public function notifyOrderStatus(User $user, string $title, string $body, array $data = []): bool
    {
        if (empty($user->fcm_token)) {
            return false;
        }

        return $this->sendToToken($user->fcm_token, $title, $body, $data);
    }
}
