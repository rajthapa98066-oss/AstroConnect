{{-- View: resources\views\dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'AstroConnect | Dashboard')

@section('content')
@php
    $astrologerProfile = auth()->user()?->astrologer;
    $status = $astrologerProfile?->verification_status;
@endphp

<section class="mx-auto max-w-7xl px-4 pb-14 pt-14 sm:px-6 lg:px-8 lg:pb-20 lg:pt-20">
    <div class="grid gap-10 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
        <div>
            <p class="text-sm uppercase tracking-[0.35em] text-amber-200/70">Personal Dashboard</p>
            <h1 class="mt-4 text-5xl text-white sm:text-6xl [font-family:'Cormorant_Garamond',serif]">Welcome back, {{ auth()->user()->name }}.</h1>
            <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-300">Track your AstroConnect access, manage your profile, and continue your astrologer journey from one central space.</p>

            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('profile.edit') }}" class="inline-flex items-center justify-center rounded-full border border-white/15 px-6 py-3 text-xs font-semibold uppercase tracking-[0.2em] text-white transition hover:border-white/30 hover:bg-white/5">
                    Manage Profile
                </a>
                <a href="{{ route('astrologers.index') }}" class="inline-flex items-center justify-center rounded-full bg-amber-300 px-6 py-3 text-xs font-semibold uppercase tracking-[0.2em] text-slate-950 transition hover:bg-amber-200">
                    Browse Astrologers
                </a>
            </div>
        </div>

        <div class="rounded-[2rem] border border-white/10 bg-white/5 p-8 backdrop-blur">
            <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Account state</p>
            <p class="mt-2 text-3xl text-amber-200 [font-family:'Cormorant_Garamond',serif]">{{ $status ? ucfirst($status) : 'Member' }}</p>
            <p class="mt-4 text-sm leading-7 text-slate-300">
                @if (! $astrologerProfile)
                    You have not applied as an astrologer yet.
                @elseif ($status === 'approved')
                    Your astrologer profile is approved and visible.
                @elseif ($status === 'pending')
                    Your astrologer application is currently under review.
                @else
                    Your previous application was rejected and can be updated.
                @endif
            </p>
        </div>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 pb-20 sm:px-6 lg:px-8">
    <div class="rounded-[2rem] border border-white/10 bg-gradient-to-b from-slate-900 to-slate-950 p-7 shadow-xl shadow-slate-950/30 sm:p-8">
        <p class="text-sm uppercase tracking-[0.35em] text-amber-200/70">Astrologer Access</p>

        @if (! $astrologerProfile)
            <h2 class="mt-3 text-4xl text-white [font-family:'Cormorant_Garamond',serif]">Apply and get listed on AstroConnect.</h2>
            <p class="mt-4 text-base leading-8 text-slate-300">Create your astrologer profile to start receiving consultation requests from users.</p>
            <a href="{{ route('astrologer.apply') }}" class="mt-7 inline-flex items-center justify-center rounded-full bg-amber-300 px-7 py-3 text-xs font-semibold uppercase tracking-[0.2em] text-slate-950 transition hover:bg-amber-200">
                Apply as Astrologer
            </a>
        @elseif ($status === 'approved')
            <h2 class="mt-3 text-4xl text-white [font-family:'Cormorant_Garamond',serif]">Your profile is approved and active.</h2>
            <p class="mt-4 text-base leading-8 text-slate-300">Open your astrologer dashboard to manage profile details and appointments.</p>
            <a href="{{ route('astrologer.dashboard') }}" class="mt-7 inline-flex items-center justify-center rounded-full bg-amber-300 px-7 py-3 text-xs font-semibold uppercase tracking-[0.2em] text-slate-950 transition hover:bg-amber-200">
                Go to Astrologer Dashboard
            </a>
        @elseif ($status === 'pending')
            <h2 class="mt-3 text-4xl text-white [font-family:'Cormorant_Garamond',serif]">Your application is being reviewed.</h2>
            <p class="mt-4 text-base leading-8 text-slate-300">You can reopen the application page to review what you submitted while waiting for admin approval.</p>
            <a href="{{ route('astrologer.apply') }}" class="mt-7 inline-flex items-center justify-center rounded-full border border-white/15 px-7 py-3 text-xs font-semibold uppercase tracking-[0.2em] text-white transition hover:border-white/30 hover:bg-white/5">
                View Application
            </a>
        @else
            <h2 class="mt-3 text-4xl text-white [font-family:'Cormorant_Garamond',serif]">Your previous application was not approved.</h2>
            <p class="mt-4 text-base leading-8 text-slate-300">Update your details and submit a stronger profile to reapply as an astrologer.</p>
            <a href="{{ route('astrologer.apply') }}" class="mt-7 inline-flex items-center justify-center rounded-full bg-amber-300 px-7 py-3 text-xs font-semibold uppercase tracking-[0.2em] text-slate-950 transition hover:bg-amber-200">
                Reapply as Astrologer
            </a>
        @endif
    </div>
</section>
@endsection
