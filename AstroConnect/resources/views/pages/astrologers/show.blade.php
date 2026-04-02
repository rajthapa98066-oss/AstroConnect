@extends('layouts.app')

@section('title', $astrologer->user->name . ' | AstroConnect')

@section('content')
<section class="mx-auto max-w-7xl px-4 pb-16 pt-14 sm:px-6 lg:px-8 lg:pb-24 lg:pt-20">
    <div class="mb-8">
        <a href="{{ route('astrologers.index') }}" class="inline-flex items-center gap-2 rounded-full border border-white/10 px-4 py-2 text-sm font-medium text-slate-200 transition hover:border-white/20 hover:text-white">
            <span>←</span>
            <span>Back to Astrologers</span>
        </a>
    </div>

    <div class="grid gap-8 lg:grid-cols-[0.75fr_1.25fr]">
        <aside class="rounded-[2rem] border border-white/10 bg-gradient-to-b from-slate-900 to-slate-950 p-7 shadow-xl shadow-slate-950/30">
            <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-amber-300/15 text-3xl font-semibold text-amber-200">
                {{ strtoupper(substr($astrologer->user->name, 0, 1)) }}
            </div>

            <h1 class="mt-6 text-center text-4xl text-white [font-family:'Cormorant_Garamond',serif]">{{ $astrologer->user->name }}</h1>
            <p class="mt-2 text-center text-sm uppercase tracking-[0.25em] text-amber-200/80">{{ $astrologer->specialization }}</p>

            <div class="mt-6 space-y-3">
                <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-300">
                    Availability<br>
                    <span class="text-base font-semibold text-white">{{ ucfirst($astrologer->availability_status) }}</span>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-300">
                    Experience<br>
                    <span class="text-base font-semibold text-white">{{ $astrologer->experience_years }} years</span>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-300">
                    Consultation Fee<br>
                    <span class="text-base font-semibold text-white">{{ number_format((float) $astrologer->consultation_fee, 2) }}</span>
                </div>
            </div>
        </aside>

        <div class="rounded-[2rem] border border-white/10 bg-white/5 p-7 backdrop-blur sm:p-8">
            <p class="text-sm uppercase tracking-[0.35em] text-amber-200/70">About the Astrologer</p>
            <h2 class="mt-3 text-4xl text-white [font-family:'Cormorant_Garamond',serif]">Guidance style and reading focus</h2>
            <p class="mt-6 text-base leading-8 text-slate-300">{{ $astrologer->bio }}</p>

            <div class="mt-10 overflow-hidden rounded-[1.75rem] border border-amber-300/20 bg-[linear-gradient(135deg,rgba(120,53,15,0.28),rgba(15,23,42,0.9),rgba(2,6,23,1))] px-7 py-8">
                <p class="text-sm uppercase tracking-[0.35em] text-amber-200/80">Need a reading?</p>
                <h3 class="mt-3 text-3xl text-white [font-family:'Cormorant_Garamond',serif]">Start your consultation request today.</h3>
                <p class="mt-3 text-sm leading-7 text-slate-300">This astrologer profile is approved and visible in the AstroConnect directory. Reach out through contact channels to begin your session.</p>
                <a href="{{ route('contact') }}" class="mt-6 inline-flex items-center justify-center rounded-full bg-amber-300 px-6 py-3 text-xs font-semibold uppercase tracking-[0.2em] text-slate-950 transition hover:bg-amber-200">
                    Contact Now
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
