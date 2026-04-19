{{-- View: resources\views\pages\admin\dashboard.blade.php --}}
@extends('layouts.admin.master')

@section('admin')
    <div class="container-xxl">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Dashboard</h4>
                <p class="text-muted mb-0">Live operational summary for AstroConnect admin tools.</p>
            </div>
            <div class="text-muted small mt-2 mt-sm-0">
                Last updated: {{ $generatedAt->format('M d, Y h:i A') }}
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="fs-14 mb-1">Total Admins</div>
                                <div class="fs-22 mb-0 fw-semibold text-black">{{ $kpis['admins'] }}</div>
                            </div>
                            <i data-feather="shield" class="text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="fs-14 mb-1">Registered Users</div>
                                <div class="fs-22 mb-0 fw-semibold text-black">{{ $kpis['users'] }}</div>
                            </div>
                            <i data-feather="users" class="text-info"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="fs-14 mb-1">Astrologers</div>
                                <div class="fs-22 mb-0 fw-semibold text-black">{{ $kpis['astrologers_total'] }}</div>
                                <div class="small text-muted">Pending: {{ $kpis['astrologers_pending'] }}</div>
                            </div>
                            <i data-feather="user-check" class="text-success"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="fs-14 mb-1">Pending Reports</div>
                                <div class="fs-22 mb-0 fw-semibold text-black">{{ $kpis['pending_reports'] }}</div>
                                <div class="small text-muted">Pending Blog Reviews: {{ $kpis['pending_blog_reviews'] }}</div>
                            </div>
                            <i data-feather="alert-triangle" class="text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="fs-14 mb-1">Total Appointments</div>
                                <div class="fs-22 mb-0 fw-semibold text-black">{{ $kpis['appointments_total'] }}</div>
                                <div class="small text-muted">Paid Sessions: {{ $kpis['completed_payments'] }}</div>
                            </div>
                            <i data-feather="calendar" class="text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="fs-14 mb-1">Total Revenue</div>
                                <div class="fs-22 mb-0 fw-semibold text-black">NPR {{ number_format($kpis['total_revenue'], 2) }}</div>
                                <div class="small text-muted">Completed payments only</div>
                            </div>
                            <i data-feather="credit-card" class="text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-xl-8">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Appointments and Revenue (Last 6 Months)</h5>
                    </div>
                    <div class="card-body">
                        <div id="appointments-revenue-trend" class="apex-charts" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Appointment Status Breakdown</h5>
                    </div>
                    <div class="card-body">
                        <div id="appointment-status-breakdown" class="apex-charts" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-xl-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Recent Astrologer Applications</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Status</th>
                                        <th>Submitted</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($recentApplications as $application)
                                        <tr>
                                            <td>{{ $application->user?->name ?? 'Deleted user' }}</td>
                                            <td>
                                                @if ($application->verification_status === 'approved')
                                                    <span class="badge bg-success">Approved</span>
                                                @elseif ($application->verification_status === 'rejected')
                                                    <span class="badge bg-danger">Rejected</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                @endif
                                            </td>
                                            <td>{{ $application->created_at?->format('M d, Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">No applications found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Recent Reports</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Astrologer</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($recentReports as $report)
                                        <tr>
                                            <td>{{ $report->astrologer?->user?->name ?? 'Deleted astrologer' }}</td>
                                            <td>
                                                @if ($report->status === 'pending')
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                @else
                                                    <span class="badge bg-success">Resolved</span>
                                                @endif
                                            </td>
                                            <td>{{ $report->created_at?->format('M d, Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">No reports found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Recent Payments</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Status</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($recentPayments as $payment)
                                        <tr>
                                            <td>{{ $payment->appointment?->user?->name ?? 'Deleted user' }}</td>
                                            <td>
                                                @if ($payment->status === 'completed')
                                                    <span class="badge bg-success">Completed</span>
                                                @elseif ($payment->status === 'pending')
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                @else
                                                    <span class="badge bg-danger">{{ ucfirst((string) $payment->status) }}</span>
                                                @endif
                                            </td>
                                            <td>NPR {{ number_format((float) $payment->amount, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">No payments found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const trendElement = document.querySelector('#appointments-revenue-trend');

            if (trendElement) {
                const trendChart = new ApexCharts(trendElement, {
                    chart: {
                        type: 'line',
                        height: 320,
                        toolbar: {
                            show: false,
                        },
                    },
                    series: [
                        {
                            name: 'Appointments',
                            data: @json($dashboardCharts['appointments']),
                        },
                        {
                            name: 'Revenue (NPR)',
                            data: @json($dashboardCharts['revenue']),
                        },
                    ],
                    stroke: {
                        curve: 'smooth',
                        width: 3,
                    },
                    dataLabels: {
                        enabled: false,
                    },
                    xaxis: {
                        categories: @json($dashboardCharts['labels']),
                    },
                    yaxis: [
                        {
                            title: {
                                text: 'Appointments',
                            },
                        },
                        {
                            opposite: true,
                            title: {
                                text: 'Revenue (NPR)',
                            },
                            labels: {
                                formatter: function (value) {
                                    return Number(value).toFixed(0);
                                },
                            },
                        },
                    ],
                    colors: ['#3b82f6', '#16a34a'],
                    legend: {
                        position: 'top',
                    },
                });

                trendChart.render();
            }

            const statusElement = document.querySelector('#appointment-status-breakdown');

            if (statusElement) {
                const statusChart = new ApexCharts(statusElement, {
                    chart: {
                        type: 'donut',
                        height: 320,
                    },
                    series: @json($appointmentStatusSeries),
                    labels: @json($appointmentStatusLabels),
                    legend: {
                        position: 'bottom',
                    },
                    colors: ['#f59e0b', '#0ea5e9', '#16a34a', '#ef4444', '#64748b'],
                    noData: {
                        text: 'No appointment data available',
                    },
                });

                statusChart.render();
            }
        });
    </script>
@endpush
