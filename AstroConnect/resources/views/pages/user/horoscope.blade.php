{{-- View: resources\views\pages\user\horoscope.blade.php --}}
@extends('layouts.app')

@section('title', 'AstroConnect | Daily Horoscopes')

@section('content')
{{-- Hero Section --}}
<section class="relative mx-auto max-w-7xl px-4 pt-14 pb-12 sm:px-6 lg:px-8 lg:pt-20">
    <div class="relative z-10">
        <span class="mb-6 inline-flex w-fit items-center rounded-full border border-amber-300/25 bg-amber-300/10 px-4 py-2 text-xs font-medium uppercase tracking-[0.3em] text-amber-200">
            Daily Cosmic Guidance
        </span>
        <h1 class="max-w-3xl text-5xl leading-tight text-white sm:text-6xl [font-family:'Cormorant_Garamond',serif]">
            The Heavens Speak. <br><span class="text-amber-200">Are You Listening?</span>
        </h1>
        <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-300">
            Select your zodiac archetype to reveal today's energetic forecast, or use our precision tool to map your natal configuration.
        </p>
    </div>
    
    <div class="absolute right-0 top-0 -z-10 h-80 w-80 rounded-full bg-amber-300/5 blur-3xl sm:h-[32rem] sm:w-[32rem]"></div>
</section>

{{-- Zodiac Grid --}}
<section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($signs as $index => $sign)
            <a href="{{ route('horoscope.show', strtolower($sign['name'])) }}" class="group relative rounded-[2rem] border border-white/10 bg-slate-900/70 p-8 transition-all hover:border-amber-300/30 hover:bg-slate-900/90 hover:shadow-2xl hover:shadow-amber-300/5">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-[0.3em] text-amber-200/50">{{ sprintf('%02d', $index + 1) }}</span>
                    <div class="h-10 w-10 rounded-full bg-amber-300/10 flex items-center justify-center text-amber-200 group-hover:bg-amber-300 group-hover:text-slate-950 transition-colors">
                        ✦
                    </div>
                </div>
                <h3 class="mt-6 text-2xl text-white [font-family:'Cormorant_Garamond',serif]">{{ $sign['name'] }} <span class="text-amber-200/40 mx-1">/</span> <span class="text-amber-200">{{ $sign['nepali'] }}</span></h3>
                <p class="mt-4 text-sm leading-relaxed text-slate-400">Discover your daily energies, emotional rhythms, and aligned purpose for this cycle.</p>
                
                <div class="mt-6 flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest text-amber-200 opacity-0 transition group-hover:opacity-100">
                    <span>View Oracle</span>
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </div>
            </a>
        @endforeach
    </div>
</section>

@endsection
