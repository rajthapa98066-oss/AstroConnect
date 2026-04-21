<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Astrologer;
use App\Notifications\AstrologerApplicationReviewedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class AdminAstrologerController extends Controller
{
    /**
     * Show approved astrologer profiles.
     */
    public function index(): View
    {
        $astrologers = Astrologer::query()
            ->with('user')
            ->where('verification_status', 'approved')
            ->latest()
            ->paginate(20);

        return view('pages.admin.astrologers-management', [
            'astrologers' => $astrologers,
        ]);
    }

    /**
     * Show astrologer application queue for admin review.
     */
    public function applications(): View
    {
        $astrologers = Astrologer::with('user')->latest()->paginate(20);

        return view('pages.admin.astrologer-applications-management', [
            'astrologers' => $astrologers,
        ]);
    }

    /**
     * Approve an astrologer application.
     */
    public function approve(Astrologer $astrologer): RedirectResponse
    {
        if ($astrologer->user) {
            $astrologer->user->forceFill(['role' => 'astrologer'])->save();
        }

        $astrologer->update([
            'verification_status' => 'approved',
            'moderation_status' => 'active',
        ]);

        $astrologer->loadMissing('user');

        if ($astrologer->user) {
            $astrologer->user->notify(new AstrologerApplicationReviewedNotification($astrologer));
        }

        return Redirect::route('admin.astrologer-applications.index')->with('status', 'astrologer-approved');
    }

    /**
     * Reject an astrologer application.
     */
    public function reject(Astrologer $astrologer): RedirectResponse
    {
        if ($astrologer->user) {
            $astrologer->user->forceFill(['role' => 'user'])->save();
        }

        $astrologer->update(['verification_status' => 'rejected']);

        $astrologer->loadMissing('user');

        if ($astrologer->user) {
            $astrologer->user->notify(new AstrologerApplicationReviewedNotification($astrologer));
        }

        return Redirect::route('admin.astrologer-applications.index')->with('status', 'astrologer-rejected');
    }

    /**
     * Update editable astrologer fields from admin panel.
     */
    public function update(Request $request, Astrologer $astrologer): RedirectResponse
    {
        $validated = $request->validate([
            'specialization' => ['required', 'string', 'max:255'],
            'experience_years' => ['required', 'integer', 'min:0', 'max:80'],
            'consultation_fee' => ['required', 'numeric', 'min:0'],
            'availability_status' => ['required', 'in:available,busy,unavailable'],
            'verification_status' => ['required', 'in:pending,approved,rejected'],
        ]);

        $astrologer->update($validated);

        return Redirect::route('admin.astrologers.index')->with('status', 'astrologer-updated');
    }
}
