@extends('layouts.app')

@section('title', 'AstroConnect | Home')

@section('content')
<section class="mx-auto grid max-w-7xl gap-16 px-4 pb-20 pt-14 sm:px-6 lg:grid-cols-[1.1fr_0.9fr] lg:px-8 lg:pb-28 lg:pt-20">
    <div class="flex flex-col justify-center">
        <span class="mb-6 inline-flex w-fit items-center rounded-full border border-amber-300/25 bg-amber-300/10 px-4 py-2 text-xs font-medium uppercase tracking-[0.3em] text-amber-200">
            Ancient wisdom, modern clarity
        </span>
        <h1 class="max-w-3xl text-5xl leading-none text-white sm:text-6xl lg:text-7xl [font-family:'Cormorant_Garamond',serif]">
            Chart your next chapter with trusted astrologers and cosmic insight.
        </h1>
        <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-300 sm:text-xl">
            AstroConnect pairs personalized readings, horoscope guidance, and experienced astrologers in one calm, immersive space designed for seekers.
        </p>
        <div class="mt-10 flex flex-col gap-4 sm:flex-row">
            <a href="{{ url('/services') }}" class="inline-flex items-center justify-center rounded-full bg-amber-300 px-7 py-4 text-sm font-semibold uppercase tracking-[0.2em] text-slate-950 transition hover:bg-amber-200">
                Explore Services
            </a>
            <a href="{{ url('/contact') }}" class="inline-flex items-center justify-center rounded-full border border-white/15 px-7 py-4 text-sm font-semibold uppercase tracking-[0.2em] text-white transition hover:border-white/30 hover:bg-white/5">
                Book a Consultation
            </a>
        </div>
        <div class="mt-12 grid gap-4 sm:grid-cols-3">
            <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur">
                <p class="text-3xl text-amber-200 [font-family:'Cormorant_Garamond',serif]">12</p>
                <p class="mt-2 text-sm uppercase tracking-[0.25em] text-slate-400">Zodiac archetypes</p>
            </div>
            <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur">
                <p class="text-3xl text-amber-200 [font-family:'Cormorant_Garamond',serif]">24/7</p>
                <p class="mt-2 text-sm uppercase tracking-[0.25em] text-slate-400">Horoscope inspiration</p>
            </div>
            <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur">
                <p class="text-3xl text-amber-200 [font-family:'Cormorant_Garamond',serif]">1:1</p>
                <p class="mt-2 text-sm uppercase tracking-[0.25em] text-slate-400">Guided sessions</p>
            </div>
        </div>
    </div>

    <div class="relative flex items-center justify-center">
        <div class="absolute inset-0 m-auto h-80 w-80 rounded-full bg-amber-300/10 blur-3xl sm:h-[28rem] sm:w-[28rem]"></div>
        <div class="relative flex aspect-square w-full max-w-[34rem] items-center justify-center rounded-full border border-amber-300/20 bg-white/5 p-8 shadow-[0_0_80px_rgba(15,23,42,0.7)] backdrop-blur">
            <div class="absolute inset-6 rounded-full border border-dashed border-white/10"></div>
            <img src="{{ asset('images/zodiac-wheel.png') }}" alt="Rotating zodiac wheel" class="relative z-10 w-full max-w-md animate-spin drop-shadow-[0_0_40px_rgba(251,191,36,0.18)] motion-reduce:animate-none [animation-duration:36s]">
            <div class="absolute inset-0 bg-[radial-gradient(circle,_rgba(15,23,42,0)_38%,rgba(15,23,42,0.65)_100%)]"></div>
        </div>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
    <div class="mb-12 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="text-sm uppercase tracking-[0.35em] text-amber-200/70">Services</p>
            <h2 class="mt-3 text-4xl text-white [font-family:'Cormorant_Garamond',serif]">Spiritual services tailored to your journey.</h2>
        </div>
        <a href="{{ url('/services') }}" class="text-sm font-semibold uppercase tracking-[0.2em] text-amber-200 transition hover:text-amber-100">View all services</a>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <article class="rounded-[2rem] border border-white/10 bg-slate-900/70 p-8 shadow-xl shadow-slate-950/30">
            <p class="text-sm uppercase tracking-[0.3em] text-amber-200/70">01</p>
            <h3 class="mt-4 text-3xl text-white [font-family:'Cormorant_Garamond',serif]">Birth Chart Reading</h3>
            <p class="mt-4 text-base leading-7 text-slate-300">Decode your strengths, emotional patterns, and life timing with a complete natal chart interpretation.</p>
        </article>
        <article class="rounded-[2rem] border border-white/10 bg-slate-900/70 p-8 shadow-xl shadow-slate-950/30">
            <p class="text-sm uppercase tracking-[0.3em] text-amber-200/70">02</p>
            <h3 class="mt-4 text-3xl text-white [font-family:'Cormorant_Garamond',serif]">Compatibility Guidance</h3>
            <p class="mt-4 text-base leading-7 text-slate-300">Explore relationship dynamics, communication rhythms, and long-term harmony through synastry.</p>
        </article>
        <article class="rounded-[2rem] border border-white/10 bg-slate-900/70 p-8 shadow-xl shadow-slate-950/30">
            <p class="text-sm uppercase tracking-[0.3em] text-amber-200/70">03</p>
            <h3 class="mt-4 text-3xl text-white [font-family:'Cormorant_Garamond',serif]">Daily Horoscope Insight</h3>
            <p class="mt-4 text-base leading-7 text-slate-300">Receive grounded zodiac guidance for love, work, and wellbeing with fresh daily forecasts.</p>
        </article>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
    <div class="mb-12 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="text-sm uppercase tracking-[0.35em] text-amber-200/70">Astrologers</p>
            <h2 class="mt-3 text-4xl text-white [font-family:'Cormorant_Garamond',serif]">Meet astrologers with distinct styles and specialties.</h2>
        </div>
        <a href="{{ url('/astrologers') }}" class="text-sm font-semibold uppercase tracking-[0.2em] text-amber-200 transition hover:text-amber-100">Browse astrologers</a>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <article class="rounded-[2rem] border border-white/10 bg-gradient-to-b from-slate-900 to-slate-950 p-8">
            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-amber-300/15 text-lg text-amber-200">AR</div>
            <h3 class="mt-6 text-3xl text-white [font-family:'Cormorant_Garamond',serif]">Ariana Ray</h3>
            <p class="mt-2 text-sm uppercase tracking-[0.25em] text-slate-400">Natal Charts</p>
            <p class="mt-4 text-base leading-7 text-slate-300">Focused on self-discovery, timing cycles, and career clarity for pivotal life transitions.</p>
        </article>
        <article class="rounded-[2rem] border border-white/10 bg-gradient-to-b from-slate-900 to-slate-950 p-8">
            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-amber-300/15 text-lg text-amber-200">SD</div>
            <h3 class="mt-6 text-3xl text-white [font-family:'Cormorant_Garamond',serif]">Sage Dev</h3>
            <p class="mt-2 text-sm uppercase tracking-[0.25em] text-slate-400">Relationship Astrology</p>
            <p class="mt-4 text-base leading-7 text-slate-300">Helps couples and partners understand compatibility, emotional language, and energetic balance.</p>
        </article>
        <article class="rounded-[2rem] border border-white/10 bg-gradient-to-b from-slate-900 to-slate-950 p-8">
            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-amber-300/15 text-lg text-amber-200">LM</div>
            <h3 class="mt-6 text-3xl text-white [font-family:'Cormorant_Garamond',serif]">Luna Mira</h3>
            <p class="mt-2 text-sm uppercase tracking-[0.25em] text-slate-400">Moon & Transit Work</p>
            <p class="mt-4 text-base leading-7 text-slate-300">Guides clients through emotional resets, lunar rituals, and real-time transit interpretation.</p>
        </article>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
    <div class="overflow-hidden rounded-[2.5rem] border border-amber-300/20 bg-[linear-gradient(135deg,rgba(120,53,15,0.28),rgba(15,23,42,0.9),rgba(2,6,23,1))] px-8 py-12 sm:px-12 lg:flex lg:items-center lg:justify-between lg:px-16">
        <div class="max-w-2xl">
            <p class="text-sm uppercase tracking-[0.35em] text-amber-200/80">Start your reading</p>
            <h2 class="mt-4 text-4xl text-white [font-family:'Cormorant_Garamond',serif]">Ready to understand what the stars are already revealing?</h2>
            <p class="mt-4 text-base leading-7 text-slate-300">Whether you want practical guidance or a deeper spiritual reading, AstroConnect helps you begin with clarity.</p>
        </div>
        <div class="mt-8 flex flex-col gap-4 sm:flex-row lg:mt-0">
            <a href="{{ url('/contact') }}" class="inline-flex items-center justify-center rounded-full bg-amber-300 px-7 py-4 text-sm font-semibold uppercase tracking-[0.2em] text-slate-950 transition hover:bg-amber-200">
                Contact Us
            </a>
            <a href="{{ url('/horoscope') }}" class="inline-flex items-center justify-center rounded-full border border-white/15 px-7 py-4 text-sm font-semibold uppercase tracking-[0.2em] text-white transition hover:border-white/30 hover:bg-white/5">
                Read Horoscope
            </a>
        </div>
    </div>
</section>

@endsection
