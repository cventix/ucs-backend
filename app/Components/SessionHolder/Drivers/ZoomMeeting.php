<?php


namespace App\Components\SessionHolder\Drivers;


use App\Components\SessionHolder\Contracts\Driver;
use App\Components\SessionHolder\Exceptions\SessionDeleteFailedException;
use App\Components\SessionHolder\Exceptions\SessionRegistrationFailedException;
use App\Components\SessionHolder\Exceptions\SessionUpdateFailedException;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Http;

class ZoomMeeting extends Driver
{
    const ZOOM_API_BASE_URL = 'https://api.zoom.us/v2';

    /**
     * @return string
     */
    public function generateToken()
    {
        $current = now();

        $payload = [
            "aud" => null,
            "iss" => config('services.zoom.key'),
            "exp" => $current->addSeconds(60)->timestamp,
            "iat" => $current->timestamp
        ];

        return JWT::encode($payload, config('services.zoom.secret'));
    }

    protected function _register()
    {
        $token = $this->generateToken();
        // $sessionStartTime = new \DateTime($this->startTime, new \DateTimeZone(config('app.timezone')));
        $sessionStartTime = $this->startTime;
        $sessionStartTime = $sessionStartTime->format('Y-m-d\TH:i:s');

        $hostEmail = $this->host->email;
        if ($this->host->zoom_email)
            $hostEmail = $this->host->zoom_email;

        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $token])
            ->post(self::ZOOM_API_BASE_URL . "/users/$hostEmail/meetings", [
                "topic" => $this->title ?? 'Session for ' . $hostEmail,
                "type" => 2,
                "start_time" => $sessionStartTime,
                "timezone" => config('app.timezone'),
                "duration" => (int) $this->duration,
                "settings" => [
                    "approval_type" => 2,
                ]
            ]);

        if ($response->status() != 201) {
            $errorMessage = !empty($response->json()) && isset($response->json()['message']) ? $response->json()['message'] : '';

            throw new SessionRegistrationFailedException($errorMessage);
        }

        $response = $response->json();

        return [
            'id' => $response['id'],
            'join_url' => $response['join_url'],
            'meta' => $response,
        ];
    }

    protected function _update()
    {
        $token = $this->generateToken();
        // $sessionStartTime = new \DateTime($this->startTime, new \DateTimeZone(config('app.timezone')));
        $sessionStartTime = $this->startTime;
        $sessionStartTime = $sessionStartTime->format('Y-m-d\TH:i:s');

        $body = [
            "start_time" => $sessionStartTime,
            "timezone" => config('app.timezone'),
            "duration" => (int) $this->duration,
        ];

        if (!$this->title) {
            $body['topic'] = $this->title;
        }

        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $token])
            ->patch(self::ZOOM_API_BASE_URL . "/meetings/" . $this->sessionId, $body);

        if ($response->status() != 204) {
            $errorMessage = !empty($response->json()) && isset($response->json()['message']) ? $response->json()['message'] : '';

            throw new SessionUpdateFailedException($errorMessage);
        }

        $response = $response->json();

        return [
            'id' => $response['id'],
            'join_url' => $response['join_url'],
            'meta' => $response,
        ];
    }

    protected function _delete()
    {
        $token = $this->generateToken();
        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $token])
            ->delete(self::ZOOM_API_BASE_URL . "/meetings/" . $this->sessionId);

        if ($response->status() != 204)
            throw new SessionDeleteFailedException();
    }
}
