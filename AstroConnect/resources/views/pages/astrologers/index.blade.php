@extends('layouts.app')

@section('title', 'AstroConnect | Astrologers')

@section('content')
<section class="mx-auto max-w-7xl px-4 pb-16 pt-14 sm:px-6 lg:px-8 lg:pb-24 lg:pt-20">
    <div class="grid gap-10 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
        <div>
            <p class="text-sm uppercase tracking-[0.35em] text-amber-200/70">Astrologer Directory</p>
            <h1 class="mt-4 text-5xl text-white sm:text-6xl [font-family:'Cormorant_Garamond',serif]">Connect with readers who match your energy and intent.</h1>
            <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-300">Browse approved astrologers, compare specialties, and choose the guidance style that fits your questions best.</p>
        </div>
        <div class="rounded-[2rem] border border-white/10 bg-white/5 p-8 backdrop-blur">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl border border-white/10 bg-slate-950/70 p-5">
                    <p class="text-3xl text-amber-200 [font-family:'Cormorant_Garamond',serif]">{{ $astrologers->total() }}</p>
                    <p class="mt-2 text-sm uppercase tracking-[0.25em] text-slate-400">Approved astrologers</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-slate-950/70 p-5">
                    <p class="text-3xl text-amber-200 [font-family:'Cormorant_Garamond',serif]">{{ $astrologers->count() }}</p>
                    <p class="mt-2 text-sm uppercase tracking-[0.25em] text-slate-400">Shown this page</p>
                </div>
            </div>
            <p class="mt-6 text-sm leading-7 text-slate-300">Need a reading for love, timing, career, or emotional clarity? Start by exploring specialists below.</p>
        </div>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 pb-20 sm:px-6 lg:px-8">
    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($astrologers as $astrologer)
            <article class="rounded-[2rem] border border-white/10 bg-gradient-to-b from-slate-900 to-slate-950 p-7 shadow-xl shadow-slate-950/30">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-amber-300/15 text-lg font-semibold text-amber-200">
                        {{ strtoupper(substr($astrologer->user->name, 0, 1)) }}
                    </div>
                    <span class="rounded-full border border-emerald-400/20 bg-emerald-400/10 px-3 py-1 text-xs font-medium uppercase tracking-[0.2em] text-emerald-200">
                        {{ ucfirst($astrologer->availability_status) }}
                    </span>
                </div>

                <h2 class="mt-6 text-3xl text-white [font-family:'Cormorant_Garamond',serif]">{{ $astrologer->user->name }}</h2>
                <p class="mt-2 text-sm uppercase tracking-[0.25em] text-amber-200/80">{{ $astrologer->specialization }}</p>

                <div class="mt-6 grid gap-3 text-sm text-slate-300 sm:grid-cols-2">
                    <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                        Experience<br>
                        <span class="text-base font-semibold text-white">{{ $astrologer->experience_years }} years</span>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                        Consultation fee<br>
                        <span class="text-base font-semibold text-white">{{ number_format((float) $astrologer->consultation_fee, 2) }}</span>
                    </div>
                </div>

                <p class="mt-6 text-base leading-7 text-slate-300">{{ \Illuminate\Support\Str::limit($astrologer->bio, 180) }}</p>

                <a href="{{ route('astrologers.show', $astrologer) }}" class="mt-8 inline-flex items-center justify-center rounded-full bg-amber-300 px-6 py-3 text-sm font-semibold uppercase tracking-[0.2em] text-slate-950 transition hover:bg-amber-200">
                    View Profile
                </a>
            </article>
        @empty
            <div class="rounded-[2rem] border border-white/10 bg-slate-900/70 p-8 text-slate-300 md:col-span-2 xl:col-span-3">
                No approved astrologers are available right now.
            </div>
        @endforelse
    </div>

    <div class="mt-10 [&_nav]:flex [&_nav]:justify-center [&_nav]:text-slate-300 [&_span]:border-white/10 [&_span]:bg-white/5 [&_span]:text-slate-300 [&_a]:border-white/10 [&_a]:bg-white/5 [&_a]:text-slate-200 [&_a:hover]:bg-white/10">
        {{ $astrologers->links() }}
    </div>
</section>
@endsection
