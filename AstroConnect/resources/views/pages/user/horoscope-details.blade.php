{{-- View: resources\views\pages\user\horoscope-details.blade.php --}}
@extends('layouts.app')

@section('title', 'AstroConnect | ' . $sign . ' Prediction')

@section('content')
<section class="relative min-h-[80vh] flex flex-col items-center justify-center py-20 px-4">
    <div class="absolute inset-0 -z-10 overflow-hidden">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 h-[40rem] w-[40rem] rounded-full bg-amber-300/5 blur-[120px]"></div>
    </div>

    <div class="mx-auto max-w-4xl w-full text-center relative z-10">
        {{-- Custom Page Header --}}
        <div class="mb-12">
            <a href="{{ route('horoscope') }}" class="group inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.3em] text-slate-500 transition hover:text-amber-200">
                <svg class="h-4 w-4 transition group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                Zodiac Directory
            </a>
        </div>

        <span class="mb-4 inline-block text-[10px] font-bold uppercase tracking-[0.5em] text-amber-200/60">Insight • {{ $date }}</span>
        <h1 class="text-7xl md:text-9xl text-white [font-family:'Cormorant_Garamond',serif] leading-none">
            {{ $sign }} <span class="text-amber-200/20 md:text-6xl text-4xl block md:inline-block mt-4 md:mt-0 font-light italic">/ {{ $nepaliSign }}</span>
        </h1>

        {{-- The Oracle Card --}}
        <div class="mt-16 mx-auto max-w-3xl">
            <div class="relative rounded-[3rem] border border-white/10 bg-slate-900/60 p-10 md:p-20 shadow-2xl backdrop-blur-xl">
                <div class="absolute -top-4 left-1/2 -translate-x-1/2 rounded-full border border-white/10 bg-slate-950 px-6 py-1 text-[10px] font-bold uppercase tracking-[0.3em] text-amber-200">
                    Prophecy
                </div>
                
                <p class="text-2xl md:text-4xl text-white leading-relaxed [font-family:'Cormorant_Garamond',serif] italic font-light">
                    "{{ $prediction }}"
                </p>

                <div class="mt-12 flex items-center justify-center gap-6">
                    <div class="h-px w-10 bg-white/10"></div>
                    <span class="text-amber-300/40 text-xl">✦</span>
                    <div class="h-px w-10 bg-white/10"></div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
