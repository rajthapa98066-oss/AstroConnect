@extends('layouts.user.master')

@section('title', $astrologer->user->name . ' | AstroConnect')

@section('content')
<section class="mx-auto max-w-7xl px-4 pb-16 pt-14 sm:px-6 lg:px-8 lg:pb-24 lg:pt-20">
    <div class="mb-8">
        <a href="{{ route('astrologers.index') }}" class="inline-flex items-center gap-2 rounded-full border border-white/10 px-4 py-2 text-sm font-medium text-slate-200 transition hover:border-white/20 hover:text-white">
            <span>←</span>
            <span>Back to Astrologers</span>
        </a>
    </div>

    <div class="grid gap-8 lg:grid-cols-[0.75fr_1.25fr]">
        <aside class="rounded-[2rem] border border-white/10 bg-gradient-to-b from-slate-900 to-slate-950 p-7 shadow-xl shadow-slate-950/30">
            <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-amber-300/15 text-3xl font-semibold text-amber-200">
                {{ strtoupper(substr($astrologer->user->name, 0, 1)) }}
            </div>

            <h1 class="mt-6 text-center text-4xl text-white [font-family:'Cormorant_Garamond',serif]">{{ $astrologer->user->name }}</h1>
            <p class="mt-2 text-center text-sm uppercase tracking-[0.25em] text-amber-200/80">{{ $astrologer->specialization }}</p>

            <div class="mt-6 space-y-3">
                <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-300">
                    Availability<br>
                    <span class="text-base font-semibold text-white">{{ ucfirst($astrologer->availability_status) }}</span>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-300">
                    Experience<br>
                    <span class="text-base font-semibold text-white">{{ $astrologer->experience_years }} years</span>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-300">
                    Consultation Fee<br>
                    <span class="text-base font-semibold text-white">{{ number_format((float) $astrologer->consultation_fee, 2) }}</span>
                </div>
            </div>
        </aside>

        <div class="rounded-[2rem] border border-white/10 bg-white/5 p-7 backdrop-blur sm:p-8">
            <p class="text-sm uppercase tracking-[0.35em] text-amber-200/70">About the Astrologer</p>
            <h2 class="mt-3 text-4xl text-white [font-family:'Cormorant_Garamond',serif]">Guidance style and reading focus</h2>
            <p class="mt-6 text-base leading-8 text-slate-300">{{ $astrologer->bio }}</p>

            @if (session('status') === 'appointment-booked')
                <div class="mt-6 rounded-2xl border border-emerald-300/20 bg-emerald-300/10 px-5 py-4 text-sm text-emerald-200">
                    Appointment request submitted successfully.
                </div>
            @endif

            @auth
                @if (auth()->user()->role === 'user')
                    <div class="mt-8 rounded-[1.75rem] border border-white/10 bg-slate-900/70 p-6">
                        <p class="text-sm uppercase tracking-[0.3em] text-amber-200/70">Book Session</p>
                        <h3 class="mt-3 text-3xl text-white [font-family:'Cormorant_Garamond',serif]">Request an appointment</h3>

                        <form method="POST" action="{{ route('appointments.store', $astrologer) }}" class="mt-5 space-y-4">
                            @csrf

                            <div>
                                <label for="scheduled_at" class="text-xs uppercase tracking-[0.18em] text-slate-400">Preferred date & time</label>
                                <input id="scheduled_at" type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" class="mt-2 block w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-slate-100 outline-none focus:border-amber-300/60 focus:ring-2 focus:ring-amber-300/30" required>
                                @error('scheduled_at')
                                    <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="topic" class="text-xs uppercase tracking-[0.18em] text-slate-400">Topic</label>
                                <input id="topic" type="text" name="topic" value="{{ old('topic') }}" class="mt-2 block w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-slate-100 outline-none focus:border-amber-300/60 focus:ring-2 focus:ring-amber-300/30" required>
                                @error('topic')
                                    <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="message" class="text-xs uppercase tracking-[0.18em] text-slate-400">Message (optional)</label>
                                <textarea id="message" name="message" rows="4" class="mt-2 block w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-slate-100 outline-none focus:border-amber-300/60 focus:ring-2 focus:ring-amber-300/30">{{ old('message') }}</textarea>
                                @error('message')
                                    <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit" class="inline-flex items-center justify-center rounded-full bg-amber-300 px-6 py-3 text-xs font-semibold uppercase tracking-[0.2em] text-slate-950 transition hover:bg-amber-200">
                                Book Appointment
                            </button>
                        </form>
                    </div>
                @endif
            @endauth

            <div class="mt-10 overflow-hidden rounded-[1.75rem] border border-amber-300/20 bg-[linear-gradient(135deg,rgba(120,53,15,0.28),rgba(15,23,42,0.9),rgba(2,6,23,1))] px-7 py-8">
                <p class="text-sm uppercase tracking-[0.35em] text-amber-200/80">Need a reading?</p>
                <h3 class="mt-3 text-3xl text-white [font-family:'Cormorant_Garamond',serif]">Start your consultation request today.</h3>
                <p class="mt-3 text-sm leading-7 text-slate-300">This astrologer profile is approved and visible in the AstroConnect directory. Reach out through contact channels to begin your session.</p>
                <a href="{{ route('contact') }}" class="mt-6 inline-flex items-center justify-center rounded-full bg-amber-300 px-6 py-3 text-xs font-semibold uppercase tracking-[0.2em] text-slate-950 transition hover:bg-amber-200">
                    Contact Now
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
