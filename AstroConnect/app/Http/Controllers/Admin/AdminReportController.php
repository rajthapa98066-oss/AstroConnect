<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AstrologerReport;
use App\Notifications\AstrologerReportResolvedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class AdminReportController extends Controller
{
    /**
     * Show reports submitted by verified users.
     */
    public function index(): View
    {
        $reports = AstrologerReport::query()
            ->with(['reporter', 'astrologer.user', 'reviewedBy'])
            ->latest()
            ->paginate(20);

        return view('pages.admin.reports-management', [
            'reports' => $reports,
        ]);
    }

    /**
     * Mark a report as flagged and keep the astrologer visible.
     */
    public function flag(AstrologerReport $report): RedirectResponse
    {
        $this->applyModeration($report, 'flag', 'flagged', 'Report flagged and astrologer marked for review.');

        return Redirect::route('admin.reports.index')->with('status', 'report-flagged');
    }

    /**
     * Disable the astrologer account from the report.
     */
    public function disable(AstrologerReport $report): RedirectResponse
    {
        $this->applyModeration($report, 'disable', 'disabled', 'Report resolved by disabling the astrologer account.');

        return Redirect::route('admin.reports.index')->with('status', 'report-disabled');
    }

    /**
     * Delete the reported astrologer account.
     */
    public function deleteAstrologer(AstrologerReport $report): RedirectResponse
    {
        DB::transaction(function () use ($report): void {
            $this->applyModeration($report, 'delete', 'resolved', 'Reported astrologer account deleted.');

            if ($report->astrologer?->user) {
                $report->astrologer->user->delete();
            }
        });

        return Redirect::route('admin.reports.index')->with('status', 'report-account-deleted');
    }

    /**
     * Persist moderation changes on a report and astrologer.
     */
    private function applyModeration(AstrologerReport $report, string $resolution, string $moderationStatus, string $adminNote): void
    {
        $report->loadMissing('astrologer');

        if ($report->astrologer) {
            $report->astrologer->update(['moderation_status' => $moderationStatus]);
        }

        $report->update([
            'status' => 'resolved',
            'resolution' => $resolution,
            'reviewed_by_admin_id' => request()->user()?->id,
            'reviewed_at' => now(),
            'admin_note' => $adminNote,
        ]);

        $report->loadMissing(['reporter', 'astrologer.user']);

        if ($report->reporter) {
            $report->reporter->notify(new AstrologerReportResolvedNotification($report));
        }
    }
}
