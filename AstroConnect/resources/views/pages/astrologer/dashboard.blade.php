{{-- View: resources\views\pages\astrologer\dashboard.blade.php --}}
@extends('layouts.astrologer.master')

@section('title', 'AstroConnect | Astrologer Dashboard')

@section('content')
{{-- Hero area with primary actions and current availability summary. --}}
<section class="mx-auto max-w-7xl px-4 pb-16 pt-14 sm:px-6 lg:px-8 lg:pb-24 lg:pt-20">
    <div class="grid gap-10 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
        <div>
            <p class="text-sm uppercase tracking-[0.35em] text-amber-200/70">Astrologer Dashboard</p>
            <h1 class="mt-4 text-5xl text-white sm:text-6xl [font-family:'Cormorant_Garamond',serif]">Welcome, {{ auth()->user()->name }}.</h1>
            <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-300">Your profile is approved and live. Keep your expertise details fresh so seekers can trust and book with confidence.</p>

            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('astrologer.profile') }}" class="inline-flex items-center justify-center rounded-full bg-amber-300 px-6 py-3 text-xs font-semibold uppercase tracking-[0.2em] text-slate-950 transition hover:bg-amber-200">
                    Edit Profile
                </a>
                <a href="{{ route('astrologer.appointments') }}" class="inline-flex items-center justify-center rounded-full border border-white/15 px-6 py-3 text-xs font-semibold uppercase tracking-[0.2em] text-white transition hover:border-white/30 hover:bg-white/5">
                    View Appointments
                </a>
                <a href="{{ route('astrologer.blogs.index') }}" class="inline-flex items-center justify-center rounded-full border border-white/15 px-6 py-3 text-xs font-semibold uppercase tracking-[0.2em] text-white transition hover:border-white/30 hover:bg-white/5">
                    Manage Blogs
                </a>
            </div>
        </div>

        <div class="rounded-[2rem] border border-white/10 bg-white/5 p-8 backdrop-blur">
            <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Availability</p>
            <p class="mt-2 text-3xl text-amber-200 [font-family:'Cormorant_Garamond',serif]">{{ ucfirst($astrologer->availability_status) }}</p>
            <p class="mt-4 text-sm leading-7 text-slate-300">Update your status to reflect current consultation capacity and improve booking clarity for users.</p>
        </div>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 pb-20 sm:px-6 lg:px-8">
    {{-- Profile snapshot cards for specialization, experience, and fee. --}}
    <div class="grid gap-6 md:grid-cols-3">
        <article class="rounded-[2rem] border border-white/10 bg-gradient-to-b from-slate-900 to-slate-950 p-7 shadow-xl shadow-slate-950/30">
            <p class="text-sm uppercase tracking-[0.25em] text-slate-400">Specialization</p>
            <h2 class="mt-3 text-3xl text-white [font-family:'Cormorant_Garamond',serif]">{{ $astrologer->specialization }}</h2>
        </article>

        <article class="rounded-[2rem] border border-white/10 bg-gradient-to-b from-slate-900 to-slate-950 p-7 shadow-xl shadow-slate-950/30">
            <p class="text-sm uppercase tracking-[0.25em] text-slate-400">Experience</p>
            <h2 class="mt-3 text-3xl text-white [font-family:'Cormorant_Garamond',serif]">{{ $astrologer->experience_years }} Years</h2>
        </article>

        <article class="rounded-[2rem] border border-white/10 bg-gradient-to-b from-slate-900 to-slate-950 p-7 shadow-xl shadow-slate-950/30">
            <p class="text-sm uppercase tracking-[0.25em] text-slate-400">Consultation Fee</p>
            <h2 class="mt-3 text-3xl text-white [font-family:'Cormorant_Garamond',serif]">{{ number_format((float) $astrologer->consultation_fee, 2) }}</h2>
        </article>
    </div>

    {{-- Operational counters for appointment and blog review states. --}}
    <div class="mt-6 grid gap-6 md:grid-cols-3">
        <article class="rounded-[2rem] border border-white/10 bg-slate-900/70 p-6">
            <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Appointments</p>
            <p class="mt-3 text-3xl text-amber-200 [font-family:'Cormorant_Garamond',serif]">{{ (int) ($appointmentCounts['pending'] ?? 0) }}</p>
            <p class="text-sm text-slate-300">Pending requests</p>
        </article>
        <article class="rounded-[2rem] border border-white/10 bg-slate-900/70 p-6">
            <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Appointments</p>
            <p class="mt-3 text-3xl text-amber-200 [font-family:'Cormorant_Garamond',serif]">{{ (int) ($appointmentCounts['confirmed'] ?? 0) }}</p>
            <p class="text-sm text-slate-300">Confirmed sessions</p>
        </article>
        <article class="rounded-[2rem] border border-white/10 bg-slate-900/70 p-6">
            <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Blog Review</p>
            <p class="mt-3 text-3xl text-amber-200 [font-family:'Cormorant_Garamond',serif]">{{ (int) ($blogCounts['pending'] ?? 0) }}</p>
            <p class="text-sm text-slate-300">Posts waiting admin approval</p>
        </article>
    </div>

    {{-- Latest appointment feed with quick status visibility. --}}
    <div class="mt-6 rounded-[2rem] border border-white/10 bg-slate-900/70 p-6">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-2xl text-white [font-family:'Cormorant_Garamond',serif]">Recent appointments</h3>
            <a href="{{ route('astrologer.appointments') }}" class="text-xs uppercase tracking-[0.2em] text-amber-200 hover:text-amber-100">Open full list</a>
        </div>

        <div class="space-y-3">
            @forelse ($recentAppointments as $appointment)
                <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-200">
                    {{ $appointment->user->name }} • {{ $appointment->topic }} • {{ $appointment->scheduled_at->format('M d, Y h:i A') }}
                    <span class="ml-2 rounded-full border border-white/15 px-2 py-1 text-[10px] uppercase tracking-[0.15em] text-slate-300">{{ $appointment->status }}</span>
                </div>
            @empty
                <p class="text-sm text-slate-400">No appointments yet. As users start booking, they will appear here.</p>
            @endforelse
        </div>
    </div>
</section>
@endsection
