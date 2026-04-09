<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Astrologer;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class AppointmentController extends Controller
{
    /**
     * Create a new appointment request for an approved astrologer.
     */
    public function store(Request $request, Astrologer $astrologer): RedirectResponse
    {
        abort_unless($request->user()?->canAccessUserPanel(), 403);
        abort_if($astrologer->verification_status !== 'approved', 404);

        $validated = $request->validate([
            'scheduled_at' => ['required', 'date', 'after:now'],
            'topic' => ['required', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        Appointment::create([
            'user_id' => $request->user()->id,
            'astrologer_id' => $astrologer->id,
            'scheduled_at' => $validated['scheduled_at'],
            'topic' => $validated['topic'],
            'message' => $validated['message'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()
            ->route('astrologers.show', $astrologer)
            ->with('status', 'appointment-booked');
    }

    /**
     * List appointments booked by the currently authenticated user.
     */
    public function userIndex(Request $request): View
    {
        abort_unless($request->user()?->canAccessUserPanel(), 403);

        $appointments = Appointment::query()
            ->with('astrologer.user')
            ->where('user_id', $request->user()->id)
            ->orderByDesc('scheduled_at')
            ->paginate(10);

        $myReviews = Review::query()
            ->where('user_id', $request->user()->id)
            ->get()
            ->keyBy('astrologer_id');

        return view('pages.user.appointments-list', [
            'appointments' => $appointments,
            'myReviews' => $myReviews,
        ]);
    }

    /**
     * Store or update a session rating after a completed appointment.
     */
    public function rate(Request $request, Appointment $appointment): RedirectResponse
    {
        abort_unless($request->user()?->canAccessUserPanel(), 403);
        abort_unless($appointment->user_id === $request->user()->id, 403);

        if ($appointment->status !== 'completed') {
            throw ValidationException::withMessages([
                'rating' => 'You can only rate a completed session.',
            ]);
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        $appointment->update([
            'rating' => $validated['rating'],
            'rated_at' => now(),
        ]);

        return redirect()
            ->route('appointments.user.index')
            ->with('status', 'session-rating-saved');
    }
}
