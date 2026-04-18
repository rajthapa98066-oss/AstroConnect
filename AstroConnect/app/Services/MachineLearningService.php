<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class MachineLearningService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.machine_learning.base_url');
    }

    /**
     * Call the ML API for Career Prediction
     *
     * @param array $planetarySigns Array of planetary signs (sun_sign, moon_sign, etc.)
     * @return string Predicted career
     * @throws Exception
     */
    public function predictCareer(array $planetarySigns): string
    {
        try {
            $response = Http::timeout(10)
                ->post($this->baseUrl . '/api/v1/predict/career', $planetarySigns);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['status']) && $data['status'] === 'success') {
                    return $data['data']['predicted_career'] ?? 'Unknown Career';
                }
                
                Log::warning('ML API returned unexpected format for career prediction.', ['response' => $data]);
                throw new Exception("Invalid response format from ML model.");
            }

            Log::error('ML API Error for career prediction', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
            $errorMsg = "Prediction engine error ({$response->status()})";
            $data = $response->json();
            if (isset($data['detail'])) {
                $errorMsg = "Prediction Error: " . $data['detail'];
            }
            
            throw new Exception($errorMsg);
            
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('ML API Connection Timeout/Error: ' . $e->getMessage());
            throw new Exception("Prediction service is currently unreachable.");
        }
    }

    /**
     * Call the ML API for Compatibility Prediction
     *
     * @param array $kootaData Array of koota scores and moon signs
     * @return array Compatibility results (prediction, is_compatible, confidence_score)
     * @throws Exception
     */
    public function predictCompatibility(array $kootaData): array
    {
        try {
            $response = Http::timeout(10)
                ->post($this->baseUrl . '/api/v1/predict/compatibility', $kootaData);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['status']) && $data['status'] === 'success') {
                    return $data['data'];
                }

                Log::warning('ML API returned unexpected format for compatibility prediction.', ['response' => $data]);
                throw new Exception("Invalid response format from compatibility engine.");
            }

            Log::error('ML API Error for compatibility prediction', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
            $errorMsg = "Compatibility engine error ({$response->status()})";
            $data = $response->json();
            if (isset($data['detail'])) {
                $errorMsg = "Compatibility Error: " . $data['detail'];
            }

            throw new Exception($errorMsg);
            
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('ML API Connection Timeout/Error: ' . $e->getMessage());
            throw new Exception("Compatibility service is currently unreachable.");
        }
    }
}
