<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Astrologer;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    /**
     * Store or update a review for an approved astrologer.
     */
    public function store(Request $request, Astrologer $astrologer): RedirectResponse
    {
        abort_if($astrologer->verification_status !== 'approved', 404);
        abort_unless($request->user()?->canAccessUserPanel(), 403);

        $validated = $request->validate([
            'appointment_id' => ['required', 'integer'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'max:1000'],
            'redirect_to' => ['nullable', 'in:profile,appointments'],
        ]);

        $appointment = Appointment::query()
            ->where('id', $validated['appointment_id'])
            ->where('astrologer_id', $astrologer->id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $appointment || $appointment->status !== 'completed') {
            throw ValidationException::withMessages([
                'comment' => 'You can submit a review only after completing the appointment.',
            ]);
        }

        Review::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'astrologer_id' => $astrologer->id,
            ],
            [
                'rating' => $validated['rating'],
                'comment' => $validated['comment'],
            ]
        );

        $appointment->update([
            'rating' => $validated['rating'],
            'rated_at' => now(),
        ]);

        if (($validated['redirect_to'] ?? null) === 'appointments') {
            return redirect()
                ->route('appointments.user.index')
                ->with('status', 'review-saved');
        }

        return redirect()
            ->route('astrologers.show', $astrologer)
            ->with('status', 'review-saved');
    }
}
