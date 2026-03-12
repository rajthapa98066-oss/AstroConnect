@extends('layouts.app')

@section('title', 'AstroConnect | Blog')

@section('content')
@php
    $posts = [
        ['category' => 'Moon Phases', 'title' => 'How lunar cycles shape emotional timing and clarity', 'excerpt' => 'A practical look at how new moons and full moons influence rest, momentum, and decision-making.'],
        ['category' => 'Relationships', 'title' => 'Reading compatibility beyond sun signs', 'excerpt' => 'Why deeper synastry patterns often reveal more than surface-level zodiac comparisons.'],
        ['category' => 'Career', 'title' => 'Using astrology to navigate major career pivots', 'excerpt' => 'Learn how planetary timing can frame growth, patience, and strategic risk-taking.'],
    ];
@endphp

<section class="mx-auto max-w-7xl px-4 pb-16 pt-14 sm:px-6 lg:px-8 lg:pb-24 lg:pt-20">
    <p class="text-sm uppercase tracking-[0.35em] text-amber-200/70">Blog</p>
    <h1 class="mt-4 max-w-4xl text-5xl text-white sm:text-6xl [font-family:'Cormorant_Garamond',serif]">Editorial astrology content for readers who want context, not just headlines.</h1>
    <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-300">This page presents a blog-style layout inside your Blade structure, ready for future dynamic content when you choose to wire it.</p>
</section>

<section class="mx-auto max-w-7xl px-4 pb-20 sm:px-6 lg:px-8">
    <div class="grid gap-6 lg:grid-cols-3">
        @foreach ($posts as $post)
            <article class="rounded-[2rem] border border-white/10 bg-slate-900/70 p-8">
                <p class="text-sm uppercase tracking-[0.3em] text-amber-200/70">{{ $post['category'] }}</p>
                <h2 class="mt-4 text-3xl text-white [font-family:'Cormorant_Garamond',serif]">{{ $post['title'] }}</h2>
                <p class="mt-4 text-base leading-7 text-slate-300">{{ $post['excerpt'] }}</p>
                <a href="{{ url('/contact') }}" class="mt-8 inline-flex text-sm font-semibold uppercase tracking-[0.2em] text-amber-200 transition hover:text-amber-100">Read more inspiration</a>
            </article>
        @endforeach
    </div>
</section>
@endsection
