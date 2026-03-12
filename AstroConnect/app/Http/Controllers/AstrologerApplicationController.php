<?php

namespace App\Http\Controllers;

use App\Models\Astrologer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class AstrologerApplicationController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        $astrologer = $request->user()->astrologer;

        if ($astrologer?->verification_status === 'approved') {
            return Redirect::route('astrologer.dashboard');
        }

        return view('pages.astrologer.apply', [
            'astrologer' => $astrologer,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'specialization' => ['required', 'string', 'max:255'],
            'experience_years' => ['required', 'integer', 'min:0', 'max:80'],
            'bio' => ['required', 'string', 'max:2000'],
            'consultation_fee' => ['required', 'numeric', 'min:0'],
            'availability_status' => ['required', 'in:available,busy,unavailable'],
            'profile_photo' => ['nullable', 'image', 'max:2048'],
        ]);

        $astrologer = $request->user()->astrologer;

        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo'] = $request->file('profile_photo')->store('astrologers/photos', 'public');
        } elseif ($astrologer) {
            $validated['profile_photo'] = $astrologer->profile_photo;
        }

        $validated['verification_status'] = 'pending';

        Astrologer::updateOrCreate(
            ['user_id' => $request->user()->id],
            $validated
        );

        return Redirect::route('astrologer.apply')->with('status', 'application-submitted');
    }
}
