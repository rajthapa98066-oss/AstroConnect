@extends('layouts.user.master')

@section('title', 'AstroConnect | My Appointments')

@section('content')
<section class="mx-auto max-w-7xl px-4 pb-16 pt-14 sm:px-6 lg:px-8 lg:pb-24 lg:pt-20">
    <p class="text-sm uppercase tracking-[0.35em] text-amber-200/70">My Appointments</p>
    <h1 class="mt-4 text-5xl text-white sm:text-6xl [font-family:'Cormorant_Garamond',serif]">Track your consultation requests and upcoming sessions.</h1>
</section>

<section class="mx-auto max-w-7xl px-4 pb-20 sm:px-6 lg:px-8">
    <div class="rounded-[2rem] border border-white/10 bg-gradient-to-b from-slate-900 to-slate-950 p-7 shadow-xl shadow-slate-950/30 sm:p-8">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm text-slate-300">
                <thead class="border-b border-white/10 text-xs uppercase tracking-[0.2em] text-slate-400">
                    <tr>
                        <th class="px-3 py-3">Astrologer</th>
                        <th class="px-3 py-3">Topic</th>
                        <th class="px-3 py-3">Schedule</th>
                        <th class="px-3 py-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($appointments as $appointment)
                        <tr class="border-b border-white/5">
                            <td class="px-3 py-4">{{ $appointment->astrologer->user->name }}</td>
                            <td class="px-3 py-4">{{ $appointment->topic }}</td>
                            <td class="px-3 py-4">{{ $appointment->scheduled_at->format('M d, Y h:i A') }}</td>
                            <td class="px-3 py-4">
                                <span class="rounded-full border border-white/15 bg-white/5 px-3 py-1 text-xs uppercase tracking-[0.18em] text-white">{{ $appointment->status }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-3 py-8 text-center text-slate-400">No appointments booked yet.</td>
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
