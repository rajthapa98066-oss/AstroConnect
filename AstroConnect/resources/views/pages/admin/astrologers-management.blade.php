{{-- View: resources\views\pages\admin\astrologers-management.blade.php --}}
@extends('layouts.admin.master')

@section('admin')
    <div class="container-xxl">
        {{-- Page title and context for moderation queue. --}}
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Astrologer Applications</h4>
            </div>
        </div>

        @if (session('status'))
            <div class="alert alert-success">Action completed: {{ session('status') }}</div>
        @endif

        {{-- Paginated application table with direct approve/reject actions. --}}
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Specialization</th>
                                <th>Experience</th>
                                <th>Fee</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($astrologers as $astrologer)
                                <tr>
                                    <td>{{ $astrologer->id }}</td>
                                    <td>{{ $astrologer->user->name }}</td>
                                    <td>{{ $astrologer->user->email }}</td>
                                    <td>{{ $astrologer->specialization }}</td>
                                    <td>{{ $astrologer->experience_years }} years</td>
                                    <td>{{ number_format((float) $astrologer->consultation_fee, 2) }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ ucfirst($astrologer->verification_status) }}</span>
                                    </td>
                                    {{-- Admin moderation actions per astrologer profile. --}}
                                    <td class="d-flex gap-2">
                                        <form method="POST" action="{{ route('admin.astrologers.approve', $astrologer) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.astrologers.reject', $astrologer) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No astrologer applications found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $astrologers->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
