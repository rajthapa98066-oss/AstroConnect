{{-- View: resources\views\pages\user\services.blade.php --}}
@extends('layouts.user.master')

@section('title', 'AstroConnect | Services')

@section('content')
{{-- Services hero describing available guidance formats. --}}
<section class="mx-auto max-w-7xl px-4 pb-16 pt-14 sm:px-6 lg:px-8 lg:pb-24 lg:pt-20">
    <p class="text-sm uppercase tracking-[0.35em] text-amber-200/70">Services</p>
    <h1 class="mt-4 max-w-4xl text-5xl text-white sm:text-6xl [font-family:'Cormorant_Garamond',serif]">Guidance formats designed for different questions, seasons, and life transitions.</h1>
    <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-300">Choose from focused readings, relationship insights, and horoscope guidance shaped around the way people actually seek clarity.</p>
</section>

{{-- Service cards listing the core offering types. --}}
<section class="mx-auto max-w-7xl px-4 pb-20 sm:px-6 lg:px-8">
    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
        <article class="rounded-[2rem] border border-white/10 bg-slate-900/70 p-8">
            <p class="text-sm uppercase tracking-[0.3em] text-amber-200/70">01</p>
            <h2 class="mt-4 text-3xl text-white [font-family:'Cormorant_Garamond',serif]">Birth Chart Reading</h2>
            <p class="mt-4 text-base leading-7 text-slate-300">A complete overview of your natal chart, personality patterns, strengths, and current themes.</p>
        </article>
        <article class="rounded-[2rem] border border-white/10 bg-slate-900/70 p-8">
            <p class="text-sm uppercase tracking-[0.3em] text-amber-200/70">02</p>
            <h2 class="mt-4 text-3xl text-white [font-family:'Cormorant_Garamond',serif]">Compatibility Analysis</h2>
            <p class="mt-4 text-base leading-7 text-slate-300">Understand emotional chemistry, communication style, and long-term relational alignment.</p>
        </article>
        <article class="rounded-[2rem] border border-white/10 bg-slate-900/70 p-8">
            <p class="text-sm uppercase tracking-[0.3em] text-amber-200/70">03</p>
            <h2 class="mt-4 text-3xl text-white [font-family:'Cormorant_Garamond',serif]">Transit Forecast</h2>
            <p class="mt-4 text-base leading-7 text-slate-300">A focused session on the major astrological cycles currently shaping your choices and momentum.</p>
        </article>
        <article class="rounded-[2rem] border border-white/10 bg-slate-900/70 p-8">
            <p class="text-sm uppercase tracking-[0.3em] text-amber-200/70">04</p>
            <h2 class="mt-4 text-3xl text-white [font-family:'Cormorant_Garamond',serif]">Love & Relationship Reading</h2>
            <p class="mt-4 text-base leading-7 text-slate-300">Reveal recurring patterns in attraction, attachment, timing, and emotional intimacy.</p>
        </article>
        <article class="rounded-[2rem] border border-white/10 bg-slate-900/70 p-8">
            <p class="text-sm uppercase tracking-[0.3em] text-amber-200/70">05</p>
            <h2 class="mt-4 text-3xl text-white [font-family:'Cormorant_Garamond',serif]">Daily Horoscope Guidance</h2>
            <p class="mt-4 text-base leading-7 text-slate-300">Lightweight, ongoing insight for the energies influencing work, rest, love, and decision-making.</p>
        </article>
    </div>
</section>
@endsection
