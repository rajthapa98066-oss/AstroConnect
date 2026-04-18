@extends('layouts.app')

@section('title', 'Compatibility Result - AstroConnect')

@section('content')
<div class="relative py-16 sm:py-24 min-h-[80vh] flex items-center justify-center">
    <!-- Starry background effects -->
    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
        @if(isset($predictionResult['is_compatible']) && $predictionResult['is_compatible'])
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 h-[800px] w-[800px] rounded-full bg-pink-600/20 blur-[120px]"></div>
        @else
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 h-[800px] w-[800px] rounded-full bg-slate-600/20 blur-[120px]"></div>
        @endif
    </div>

    <div class="relative z-10 mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 text-center w-full">
        
        @php
            $isCompatible = $predictionResult['is_compatible'] ?? false;
            $predictionText = $predictionResult['prediction'] ?? 'Unknown Compatibility';
            $confidence = isset($predictionResult['confidence_score']) ? round($predictionResult['confidence_score'] * 100, 1) : 0;
        @endphp

        <div class="inline-flex items-center rounded-full px-4 py-1.5 text-sm font-semibold leading-6 {{ $isCompatible ? 'text-pink-300 ring-pink-500/20 bg-pink-500/10' : 'text-slate-300 ring-slate-500/20 bg-slate-500/10' }} ring-1 ring-inset mb-8 backdrop-blur-md">
            Ashtakoot Analysis Complete
        </div>
        
        <h1 class="font-serif text-3xl font-light text-slate-300 sm:text-4xl lg:text-5xl mb-4">
            Cosmic Connection
        </h1>
        
        <div class="relative my-10 py-14 border-y border-white/5 bg-gradient-to-r from-transparent via-white/5 to-transparent backdrop-blur-sm">
            <h2 class="font-serif text-5xl sm:text-6xl lg:text-7xl font-bold tracking-tight {{ $isCompatible ? 'text-white  drop-shadow-[0_0_20px_rgba(236,72,153,0.6)]' : 'text-slate-400' }}">
                {{ $predictionText }}
            </h2>
            
            <div class="mt-6 inline-flex items-center justify-center gap-2 text-xl font-medium">
                <span class="text-slate-400 font-light">Confidence Score:</span>
                <span class="{{ $isCompatible ? 'text-pink-400' : 'text-slate-300' }} drop-shadow-md">
                    {{ $confidence }}%
                </span>
            </div>
        </div>
        
        <p class="mt-8 text-lg text-purple-200/80 mb-12 max-w-2xl mx-auto leading-relaxed">
            @if($isCompatible)
                The stars celebrate this union. Based on thorough analysis of your koota points, Varna, Vashya, and celestial alignments, this match holds profound potential for harmony.
            @else
                The cosmic energy suggests challenges in this alignment. While stars guide us, remember that conscious effort and understanding can bridge celestial differences.
            @endif
        </p>

        <!-- Koota Breakdown display -->
        <div class="mt-8 px-6 py-8 rounded-3xl bg-slate-900/50 border border-white/5 backdrop-blur-xl text-left max-w-3xl mx-auto">
            <h3 class="text-lg font-medium text-white mb-6 uppercase tracking-widest text-center text-purple-300">Detailed Gun Milan Analysis</h3>
            
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                @php
                    $scores = [
                        'Varna' => $kootaData['varna_score'] ?? '-',
                        'Vashya' => $kootaData['vashya_score'] ?? '-',
                        'Tara' => $kootaData['tara_score'] ?? '-',
                        'Yoni' => $kootaData['yoni_score'] ?? '-',
                        'Graha Maitri' => $kootaData['graha_maitri_score'] ?? '-',
                        'Gana' => $kootaData['gana_score'] ?? '-',
                        'Bhakoot' => $kootaData['bhakoot_score'] ?? '-',
                        'Nadi' => $kootaData['nadi_score'] ?? '-'
                    ];
                @endphp
                @foreach($scores as $name => $score)
                    <div class="p-4 rounded-2xl bg-slate-800/60 border border-white/5 text-center">
                        <div class="text-xs uppercase tracking-wider text-slate-400 mb-1">{{ $name }}</div>
                        <div class="text-2xl font-serif text-white font-medium">{{ $score }}</div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-6 pt-6 border-t border-white/5 flex gap-4 justify-between items-center text-sm text-slate-400">
                <div>Sign Moon Person 1: <span class="text-slate-200">{{ $kootaData['person1_moon_sign'] ?? 'Unknown' }}</span></div>
                <div>Sign Moon Person 2: <span class="text-slate-200">{{ $kootaData['person2_moon_sign'] ?? 'Unknown' }}</span></div>
            </div>
        </div>

        <div class="mt-12 mb-8">
            <a href="{{ route('predictions.compatibility') }}" class="text-sm font-semibold leading-6 text-pink-400 hover:text-pink-300 transition-colors">
                <span aria-hidden="true">&larr;</span> Check another match
            </a>
        </div>
    </div>
</div>
@endsection
