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
            ->where(function ($query) {
                $query->whereNull('moderation_status')->orWhere('moderation_status', '!=', 'disabled');
            })
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
        abort_if($astrologer->verification_status !== 'approved' || $astrologer->isDisabled(), 404);

        $astrologer = Astrologer::query()
            ->with('user')
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->withCount(['appointments as rated_sessions_count' => function ($query) {
                $query->whereNotNull('rating');
            }])
            ->withAvg('appointments', 'rating')
            ->findOrFail($astrologer->id);

        $reviews = Review::query()
            ->with('user')
            ->where('astrologer_id', $astrologer->id)
            ->latest()
            ->get();

        $myReview = null;
        $reviewAppointmentId = null;
        $hasCompletedSession = false;

        if ($request->user()?->role === 'user') {
            $myReview = Review::query()
                ->where('astrologer_id', $astrologer->id)
                ->where('user_id', $request->user()->id)
                ->first();

            $completedAppointment = Appointment::query()
                ->where('astrologer_id', $astrologer->id)
                ->where('user_id', $request->user()->id)
                ->where('status', 'completed')
                ->latest('scheduled_at')
                ->first();

            $hasCompletedSession = $completedAppointment !== null;
            $reviewAppointmentId = $completedAppointment?->id;
        }

        return view('pages.user.astrologer-profile', [
            'astrologer' => $astrologer,
            'reviews' => $reviews,
            'myReview' => $myReview,
            'hasCompletedSession' => $hasCompletedSession,
            'reviewAppointmentId' => $reviewAppointmentId,
        ]);
    }

    /**
     * Show astrologer dashboard metrics (appointments + blog stats).
     */
    public function dashboard(Request $request): View
    {
        abort_unless($request->user()?->canAccessAstrologerPanel(), 403);

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
        abort_unless($request->user()?->canAccessAstrologerPanel(), 403);

        $astrologer = Astrologer::query()
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->withCount(['appointments as rated_sessions_count' => function ($query) {
                $query->whereNotNull('rating');
            }])
            ->withAvg('appointments', 'rating')
            ->findOrFail($request->user()->astrologer->id);

        return view('pages.astrologer.profile', [
            'astrologer' => $astrologer,
        ]);
    }

    /**
     * Update astrologer profile details and optional profile photo.
     */
    public function update(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->canAccessAstrologerPanel(), 403);

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
