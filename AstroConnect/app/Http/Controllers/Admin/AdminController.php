<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Astrologer;
use App\Models\AstrologerReport;
use App\Models\Blog;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Carbon;

class AdminController extends Controller
{
    /**
     * Render admin dashboard landing page.
     */
    public function AdminDashboard()
    {
        $kpis = [
            'admins' => User::query()->where('role', 'admin')->count(),
            'users' => User::query()->where('role', 'user')->count(),
            'astrologers_total' => Astrologer::query()->count(),
            'astrologers_pending' => Astrologer::query()->where('verification_status', 'pending')->count(),
            'pending_reports' => AstrologerReport::query()->where('status', 'pending')->count(),
            'pending_blog_reviews' => Blog::query()->where('review_status', 'pending')->count(),
            'appointments_total' => Appointment::query()->count(),
            'completed_payments' => Payment::query()->where('status', 'completed')->count(),
            'total_revenue' => (float) Payment::query()->where('status', 'completed')->sum('amount'),
        ];

        $statusOrder = ['pending', 'confirmed', 'completed', 'rejected', 'cancelled'];
        $statusCounts = Appointment::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $appointmentStatusSeries = collect($statusOrder)
            ->map(fn (string $status): int => (int) ($statusCounts[$status] ?? 0))
            ->all();

        $monthStart = now()->copy()->startOfMonth()->subMonths(5);
        $monthKeys = [];
        $monthLabels = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->copy()->subMonths($i);
            $monthKeys[] = $month->format('Y-m');
            $monthLabels[] = $month->format('M Y');
        }

        $appointmentsByMonth = Appointment::query()
            ->where('created_at', '>=', $monthStart)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month_key, COUNT(*) as total")
            ->groupBy('month_key')
            ->pluck('total', 'month_key');

        $revenueByMonth = Payment::query()
            ->where('status', 'completed')
            ->where('created_at', '>=', $monthStart)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month_key, SUM(amount) as total")
            ->groupBy('month_key')
            ->pluck('total', 'month_key');

        $dashboardCharts = [
            'labels' => $monthLabels,
            'appointments' => collect($monthKeys)->map(fn (string $key): int => (int) ($appointmentsByMonth[$key] ?? 0))->all(),
            'revenue' => collect($monthKeys)->map(fn (string $key): float => (float) ($revenueByMonth[$key] ?? 0))->all(),
        ];

        $recentApplications = Astrologer::query()
            ->with('user')
            ->latest()
            ->limit(5)
            ->get();

        $recentReports = AstrologerReport::query()
            ->with(['reporter', 'astrologer.user'])
            ->latest()
            ->limit(5)
            ->get();

        $recentPayments = Payment::query()
            ->with(['appointment.user', 'appointment.astrologer.user'])
            ->latest()
            ->limit(5)
            ->get();

        return view('pages.admin.dashboard', [
            'kpis' => $kpis,
            'appointmentStatusLabels' => array_map(fn (string $status): string => ucfirst($status), $statusOrder),
            'appointmentStatusSeries' => $appointmentStatusSeries,
            'dashboardCharts' => $dashboardCharts,
            'recentApplications' => $recentApplications,
            'recentReports' => $recentReports,
            'recentPayments' => $recentPayments,
            'generatedAt' => Carbon::now(),
        ]);
    }

    /**
     * List non-admin users and unapproved astrologer accounts.
     */
    public function usersIndex()
    {
        $users = User::with('astrologer')
            ->where('role', '!=', 'admin')
            ->whereDoesntHave('astrologer', function ($query) {
                $query->where('verification_status', 'approved');
            })
            ->latest()
            ->paginate(15);

        return view('pages.admin.users-management', [
            'users' => $users,
        ]);
    }
}
