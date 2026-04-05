@extends('layouts.user.master')

@section('title', $post->title . ' | AstroConnect Blog')

@section('content')
<section class="mx-auto max-w-7xl px-4 pb-16 pt-14 sm:px-6 lg:px-8 lg:pb-24 lg:pt-20">
    <a href="{{ route('blog') }}" class="inline-flex items-center gap-2 rounded-full border border-white/10 px-4 py-2 text-sm font-medium text-slate-200 transition hover:border-white/20 hover:text-white">
        <span><-</span>
        <span>Back to Blog</span>
    </a>

    <article class="mt-8 rounded-[2rem] border border-white/10 bg-gradient-to-b from-slate-900 to-slate-950 p-8 shadow-xl shadow-slate-950/30">
        <p class="text-sm uppercase tracking-[0.3em] text-amber-200/70">{{ $post->category ?: 'Astrology' }}</p>
        <h1 class="mt-4 max-w-4xl text-5xl text-white sm:text-6xl [font-family:'Cormorant_Garamond',serif]">{{ $post->title }}</h1>
        <p class="mt-4 text-sm text-slate-400">Published {{ $post->published_at?->format('M d, Y h:i A') ?: $post->created_at?->format('M d, Y h:i A') }}</p>

        @if ($post->excerpt)
            <p class="mt-8 rounded-2xl border border-white/10 bg-white/5 p-5 text-base leading-8 text-slate-200">{{ $post->excerpt }}</p>
        @endif

        <div class="prose prose-invert mt-8 max-w-none prose-headings:[font-family:'Cormorant_Garamond',serif] prose-p:text-slate-300 prose-strong:text-white prose-a:text-amber-200">
            {!! nl2br(e($post->content)) !!}
        </div>
    </article>
</section>

@if ($relatedPosts->isNotEmpty())
    <section class="mx-auto max-w-7xl px-4 pb-20 sm:px-6 lg:px-8">
        <div class="mb-8">
            <p class="text-sm uppercase tracking-[0.35em] text-amber-200/70">Related Reading</p>
            <h2 class="mt-3 text-4xl text-white [font-family:'Cormorant_Garamond',serif]">More from the AstroConnect blog</h2>
        </div>

        <div class="grid gap-6 md:grid-cols-3">
            @foreach ($relatedPosts as $related)
                <article class="rounded-[2rem] border border-white/10 bg-slate-900/70 p-7">
                    <p class="text-sm uppercase tracking-[0.3em] text-amber-200/70">{{ $related->category ?: 'Astrology' }}</p>
                    <h3 class="mt-4 text-3xl text-white [font-family:'Cormorant_Garamond',serif]">{{ $related->title }}</h3>
                    <p class="mt-4 text-base leading-7 text-slate-300">{{ $related->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($related->content), 120) }}</p>
                    <a href="{{ route('blog.show', $related) }}" class="mt-6 inline-flex text-sm font-semibold uppercase tracking-[0.2em] text-amber-200 transition hover:text-amber-100">Read article</a>
                </article>
            @endforeach
        </div>
    </section>
@endif
@endsection
