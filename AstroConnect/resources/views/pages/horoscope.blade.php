@extends('layouts.app')

@section('title', 'AstroConnect | Horoscope')

@section('content')
@php
    $signs = [
        ['name' => 'Aries', 'dates' => 'Mar 21 - Apr 19'],
        ['name' => 'Taurus', 'dates' => 'Apr 20 - May 20'],
        ['name' => 'Gemini', 'dates' => 'May 21 - Jun 20'],
        ['name' => 'Cancer', 'dates' => 'Jun 21 - Jul 22'],
        ['name' => 'Leo', 'dates' => 'Jul 23 - Aug 22'],
        ['name' => 'Virgo', 'dates' => 'Aug 23 - Sep 22'],
        ['name' => 'Libra', 'dates' => 'Sep 23 - Oct 22'],
        ['name' => 'Scorpio', 'dates' => 'Oct 23 - Nov 21'],
        ['name' => 'Sagittarius', 'dates' => 'Nov 22 - Dec 21'],
        ['name' => 'Capricorn', 'dates' => 'Dec 22 - Jan 19'],
        ['name' => 'Aquarius', 'dates' => 'Jan 20 - Feb 18'],
        ['name' => 'Pisces', 'dates' => 'Feb 19 - Mar 20'],
    ];
@endphp

<section class="mx-auto max-w-7xl px-4 pb-16 pt-14 sm:px-6 lg:px-8 lg:pb-24 lg:pt-20">
    <p class="text-sm uppercase tracking-[0.35em] text-amber-200/70">Horoscope</p>
    <h1 class="mt-4 max-w-4xl text-5xl text-white sm:text-6xl [font-family:'Cormorant_Garamond',serif]">Explore zodiac energies through a clear, responsive horoscope grid.</h1>
    <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-300">Choose your sign and begin with the symbolic rhythm that fits your season, temperament, and current path.</p>
</section>

<section class="mx-auto max-w-7xl px-4 pb-20 sm:px-6 lg:px-8">
    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($signs as $sign)
            <article class="rounded-[2rem] border border-white/10 bg-gradient-to-b from-slate-900 to-slate-950 p-6 transition hover:-translate-y-1 hover:border-amber-300/30">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-amber-300/15 text-amber-200">✦</div>
                <h2 class="mt-5 text-3xl text-white [font-family:'Cormorant_Garamond',serif]">{{ $sign['name'] }}</h2>
                <p class="mt-2 text-sm uppercase tracking-[0.2em] text-slate-400">{{ $sign['dates'] }}</p>
                <p class="mt-4 text-sm leading-7 text-slate-300">A moment to notice emotional patterns, trust momentum, and align your next decision with purpose.</p>
            </article>
        @endforeach
    </div>
</section>
@endsection
