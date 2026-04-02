@extends('layouts.app')

@section('title', 'AstroConnect | Your Profile')

@section('content')
<section class="mx-auto max-w-7xl px-4 pb-14 pt-14 sm:px-6 lg:px-8 lg:pb-20 lg:pt-20">
    <div class="grid gap-10 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
        <div>
            <p class="text-sm uppercase tracking-[0.35em] text-amber-200/70">Account Hub</p>
            <h1 class="mt-4 text-5xl text-white sm:text-6xl [font-family:'Cormorant_Garamond',serif]">Shape your profile and secure your AstroConnect account.</h1>
            <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-300">Keep your identity details current, strengthen your login credentials, and manage account access from one place.</p>
        </div>

        <div class="rounded-[2rem] border border-white/10 bg-white/5 p-8 backdrop-blur">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl border border-white/10 bg-slate-950/70 p-5">
                    <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Current member</p>
                    <p class="mt-2 text-2xl text-amber-200 [font-family:'Cormorant_Garamond',serif]">{{ $user->name }}</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-slate-950/70 p-5">
                    <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Email status</p>
                    <p class="mt-2 text-2xl text-amber-200 [font-family:'Cormorant_Garamond',serif]">{{ $user->hasVerifiedEmail() ? 'Verified' : 'Pending' }}</p>
                </div>
            </div>
            <p class="mt-6 text-sm leading-7 text-slate-300">Your profile updates apply instantly across bookings, dashboard activity, and platform notifications.</p>
        </div>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 pb-20 sm:px-6 lg:px-8">
    <div class="space-y-6">
        <div class="rounded-[2rem] border border-white/10 bg-gradient-to-b from-slate-900 to-slate-950 p-7 shadow-xl shadow-slate-950/30 sm:p-8">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="rounded-[2rem] border border-white/10 bg-gradient-to-b from-slate-900 to-slate-950 p-7 shadow-xl shadow-slate-950/30 sm:p-8">
            @include('profile.partials.update-password-form')
        </div>

        <div class="rounded-[2rem] border border-red-400/20 bg-[linear-gradient(135deg,rgba(127,29,29,0.18),rgba(15,23,42,0.9),rgba(2,6,23,1))] p-7 shadow-xl shadow-slate-950/30 sm:p-8">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</section>
@endsection
