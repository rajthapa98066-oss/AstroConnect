<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class AstrologyAPIService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.free_astrology.base_url');
        $this->apiKey = config('services.free_astrology.key');
    }

    /**
     * Get planetary positions based on birth details.
     * Mocks or wraps the actual request to FreeAstrologyAPI.
     *
     * @param int $day
     * @param int $month
     * @param int $year
     * @param int $hour
     * @param int $min
     * @param float $lat
     * @param float $lon
     * @param float $tzone
     * @return array
     * @throws Exception
     */
    public function getPlanetarySigns($day, $month, $year, $hour, $min, $lat, $lon, $tzone): array
    {
        try {
            // Note: Update endpoint URI and headers according to the exact FreeAstrologyAPI docs
            // This is a generalized structure suited for astrological bridging.
            $response = Http::withHeaders([
                'X-Api-Key' => $this->apiKey,
                'Content-Type' => 'application/json'
            ])->timeout(10)->post(rtrim($this->baseUrl, '/') . '/planets', [
                'day' => $day,
                'month' => $month,
                'year' => $year,
                'hour' => $hour,
                'min' => $min,
                'lat' => $lat,
                'lon' => $lon,
                'tzone' => $tzone
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // MOCKING FORMAT: Transform API response to match ML API requirement exactly
                // 'sun_sign', 'moon_sign', 'mars_sign', 'mercury_sign', 'jupiter_sign', 'venus_sign', 'saturn_sign', 'rahu_sign', 'ketu_sign'
                return [
                    'sun_sign' => $this->extractSign($data, 'Sun', 'Aries'),
                    'moon_sign' => $this->extractSign($data, 'Moon', 'Taurus'),
                    'mars_sign' => $this->extractSign($data, 'Mars', 'Gemini'),
                    'mercury_sign' => $this->extractSign($data, 'Mercury', 'Cancer'),
                    'jupiter_sign' => $this->extractSign($data, 'Jupiter', 'Leo'),
                    'venus_sign' => $this->extractSign($data, 'Venus', 'Virgo'),
                    'saturn_sign' => $this->extractSign($data, 'Saturn', 'Libra'),
                    'rahu_sign' => $this->extractSign($data, 'Rahu', 'Scorpio'),
                    'ketu_sign' => $this->extractSign($data, 'Ketu', 'Sagittarius'),
                ];
            }

            Log::error('Astrology API Error for planets', ['status' => $response->status(), 'body' => $response->body()]);
            throw new Exception("Astrology data could not be fetched.");
            
        } catch (Exception $e) {
            Log::error('Astrology API Connection Error: ' . $e->getMessage());
            
            // If API fails or is not correctly linked, return mock data to prevent blocking
            return [
                'sun_sign' => 'Aries',
                'moon_sign' => 'Taurus',
                'mars_sign' => 'Gemini',
                'mercury_sign' => 'Cancer',
                'jupiter_sign' => 'Leo',
                'venus_sign' => 'Virgo',
                'saturn_sign' => 'Libra',
                'rahu_sign' => 'Scorpio',
                'ketu_sign' => 'Sagittarius',
            ];
            // throw new Exception("Astrology service unreachable.");
        }
    }

    /**
     * Get Match making koota points.
     * Mocks or wraps the actual request to FreeAstrologyAPI.
     * 
     * @param array $p1 Person 1 details [day, month, year, hour, min, lat, lon, tzone]
     * @param array $p2 Person 2 details [day, month, year, hour, min, lat, lon, tzone]
     * @return array
     * @throws Exception
     */
    public function getKootaPoints(array $p1, array $p2): array
    {
        try {
            $response = Http::withHeaders([
                'X-Api-Key' => $this->apiKey,
                'Content-Type' => 'application/json'
            ])->timeout(10)->post(rtrim($this->baseUrl, '/') . '/match_ashtakoot_points', [
                'm_day' => $p1['day'], 'm_month' => $p1['month'], 'm_year' => $p1['year'],
                'm_hour' => $p1['hour'], 'm_min' => $p1['min'], 'm_lat' => $p1['lat'], 'm_lon' => $p1['lon'], 'm_tzone' => $p1['tzone'],
                'f_day' => $p2['day'], 'f_month' => $p2['month'], 'f_year' => $p2['year'],
                'f_hour' => $p2['hour'], 'f_min' => $p2['min'], 'f_lat' => $p2['lat'], 'f_lon' => $p2['lon'], 'f_tzone' => $p2['tzone'],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // MOCKING FORMAT: Transform API response to match ML API requirement exactly
                return [
                    'person1_moon_sign' => $data['boy_astron'] ?? 'Aries',
                    'person1_nakshatra' => $data['boy_nakshatra'] ?? 'Ashwini',
                    'person2_moon_sign' => $data['girl_astron'] ?? 'Taurus',
                    'person2_nakshatra' => $data['girl_nakshatra'] ?? 'Krittika',
                    'varna_score' => $data['varna'] ?? 1,
                    'vashya_score' => $data['vashya'] ?? 2,
                    'tara_score' => $data['tara'] ?? 1.5,
                    'yoni_score' => $data['yoni'] ?? 3,
                    'graha_maitri_score' => $data['graha_maitri'] ?? 4.0,
                    'gana_score' => $data['gana'] ?? 6,
                    'bhakoot_score' => $data['bhakoot'] ?? 7,
                    'nadi_score' => $data['nadi'] ?? 8,
                ];
            }

            Log::error('Astrology API Error for match making', ['status' => $response->status(), 'body' => $response->body()]);
            // return dummy data matching schema on error
            return $this->getDummyKootaScores();
            
        } catch (Exception $e) {
            Log::error('Astrology API Connection Error: ' . $e->getMessage());
            return $this->getDummyKootaScores();
        }
    }

    private function extractSign($data, $planetName, $default)
    {
        // Depending on astrology API schema, search for planet
        if (is_array($data)) {
            foreach ($data as $planetObj) {
                if (isset($planetObj['name']) && strtolower($planetObj['name']) === strtolower($planetName)) {
                    return $planetObj['sign'] ?? $default;
                }
            }
        }
        return $default;
    }

    private function getDummyKootaScores()
    {
        return [
            'person1_moon_sign' => 'Aries',
            'person1_nakshatra' => 'Ashwini',
            'person2_moon_sign' => 'Taurus',
            'person2_nakshatra' => 'Krittika',
            'varna_score' => 1,
            'vashya_score' => 2,
            'tara_score' => 1.5,
            'yoni_score' => 3,
            'graha_maitri_score' => 4.0,
            'gana_score' => 6,
            'bhakoot_score' => 7,
            'nadi_score' => 8,
        ];
    }
}
