<?php

namespace App\Http\Controllers;

use App\Models\Astrologer;
use App\Models\User;
use App\Notifications\AstrologerApplicationSubmittedAdminNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class AstrologerApplicationController extends Controller
{
    /**
     * Show the astrologer application form or redirect approved astrologers.
     */
    public function create(Request $request): View|RedirectResponse
    {
        abort_unless($request->user()?->canAccessUserPanel(), 403);

        $astrologer = $request->user()->astrologer;

        if ($astrologer?->verification_status === 'approved') {
            return Redirect::route('astrologer.dashboard');
        }

        return view('pages.astrologer.apply', [
            'astrologer' => $astrologer,
        ]);
    }

    /**
     * Submit or update astrologer application details.
     */
    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->canAccessUserPanel(), 403);

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

        $astrologer = Astrologer::updateOrCreate(
            ['user_id' => $request->user()->id],
            $validated
        );

        $astrologer->loadMissing('user');

        User::query()
            ->where('role', 'admin')
            ->get()
            ->each(function (User $admin) use ($astrologer): void {
                $admin->notify(new AstrologerApplicationSubmittedAdminNotification($astrologer));
            });

        return Redirect::route('astrologer.apply')->with('status', 'application-submitted');
    }
}
