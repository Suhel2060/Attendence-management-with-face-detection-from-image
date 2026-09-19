<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class FaceRecognitionService
{
    protected string $activeApi;
    protected string $primaryUrl;
    protected string $backupUrl;
    protected int $timeout;

    public function __construct()
    {
        $this->activeApi = config('services.face_api_active', 'primary');
        $this->primaryUrl = config('services.face_api.url', 'http://localhost:8001');
        $this->backupUrl = config('services.face_api_backup.url', 'http://localhost:8002');
        $this->timeout = config('services.face_api.timeout', 10);
    }

    public function getActiveUrl(): string
    {
        return $this->activeApi === 'backup' ? $this->backupUrl : $this->primaryUrl;
    }

    public function recognize(string $imageBinary): array
    {
        $response = Http::timeout($this->timeout)
            ->attach('image', $imageBinary, 'capture.jpg')
            ->post($this->getActiveUrl() . '/api/recognize');

        if (!$response->successful()) {
            return [
                'status' => 'error',
                'message' => 'Recognition service unavailable.',
            ];
        }

        return $response->json();
    }

    public function enroll(string $employeeId, array $imagePaths): array
    {
        $this->removeEnrollment($employeeId);

        $results = [];

        $urls = [
            'primary' => ['url' => $this->primaryUrl, 'timeout' => config('services.face_api.enroll_timeout', 60)],
            'backup' => ['url' => $this->backupUrl, 'timeout' => config('services.face_api_backup.enroll_timeout', 60)],
        ];

        foreach ($urls as $name => $cfg) {
            $httpRequest = Http::timeout($cfg['timeout']);
            foreach ($imagePaths as $path) {
                if (file_exists($path)) {
                    $httpRequest->attach('images', file_get_contents($path), basename($path));
                }
            }

            $response = $httpRequest->post($cfg['url'] . '/api/enroll?student_id=' . urlencode($employeeId));

            $results[$name] = $response->successful()
                ? $response->json()
                : ['status' => 'error', 'message' => $response->body()];
        }

        $hasError = false;
        foreach ($results as $name => $result) {
            if (($result['status'] ?? '') === 'error') {
                $hasError = true;
                break;
            }
        }

        return $hasError
            ? ['status' => 'error', 'results' => $results]
            : ['status' => 'enrolled', 'results' => $results, 'student_id' => $employeeId];
    }

    public function removeEnrollment(string $employeeId): array
    {
        $results = [];

        $urls = [
            'primary' => $this->primaryUrl,
            'backup' => $this->backupUrl,
        ];

        foreach ($urls as $name => $url) {
            $response = Http::timeout($this->timeout)
                ->delete($url . '/api/enroll/' . urlencode($employeeId));

            $results[$name] = $response->successful()
                ? $response->json()
                : ['status' => 'error', 'message' => $response->body()];
        }

        return $results;
    }

    public function getEnrolled(): array
    {
        try {
            $response = Http::timeout(3)->get($this->getActiveUrl() . '/api/enrolled');
            if ($response->successful()) {
                return $response->json()['students'] ?? [];
            }
        } catch (Exception $e) {
        }

        return [];
    }

    public function healthCheck(): array
    {
        $results = [];
        foreach (['primary' => $this->primaryUrl, 'backup' => $this->backupUrl] as $name => $url) {
            try {
                $response = Http::timeout(3)->get($url . '/api/health');
                $results[$name] = $response->successful()
                    ? $response->json()
                    : ['status' => 'unreachable'];
            } catch (Exception $e) {
                $results[$name] = ['status' => 'unreachable'];
            }
        }
        return $results;
    }
}
