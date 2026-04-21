{{-- View: resources\views\pages\admin\reports-management.blade.php --}}
@extends('layouts.admin.master')

@section('admin')
    <div class="container-xxl">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Astrologer Reports</h4>
                <p class="text-muted mb-0">Review user-submitted reports and moderate astrologer accounts.</p>
            </div>
        </div>

        @if (session('status'))
            <div class="alert alert-success">Action completed: {{ session('status') }}</div>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Reporter</th>
                                <th>Astrologer</th>
                                <th>Reason</th>
                                <th>Details</th>
                                <th>Status</th>
                                <th>Resolution</th>
                                <th>Submitted</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($reports as $report)
                                <tr>
                                    <td>{{ $report->id }}</td>
                                    <td>{{ $report->reporter?->name ?? 'Deleted user' }}</td>
                                    <td>
                                        {{ $report->astrologer?->user?->name ?? 'Deleted astrologer' }}
                                        @if ($report->astrologer)
                                            <div class="small text-muted">{{ $report->astrologer->specialization }}</div>
                                        @endif
                                    </td>
                                    <td>{{ ucfirst($report->reason) }}</td>
                                    <td style="max-width: 280px; white-space: normal;">{{ $report->details ?: 'No extra details provided.' }}</td>
                                    <td>
                                        <span class="badge {{ $report->status === 'pending' ? 'bg-warning text-dark' : 'bg-success' }}">{{ ucfirst($report->status) }}</span>
                                    </td>
                                    <td>
                                        @if ($report->resolution)
                                            <span class="badge bg-info text-dark">{{ ucfirst($report->resolution) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $report->created_at?->format('M d, Y h:i A') }}</td>
                                    <td>
                                        @if ($report->status === 'pending')
                                            <div class="d-flex gap-2 flex-wrap">
                                                <form method="POST" action="{{ route('admin.reports.flag', $report) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-warning btn-sm">Flag</button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.reports.disable', $report) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-dark btn-sm">Disable</button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.reports.delete-account', $report) }}" onsubmit="return confirm('Delete this astrologer account?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">Delete Account</button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-muted">Resolved</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">No reports found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $reports->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
