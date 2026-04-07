{{-- View: resources\views\pages\user\about.blade.php --}}
@extends('layouts.user.master')

@section('title', 'AstroConnect | About')

@section('content')
{{-- About hero and brand positioning copy. --}}
<section class="mx-auto max-w-7xl px-4 pb-16 pt-14 sm:px-6 lg:px-8 lg:pb-24 lg:pt-20">
    <div class="grid gap-10 lg:grid-cols-[1fr_0.85fr] lg:items-center">
        <div>
            <p class="text-sm uppercase tracking-[0.35em] text-amber-200/70">About AstroConnect</p>
            <h1 class="mt-4 text-5xl text-white sm:text-6xl [font-family:'Cormorant_Garamond',serif]">A modern astrology space built for reflection, guidance, and meaningful connection.</h1>
            <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-300">AstroConnect brings together timeless astrological practice and a contemporary digital experience so seekers can find trustworthy readers, daily inspiration, and deeper personal clarity.</p>
        </div>
        <div class="rounded-[2.5rem] border border-white/10 bg-white/5 p-8 backdrop-blur">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl border border-white/10 bg-slate-950/70 p-5">
                    <p class="text-3xl text-amber-200 [font-family:'Cormorant_Garamond',serif]">Vision</p>
                    <p class="mt-3 text-sm leading-7 text-slate-300">Make spiritual guidance approachable, thoughtful, and beautifully presented.</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-slate-950/70 p-5">
                    <p class="text-3xl text-amber-200 [font-family:'Cormorant_Garamond',serif]">Purpose</p>
                    <p class="mt-3 text-sm leading-7 text-slate-300">Help users explore astrology with confidence through quality readers and curated content.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Three-value pillars explaining platform principles. --}}
<section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
    <div class="grid gap-6 lg:grid-cols-3">
        <article class="rounded-[2rem] border border-white/10 bg-slate-900/70 p-8">
            <h2 class="text-3xl text-white [font-family:'Cormorant_Garamond',serif]">Authentic guidance</h2>
            <p class="mt-4 text-base leading-7 text-slate-300">We focus on clarity, empathy, and insight rather than vague promises, helping users find grounded astrological support.</p>
        </article>
        <article class="rounded-[2rem] border border-white/10 bg-slate-900/70 p-8">
            <h2 class="text-3xl text-white [font-family:'Cormorant_Garamond',serif]">Human connection</h2>
            <p class="mt-4 text-base leading-7 text-slate-300">AstroConnect is built around real practitioners, individual specialties, and the personal nature of spiritual guidance.</p>
        </article>
        <article class="rounded-[2rem] border border-white/10 bg-slate-900/70 p-8">
            <h2 class="text-3xl text-white [font-family:'Cormorant_Garamond',serif]">Elegant experience</h2>
            <p class="mt-4 text-base leading-7 text-slate-300">The interface is calm, immersive, and responsive across devices so the experience feels intentional from first visit to final click.</p>
        </article>
    </div>
</section>

{{-- Final philosophy strip highlighting service approach. --}}
<section class="mx-auto max-w-7xl px-4 pb-20 sm:px-6 lg:px-8">
    <div class="rounded-[2.5rem] border border-amber-300/20 bg-[linear-gradient(135deg,rgba(120,53,15,0.18),rgba(15,23,42,0.88),rgba(2,6,23,1))] p-8 sm:p-12">
        <p class="text-sm uppercase tracking-[0.35em] text-amber-200/80">Our approach</p>
        <div class="mt-8 grid gap-6 lg:grid-cols-3">
            <div>
                <p class="text-lg font-semibold text-white">Listen first</p>
                <p class="mt-3 text-sm leading-7 text-slate-300">We shape the experience around users who want direction, reassurance, and a sense of timing.</p>
            </div>
            <div>
                <p class="text-lg font-semibold text-white">Guide with depth</p>
                <p class="mt-3 text-sm leading-7 text-slate-300">Astrology is presented as a tool for reflection and decision-making, not noise.</p>
            </div>
            <div>
                <p class="text-lg font-semibold text-white">Build trust</p>
                <p class="mt-3 text-sm leading-7 text-slate-300">Clear structure, visible expertise, and accessible content make the platform easier to navigate.</p>
            </div>
        </div>
    </div>
</section>
@endsection
