<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Astrologer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    /**
     * Create a new appointment request for an approved astrologer.
     */
    public function store(Request $request, Astrologer $astrologer): RedirectResponse
    {
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
        $appointments = Appointment::query()
            ->with('astrologer.user')
            ->where('user_id', $request->user()->id)
            ->orderByDesc('scheduled_at')
            ->paginate(10);

        return view('pages.user.appointments-list', [
            'appointments' => $appointments,
        ]);
    }
}
