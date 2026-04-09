<?php

namespace App\Http\Controllers;

use App\Models\Astrologer;
use App\Models\AstrologerReport;
use App\Models\User;
use App\Notifications\AstrologerReportSubmittedAdminNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class AstrologerReportController extends Controller
{
    /**
     * Store or update a report for an approved astrologer.
     */
    public function store(Request $request, Astrologer $astrologer): RedirectResponse
    {
        abort_unless($request->user()?->canAccessUserPanel() && $request->user()?->hasVerifiedEmail(), 403);
        abort_if($astrologer->verification_status !== 'approved' || $astrologer->isDisabled(), 404);

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:100'],
            'details' => ['nullable', 'string', 'max:2000'],
        ]);

        $report = AstrologerReport::updateOrCreate(
            [
                'reporter_user_id' => $request->user()->id,
                'astrologer_id' => $astrologer->id,
            ],
            [
                'reason' => $validated['reason'],
                'details' => $validated['details'] ?? null,
                'status' => 'pending',
                'resolution' => null,
                'reviewed_by_admin_id' => null,
                'reviewed_at' => null,
                'admin_note' => null,
            ]
        )->loadMissing(['reporter', 'astrologer.user']);

        User::query()
            ->where('role', 'admin')
            ->get()
            ->each(function (User $admin) use ($report): void {
                $admin->notify(new AstrologerReportSubmittedAdminNotification($report));
            });

        return Redirect::route('astrologers.show', $astrologer)->with('status', 'report-submitted');
    }
}
