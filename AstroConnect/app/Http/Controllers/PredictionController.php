<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AstrologyAPIService;
use App\Services\MachineLearningService;

class PredictionController extends Controller
{
    protected $astrologyService;
    protected $mlService;

    public function __construct(AstrologyAPIService $astrologyService, MachineLearningService $mlService)
    {
        $this->astrologyService = $astrologyService;
        $this->mlService = $mlService;
    }

    /**
     * Show the Compatibility Prediction Form
     */
    public function showCompatibilityForm()
    {
        return view('pages.predictions.compatibility');
    }

    /**
     * Process the Compatibility Prediction
     */
    public function processCompatibility(Request $request)
    {
        // 1. Validate both persons birth details
        $validated = $request->validate([
            'p1_dob' => 'required|date',
            'p1_time' => 'required',
            'p1_lat' => 'required|numeric',
            'p1_lon' => 'required|numeric',
            'p1_tzone' => 'required|numeric',

            'p2_dob' => 'required|date',
            'p2_time' => 'required',
            'p2_lat' => 'required|numeric',
            'p2_lon' => 'required|numeric',
            'p2_tzone' => 'required|numeric',
        ]);

        try {
            // Parse Persons Details
            $t1 = strtotime($validated['p1_dob'] . ' ' . $validated['p1_time']);
            $p1 = [
                'day' => (int)date('d', $t1), 'month' => (int)date('m', $t1), 'year' => (int)date('Y', $t1),
                'hour' => (int)date('H', $t1), 'min' => (int)date('i', $t1),
                'lat' => $validated['p1_lat'], 'lon' => $validated['p1_lon'], 'tzone' => $validated['p1_tzone']
            ];

            $t2 = strtotime($validated['p2_dob'] . ' ' . $validated['p2_time']);
            $p2 = [
                'day' => (int)date('d', $t2), 'month' => (int)date('m', $t2), 'year' => (int)date('Y', $t2),
                'hour' => (int)date('H', $t2), 'min' => (int)date('i', $t2),
                'lat' => $validated['p2_lat'], 'lon' => $validated['p2_lon'], 'tzone' => $validated['p2_tzone']
            ];

            // 2. Fetch Koota points from Astrology API Bridge
            $kootaData = $this->astrologyService->getKootaPoints($p1, $p2);

            // 3. Pass Koota Data to Machine Learning Service
            $predictionResult = $this->mlService->predictCompatibility($kootaData);

            // 4. Return result view
            return view('pages.predictions.compatibility-result', compact('predictionResult', 'kootaData'));

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}
