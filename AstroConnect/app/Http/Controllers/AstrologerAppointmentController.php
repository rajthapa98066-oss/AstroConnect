<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Notifications\AppointmentStatusUpdatedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AstrologerAppointmentController extends Controller
{
    /**
     * Show appointments assigned to the logged-in astrologer.
     */
    public function index(Request $request): View
    {
        $astrologer = $request->user()->astrologer;

        $appointments = Appointment::query()
            ->with('user')
            ->where('astrologer_id', $astrologer->id)
            ->orderBy('scheduled_at')
            ->paginate(15);

        return view('pages.astrologer.appointments', [
            'appointments' => $appointments,
        ]);
    }

    /**
     * Update appointment status, restricted to owning astrologer.
     */
    public function updateStatus(Request $request, Appointment $appointment): RedirectResponse
    {
        $astrologer = $request->user()->astrologer;
        abort_unless($appointment->astrologer_id === $astrologer->id, 403);

        $validated = $request->validate([
            'status' => ['required', 'in:pending,confirmed,rejected,completed,cancelled'],
        ]);

        $appointment->update([
            'status' => $validated['status'],
        ]);

        if ($appointment->wasChanged('status') && $appointment->user) {
            $appointment->loadMissing('astrologer.user');
            $appointment->user->notify(new AppointmentStatusUpdatedNotification($appointment));
        }

        return redirect()
            ->route('astrologer.appointments')
            ->with('status', 'appointment-status-updated');
    }
}
