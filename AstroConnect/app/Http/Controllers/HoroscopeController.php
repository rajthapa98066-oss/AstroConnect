<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HoroscopeController extends Controller
{
    private function getSigns()
    {
        return [
            ['name' => 'Aries', 'nepali' => 'Mesh', 'dates' => 'Mar 21 - Apr 19'],
            ['name' => 'Taurus', 'nepali' => 'Brish', 'dates' => 'Apr 20 - May 20'],
            ['name' => 'Gemini', 'nepali' => 'Mithun', 'dates' => 'May 21 - Jun 20'],
            ['name' => 'Cancer', 'nepali' => 'Karkat', 'dates' => 'Jun 21 - Jul 22'],
            ['name' => 'Leo', 'nepali' => 'Simha', 'dates' => 'Jul 23 - Aug 22'],
            ['name' => 'Virgo', 'nepali' => 'Kanya', 'dates' => 'Aug 23 - Sep 22'],
            ['name' => 'Libra', 'nepali' => 'Tula', 'dates' => 'Sep 23 - Oct 22'],
            ['name' => 'Scorpio', 'nepali' => 'Brischik', 'dates' => 'Oct 23 - Nov 21'],
            ['name' => 'Sagittarius', 'nepali' => 'Dhanu', 'dates' => 'Nov 22 - Dec 21'],
            ['name' => 'Capricorn', 'nepali' => 'Makar', 'dates' => 'Dec 22 - Jan 19'],
            ['name' => 'Aquarius', 'nepali' => 'Kumbha', 'dates' => 'Jan 20 - Feb 18'],
            ['name' => 'Pisces', 'nepali' => 'Meen', 'dates' => 'Feb 19 - Mar 20'],
        ];
    }

    /**
     * Display the zodiac grid.
     */
    public function index()
    {
        $signs = $this->getSigns();
        return view('pages.user.horoscope', compact('signs'));
    }

    /**
     * Show daily prediction for a specific sign.
     */
    public function show($sign)
    {
        $allSigns = $this->getSigns();
        $signData = collect($allSigns)->first(function ($item) use ($sign) {
            return strtolower($item['name']) === strtolower($sign);
        });

        if (!$signData) {
            return redirect()->route('horoscope')->with('error', 'Sign not found.');
        }

        $cacheKey = 'horoscope_' . strtolower($sign) . '_' . now()->format('Y-m-d');
        
        $data = \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->endOfDay(), function () use ($sign) {
            $apiKey = config('services.api_ninjas.key');
            $url = "https://api.api-ninjas.com/v1/horoscope?zodiac=" . strtolower($sign);

            try {
                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'X-Api-Key' => $apiKey
                ])->withoutVerifying()->get($url);

                if ($response->successful()) {
                    return $response->json();
                }

                \Illuminate\Support\Facades\Log::error('API Ninjas Horoscope Failed', [
                    'sign' => $sign,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return null;
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('API Ninjas Horoscope Exception', [
                    'message' => $e->getMessage()
                ]);
                return null;
            }
        });

        if (!$data) {
            return back()->with('error', 'Celestial connection interrupted. Please try again later.');
        }

        return view('pages.user.horoscope-details', [
            'sign' => ucfirst($sign),
            'nepaliSign' => $signData['nepali'] ?? '',
            'prediction' => $data['horoscope'] ?? $data['prediction'] ?? 'Prediction not available.',
            'date' => $data['date'] ?? now()->format('Y-m-d')
        ]);
    }
}

