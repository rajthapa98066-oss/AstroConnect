{{-- View: resources\views\pages\user\blog.blade.php --}}
@extends('layouts.user.master')

@section('title', 'AstroConnect | Blog')

@section('content')
{{-- Blog landing hero for public readers. --}}
<section class="mx-auto max-w-7xl px-4 pb-16 pt-14 sm:px-6 lg:px-8 lg:pb-24 lg:pt-20">
    <p class="text-sm uppercase tracking-[0.35em] text-amber-200/70">Blog</p>
    <h1 class="mt-4 max-w-4xl text-5xl text-white sm:text-6xl [font-family:'Cormorant_Garamond',serif]">Editorial astrology content for readers who want context, not just headlines.</h1>
    <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-300">Discover insights curated by AstroConnect astrologers and editors across timing, relationships, purpose, and personal growth.</p>
</section>

<section class="mx-auto max-w-7xl px-4 pb-20 sm:px-6 lg:px-8">
    {{-- Paginated blog card grid showing only published content. --}}
    <div class="grid gap-6 lg:grid-cols-3">
        @forelse ($posts as $post)
            <article class="rounded-[2rem] border border-white/10 bg-slate-900/70 p-8">
                <p class="text-sm uppercase tracking-[0.3em] text-amber-200/70">{{ $post->category ?: 'Astrology' }}</p>
                <h2 class="mt-4 text-3xl text-white [font-family:'Cormorant_Garamond',serif]">{{ $post->title }}</h2>
                <p class="mt-4 text-sm text-slate-400">{{ $post->published_at?->format('M d, Y') ?: $post->created_at?->format('M d, Y') }}</p>
                <p class="mt-4 text-base leading-7 text-slate-300">{{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 140) }}</p>
                <a href="{{ route('blog.show', $post) }}" class="mt-8 inline-flex text-sm font-semibold uppercase tracking-[0.2em] text-amber-200 transition hover:text-amber-100">Read full article</a>
            </article>
        @empty
            <article class="rounded-[2rem] border border-white/10 bg-slate-900/70 p-8 lg:col-span-3">
                <h2 class="text-3xl text-white [font-family:'Cormorant_Garamond',serif]">No published blogs yet</h2>
                <p class="mt-4 text-base leading-7 text-slate-300">Admin can publish blog posts from the dashboard, and they will appear here automatically.</p>
            </article>
        @endforelse
    </div>

    {{-- Pagination controls with themed link styling. --}}
    <div class="mt-10 [&_nav]:flex [&_nav]:justify-center [&_nav]:text-slate-300 [&_span]:border-white/10 [&_span]:bg-white/5 [&_span]:text-slate-300 [&_a]:border-white/10 [&_a]:bg-white/5 [&_a]:text-slate-200 [&_a:hover]:bg-white/10">
        {{ $posts->links() }}
    </div>
</section>
@endsection
