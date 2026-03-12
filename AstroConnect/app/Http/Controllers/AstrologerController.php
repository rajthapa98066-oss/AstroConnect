<?php

namespace App\Http\Controllers;

use App\Models\Astrologer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AstrologerController extends Controller
{
    public function index(): View
    {
        $astrologers = Astrologer::with('user')
            ->where('verification_status', 'approved')
            ->latest()
            ->paginate(12);

        return view('pages.astrologers.index', [
            'astrologers' => $astrologers,
        ]);
    }

    public function show(Astrologer $astrologer): View
    {
        abort_if($astrologer->verification_status !== 'approved', 404);

        $astrologer->load('user');

        return view('pages.astrologers.show', [
            'astrologer' => $astrologer,
        ]);
    }

    public function dashboard(Request $request): View
    {
        return view('pages.astrologer.dashboard', [
            'astrologer' => $request->user()->astrologer,
        ]);
    }

    public function profile(Request $request): View
    {
        return view('pages.astrologer.profile', [
            'astrologer' => $request->user()->astrologer,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $astrologer = $request->user()->astrologer;

        $validated = $request->validate([
            'specialization' => ['required', 'string', 'max:255'],
            'experience_years' => ['required', 'integer', 'min:0', 'max:80'],
            'bio' => ['required', 'string', 'max:2000'],
            'consultation_fee' => ['required', 'numeric', 'min:0'],
            'availability_status' => ['required', 'in:available,busy,unavailable'],
            'profile_photo' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('profile_photo')) {
            if ($astrologer->profile_photo) {
                Storage::disk('public')->delete($astrologer->profile_photo);
            }

            $validated['profile_photo'] = $request->file('profile_photo')->store('astrologers/photos', 'public');
        }

        $astrologer->update($validated);

        return Redirect::route('astrologer.profile')->with('status', 'profile-updated');
    }

    public function appointments(): View
    {
        return view('pages.astrologer.appointments');
    }
}
