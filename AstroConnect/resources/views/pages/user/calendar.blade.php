{{-- View: resources\views\pages\user\calendar.blade.php --}}
@extends('layouts.app')

@section('title', 'AstroConnect | Cosmic Nepali Calendar')

@php
    $stars = collect(range(1, 120))->map(function () {
        return [
            'size' => mt_rand(5, 20) / 10,
            'left' => mt_rand(0, 10000) / 100,
            'top' => mt_rand(0, 10000) / 100,
            'delay' => mt_rand(0, 500) / 100,
            'duration' => mt_rand(300, 1000) / 100,
        ];
    });

    $eventWidgetConfig = [
        'width' => 'responsive',
        'height' => 330,
        'defaultLanguage' => 'np',
        'apiId' => 44720260415468,
    ];
@endphp

@section('content')
<div class="relative min-h-screen bg-slate-950 overflow-hidden py-12 px-4 sm:px-6 lg:px-8">
    {{-- Dynamic Background Elements --}}
    <div class="absolute inset-0 pointer-events-none">
        <div id="stars-container" class="absolute inset-0">
            @foreach ($stars as $star)
                <div
                    class="star absolute bg-white rounded-full opacity-0"
                    style="
                        width: {{ $star['size'] }}px;
                        height: {{ $star['size'] }}px;
                        left: {{ $star['left'] }}%;
                        top: {{ $star['top'] }}%;
                        animation-duration: {{ $star['duration'] }}s;
                        animation-delay: {{ $star['delay'] }}s;
                    "
                ></div>
            @endforeach
        </div>
        <div class="absolute top-1/4 left-1/4 h-96 w-96 rounded-full bg-indigo-500/5 blur-[120px] animate-pulse"></div>
        <div class="absolute bottom-1/4 right-1/4 h-96 w-96 rounded-full bg-amber-500/5 blur-[120px] animate-pulse" style="animation-delay: 2s"></div>
    </div>

    <div class="relative z-10 mx-auto max-w-7xl">
        {{-- Page Header --}}
        <div class="text-center mb-16">
            <span class="inline-block text-[10px] font-bold uppercase tracking-[0.5em] text-amber-200/60 mb-4 animate-fade-in">Vikram Samvat</span>
            <h1 class="text-6xl md:text-8xl text-white font-serif tracking-tight leading-none bg-clip-text text-transparent bg-gradient-to-b from-white to-white/60 mb-6 [font-family:'Cormorant_Garamond',serif]">
                Cosmic <span class="italic font-light text-amber-200/40">Calendar</span>
            </h1>
            <p class="text-slate-400 max-w-xl mx-auto text-lg leading-relaxed font-light font-sans">
                Align your spirit with the celestial tides of the Nepali Patro. Tracking festivals, lunar phases, and auspicious moments.
            </p>
        </div>

        {{-- Main Dashboard Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            {{-- Main Calendar Section --}}
            <div class="lg:col-span-8 group">
                <div class="relative">
                    {{-- Decorative Glow --}}
                    <div class="absolute -inset-1 bg-gradient-to-r from-indigo-500/20 to-purple-500/20 rounded-[2.5rem] blur opacity-25 group-hover:opacity-40 transition duration-1000"></div>

                    <div class="relative bg-slate-900/40 backdrop-blur-3xl border border-white/5 rounded-[2.5rem] overflow-hidden shadow-2xl p-4 md:p-8">
                        <div class="flex items-center gap-3 mb-6 px-2">
                            <div class="h-2 w-2 rounded-full bg-amber-300 animate-ping"></div>
                            <h2 class="text-xs font-bold uppercase tracking-widest text-slate-400">Main Patro</h2>
                        </div>

                        <div class="calendar-wrapper min-h-[725px]">
                            {{-- Start of nepali calendar widget --}}
                            <script src="https://www.ashesh.com.np/calendar-widget/calendar.js?width=100%&height=725&tithi=1&event=1&radius=12&api=501249q146"></script>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar / Upcoming Events --}}
            <div class="lg:col-span-4 space-y-8">
                {{-- Events Widget --}}
                <div class="group relative">
                    <div class="absolute -inset-1 bg-gradient-to-b from-amber-500/20 to-transparent rounded-[2rem] blur opacity-20 group-hover:opacity-30 transition duration-1000"></div>

                    <div class="relative bg-slate-900/30 backdrop-blur-2xl border border-white/5 rounded-[2rem] p-6 shadow-xl">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xs font-bold uppercase tracking-widest text-amber-200/80">Upcoming Events</h2>
                            <span class="text-[10px] text-slate-500 font-mono tracking-tighter">LIVE FEED</span>
                        </div>

                        <div class="events-wrapper min-h-[330px]">
                            {{-- Start of upcoming event widget --}}
                            <script type="text/javascript"> <!--
                            var nc_ev_width = @js($eventWidgetConfig['width']);
                            var nc_ev_height = {{ $eventWidgetConfig['height'] }};
                            var nc_ev_def_lan = @js($eventWidgetConfig['defaultLanguage']);
                            var nc_ev_api_id = {{ $eventWidgetConfig['apiId'] }}; //-->
                            </script>
                            <script type="text/javascript" src="https://www.ashesh.com.np/calendar-event/ev.js"></script>
                        </div>
                    </div>
                </div>

                {{-- Celestial Tip Card --}}
                <div class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-indigo-600/10 to-transparent border border-indigo-500/10 p-8">
                    <div class="relative z-10">
                        <span class="text-amber-200 text-2xl mb-4 block">✦</span>
                        <h3 class="text-white font-medium mb-3 [font-family:'Cormorant_Garamond',serif] text-xl">Spiritual Insight</h3>
                        <p class="text-slate-400 text-sm leading-relaxed mb-6 font-light">
                            Each lunar phase in the Patro carries unique energy. Use the full moon (Purnima) for culmination and the new moon (Aunsi) for new beginnings.
                        </p>
                        <a href="{{ route('horoscope') }}" class="text-[10px] font-bold uppercase tracking-widest text-amber-200 hover:text-white transition-colors">
                            Explore Horoscope →
                        </a>
                    </div>
                    {{-- Decorative Circle --}}
                    <div class="absolute -right-10 -bottom-10 h-32 w-32 rounded-full bg-indigo-500/10 blur-2xl"></div>
                </div>

                {{-- Powered By --}}
                <div id="ncwidgetlink" class="text-center px-4">
                    <span class="text-slate-600 text-[9px] uppercase tracking-[0.2em]">Data provided by</span>
                    <a href="https://www.ashesh.com.np/nepali-calendar/" id="nclink" title="Nepali calendar" target="_blank" class="block text-slate-500 hover:text-amber-200 transition-colors text-[10px] uppercase font-bold mt-1">Ashesh Nepali Patro</a>
                </div>
            </div>

        </div>
    </div>
</div>

@push('styles')
<style>
    @keyframes fade-in { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes twinkle {
        0%, 100% { opacity: 0; transform: scale(0.5); }
        50% { opacity: 0.5; transform: scale(1.2); }
    }

    .animate-fade-in { animation: fade-in 1s ease-out forwards; }

    .star {
        animation-name: twinkle;
        animation-iteration-count: infinite;
        animation-timing-function: ease-in-out;
    }

    .calendar-wrapper iframe {
        border-radius: 1.5rem !important;
        background: transparent !important;
    }
</style>
@endpush
@endsection
