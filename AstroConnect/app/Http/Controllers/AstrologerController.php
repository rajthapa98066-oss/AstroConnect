<?php

namespace App\Http\Controllers;

use App\Models\Astrologer;
use App\Models\Appointment;
use App\Models\Blog;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AstrologerController extends Controller
{
    /**
     * Display public list of approved astrologers.
     */
    public function index(): View
    {
        $astrologers = Astrologer::with('user')
            ->where('verification_status', 'approved')
            ->latest()
            ->paginate(12);

        return view('pages.user.astrologers-list', [
            'astrologers' => $astrologers,
        ]);
    }

    /**
     * Display public profile for one approved astrologer.
     */
    public function show(Request $request, Astrologer $astrologer): View
    {
        abort_if($astrologer->verification_status !== 'approved', 404);

        $astrologer = Astrologer::query()
            ->with('user')
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->findOrFail($astrologer->id);

        $reviews = Review::query()
            ->with('user')
            ->where('astrologer_id', $astrologer->id)
            ->latest()
            ->get();

        $myReview = null;

        if ($request->user()?->role === 'user') {
            $myReview = Review::query()
                ->where('astrologer_id', $astrologer->id)
                ->where('user_id', $request->user()->id)
                ->first();
        }

        return view('pages.user.astrologer-profile', [
            'astrologer' => $astrologer,
            'reviews' => $reviews,
            'myReview' => $myReview,
        ]);
    }

    /**
     * Show astrologer dashboard metrics (appointments + blog stats).
     */
    public function dashboard(Request $request): View
    {
        $astrologer = $request->user()->astrologer;

        $appointmentCounts = Appointment::query()
            ->where('astrologer_id', $astrologer->id)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $recentAppointments = Appointment::query()
            ->with('user')
            ->where('astrologer_id', $astrologer->id)
            ->orderByDesc('scheduled_at')
            ->limit(5)
            ->get();

        $blogCounts = Blog::query()
            ->where('astrologer_id', $astrologer->id)
            ->selectRaw('review_status, COUNT(*) as total')
            ->groupBy('review_status')
            ->pluck('total', 'review_status');

        return view('pages.astrologer.dashboard', [
            'astrologer' => $astrologer,
            'appointmentCounts' => $appointmentCounts,
            'recentAppointments' => $recentAppointments,
            'blogCounts' => $blogCounts,
        ]);
    }

    /**
     * Show logged-in astrologer profile form.
     */
    public function profile(Request $request): View
    {
        return view('pages.astrologer.profile', [
            'astrologer' => $request->user()->astrologer,
        ]);
    }

    /**
     * Update astrologer profile details and optional profile photo.
     */
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

    /**
     * Legacy route view for astrologer appointments page.
     */
    public function appointments(): View
    {
        return view('pages.astrologer.appointments');
    }
}
