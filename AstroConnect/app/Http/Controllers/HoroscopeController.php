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
        $apiKey = config('services.api_ninjas.key');
        $allSigns = $this->getSigns();
        
        // Find the specific sign data
        $signData = collect($allSigns)->first(function ($item) use ($sign) {
            return strtolower($item['name']) === strtolower($sign);
        });

        // The API explicitly asked for 'zodiac' instead of 'sign'
        $url = "https://api.api-ninjas.com/v1/horoscope?zodiac=" . strtolower($sign);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'X-Api-Key: ' . $apiKey
        ));

        $output = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($httpCode === 200) {
            $data = json_decode($output, true);
            return view('pages.user.horoscope-details', [
                'sign' => ucfirst($sign),
                'nepaliSign' => $signData['nepali'] ?? '',
                'prediction' => $data['horoscope'] ?? $data['prediction'] ?? 'Prediction not available.',
                'date' => $data['date'] ?? now()->format('Y-m-d')
            ]);
        }

        Log::error('API Ninjas Horoscope Failed (Native CURL)', [
            'sign' => $sign,
            'http_code' => $httpCode,
            'error' => $error,
            'response' => $output
        ]);
        
        if ($httpCode !== 200) {
            dd([
                'error' => 'API Request Failed',
                'http_code' => $httpCode,
                'curl_error' => $error,
                'response_body' => $output
            ]);
        }

        return back()->with('error', 'Celestial connection interrupted. Please ensure your API key is correct.');
    }
}
