@extends('layouts.app')

@section('title', 'AstroConnect | Contact')

@section('content')
<section class="mx-auto max-w-7xl px-4 pb-16 pt-14 sm:px-6 lg:px-8 lg:pb-24 lg:pt-20">
    <div class="grid gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:items-start">
        <div>
            <p class="text-sm uppercase tracking-[0.35em] text-amber-200/70">Contact</p>
            <h1 class="mt-4 text-5xl text-white sm:text-6xl [font-family:'Cormorant_Garamond',serif]">Start the conversation when you are ready for guidance.</h1>
            <p class="mt-6 max-w-xl text-lg leading-8 text-slate-300">This section provides the complete contact form UI inside Blade without changing your existing controllers or submission flow.</p>
            <div class="mt-8 rounded-[2rem] border border-white/10 bg-white/5 p-6 backdrop-blur">
                <p class="text-sm uppercase tracking-[0.25em] text-slate-400">Availability</p>
                <p class="mt-3 text-base leading-7 text-slate-300">Support for consultation inquiries, service questions, and horoscope guidance requests.</p>
            </div>
        </div>

        <div class="rounded-[2.5rem] border border-white/10 bg-slate-900/80 p-8 shadow-2xl shadow-slate-950/30">
            <form class="grid gap-5">
                <div class="grid gap-5 sm:grid-cols-2">
                    <label class="block">
                        <span class="mb-2 block text-sm font-medium text-slate-200">Full Name</span>
                        <input type="text" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-slate-100 placeholder:text-slate-500 focus:border-amber-300 focus:outline-none focus:ring-0" placeholder="Your name">
                    </label>
                    <label class="block">
                        <span class="mb-2 block text-sm font-medium text-slate-200">Email Address</span>
                        <input type="email" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-slate-100 placeholder:text-slate-500 focus:border-amber-300 focus:outline-none focus:ring-0" placeholder="name@example.com">
                    </label>
                </div>
                <label class="block">
                    <span class="mb-2 block text-sm font-medium text-slate-200">Subject</span>
                    <input type="text" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-slate-100 placeholder:text-slate-500 focus:border-amber-300 focus:outline-none focus:ring-0" placeholder="What would you like help with?">
                </label>
                <label class="block">
                    <span class="mb-2 block text-sm font-medium text-slate-200">Message</span>
                    <textarea rows="6" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-slate-100 placeholder:text-slate-500 focus:border-amber-300 focus:outline-none focus:ring-0" placeholder="Tell us a little about your question or the kind of reading you need."></textarea>
                </label>
                <button type="submit" class="inline-flex items-center justify-center rounded-full bg-amber-300 px-7 py-4 text-sm font-semibold uppercase tracking-[0.2em] text-slate-950 transition hover:bg-amber-200">
                    Send Message
                </button>
            </form>
        </div>
    </div>
</section>
@endsection
