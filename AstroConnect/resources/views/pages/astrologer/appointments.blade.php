@extends('layouts.astrologer.master')

@section('title', 'AstroConnect | Astrologer Appointments')

@section('content')
<section class="mx-auto max-w-7xl px-4 pb-16 pt-14 sm:px-6 lg:px-8 lg:pb-24 lg:pt-20">
    <p class="text-sm uppercase tracking-[0.35em] text-amber-200/70">Appointments</p>
    <h1 class="mt-4 text-5xl text-white sm:text-6xl [font-family:'Cormorant_Garamond',serif]">Manage user booking requests and session status.</h1>
</section>

<section class="mx-auto max-w-7xl px-4 pb-20 sm:px-6 lg:px-8">
    @if (session('status'))
        <div class="mb-6 rounded-2xl border border-emerald-300/20 bg-emerald-300/10 px-5 py-4 text-sm text-emerald-200">
            Action completed: {{ str_replace('-', ' ', session('status')) }}
        </div>
    @endif

    <div class="rounded-[2rem] border border-white/10 bg-gradient-to-b from-slate-900 to-slate-950 p-7 shadow-xl shadow-slate-950/30 sm:p-8">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm text-slate-300">
                <thead class="border-b border-white/10 text-xs uppercase tracking-[0.2em] text-slate-400">
                    <tr>
                        <th class="px-3 py-3">User</th>
                        <th class="px-3 py-3">Topic</th>
                        <th class="px-3 py-3">Message</th>
                        <th class="px-3 py-3">Scheduled At</th>
                        <th class="px-3 py-3">Status</th>
                        <th class="px-3 py-3">Update</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($appointments as $appointment)
                        <tr class="border-b border-white/5">
                            <td class="px-3 py-4">{{ $appointment->user->name }}</td>
                            <td class="px-3 py-4">{{ $appointment->topic }}</td>
                            <td class="px-3 py-4 max-w-sm">{{ $appointment->message ?: '-' }}</td>
                            <td class="px-3 py-4">{{ $appointment->scheduled_at->format('M d, Y h:i A') }}</td>
                            <td class="px-3 py-4">
                                <span class="rounded-full border border-white/15 bg-white/5 px-3 py-1 text-xs uppercase tracking-[0.18em] text-white">{{ $appointment->status }}</span>
                            </td>
                            <td class="px-3 py-4">
                                <form method="POST" action="{{ route('astrologer.appointments.status', $appointment) }}" class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-xs uppercase tracking-[0.16em] text-slate-100 outline-none">
                                        @foreach (['pending', 'confirmed', 'rejected', 'completed', 'cancelled'] as $status)
                                            <option value="{{ $status }}" @selected($appointment->status === $status)>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="rounded-full bg-amber-300 px-3 py-2 text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-950">Save</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-8 text-center text-slate-400">No appointment requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-8 [&_nav]:flex [&_nav]:justify-center [&_nav]:text-slate-300 [&_span]:border-white/10 [&_span]:bg-white/5 [&_span]:text-slate-300 [&_a]:border-white/10 [&_a]:bg-white/5 [&_a]:text-slate-200 [&_a:hover]:bg-white/10">
            {{ $appointments->links() }}
        </div>
    </div>
</section>
@endsection
