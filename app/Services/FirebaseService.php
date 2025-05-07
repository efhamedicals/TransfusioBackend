<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Google\Auth\Credentials\ServiceAccountCredentials;

class FirebaseService
{
    protected $projectId;
    protected $accessToken;

    public function __construct()
    {
        $serviceAccountJson = json_decode(file_get_contents(storage_path('app/firebase/service-account.json')), true);
        $this->projectId = $serviceAccountJson['project_id'];

        // Générer un token d'accès OAuth 2.0
        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];
        $credentials = new ServiceAccountCredentials($scopes, $serviceAccountJson);
        $this->accessToken = $credentials->fetchAuthToken()['access_token'];
    }

    public function sendToDevice($token, $title, $body, $data = [])
    {
        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        $message = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => $data,
            ]
        ];

        $response = Http::withToken($this->accessToken)
            ->post($url, $message);

        return $response->json();
    }
}
