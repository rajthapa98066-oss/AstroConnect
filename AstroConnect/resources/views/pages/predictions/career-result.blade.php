@extends('layouts.app')

@section('title', 'Your Career Prediction - AstroConnect')

@section('content')
<div class="relative py-16 sm:py-24 min-h-[80vh] flex items-center justify-center">
    <!-- Starry background effects -->
    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 h-[800px] w-[800px] rounded-full bg-indigo-600/20 blur-[100px]"></div>
        <div class="absolute top-0 right-0 h-[500px] w-[500px] rounded-full bg-amber-500/10 blur-[100px]"></div>
    </div>

    <div class="relative z-10 mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 text-center w-full">
        <div class="inline-flex items-center rounded-full px-4 py-1.5 text-sm font-semibold leading-6 text-indigo-300 ring-1 ring-inset ring-indigo-500/20 bg-indigo-500/10 mb-8 overflow-hidden backdrop-blur-md">
            The Stars Have Spoken
        </div>
        
        <h1 class="font-serif text-3xl font-light text-slate-300 sm:text-4xl lg:text-5xl mb-4">
            Your Predicted Path is
        </h1>
        
        <div class="relative my-8 py-12 border-y border-white/5 bg-gradient-to-r from-transparent via-white/5 to-transparent backdrop-blur-sm">
            <h2 class="font-serif text-6xl sm:text-7xl lg:text-8xl font-bold tracking-tight text-white drop-shadow-[0_0_15px_rgba(251,191,36,0.5)]">
                {{ $predictedCareer }}
            </h2>
        </div>
        
        <p class="mt-8 text-lg text-indigo-200/80 mb-12 max-w-2xl mx-auto leading-relaxed">
            Based on your precise planetary alignments at the moment of your birth, our AI models indicate a strong natural affinity and cosmic alignment towards this profession.
        </p>

        <!-- Planetary Configuration display -->
        <div class="mt-8 px-6 py-8 rounded-3xl bg-slate-900/50 border border-white/5 backdrop-blur-xl">
            <h3 class="text-xl font-medium text-white mb-6 uppercase tracking-widest text-sm text-indigo-300">Your Planetary Signature</h3>
            
            <div class="flex flex-wrap justify-center gap-3">
                @foreach($planetarySigns as $planet => $sign)
                    <div class="px-4 py-2 rounded-xl bg-slate-800/80 border border-white/10 text-sm">
                        <span class="text-amber-300/80 mr-2 capitalize">{{ str_replace('_sign', '', $planet) }}:</span>
                        <span class="text-white font-medium">{{ $sign }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-12">
            <a href="{{ route('predictions.career') }}" class="text-sm font-semibold leading-6 text-indigo-400 hover:text-indigo-300 transition-colors">
                <span aria-hidden="true">&larr;</span> Calculate another prediction
            </a>
        </div>
    </div>
</div>
@endsection
