{{-- View: resources\views\pages\user\astrologer-profile.blade.php --}}
@extends('layouts.user.master')

@section('title', $astrologer->user->name . ' | AstroConnect')

@section('content')
{{-- Profile page shell with back navigation and two-column layout. --}}
<section class="mx-auto max-w-7xl px-4 pb-16 pt-14 sm:px-6 lg:px-8 lg:pb-24 lg:pt-20">
    <div class="mb-8">
        <a href="{{ route('astrologers.index') }}" class="inline-flex items-center gap-2 rounded-full border border-white/10 px-4 py-2 text-sm font-medium text-slate-200 transition hover:border-white/20 hover:text-white">
            <span>←</span>
            <span>Back to Astrologers</span>
        </a>
    </div>

    <div class="grid gap-8 lg:grid-cols-[0.75fr_1.25fr]">
        {{-- Left summary card with key astrologer attributes. --}}
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
                <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-300">
                    Reviews<br>
                    @if ($astrologer->reviews_count > 0)
                        <span class="text-base font-semibold text-white">{{ number_format((float) $astrologer->reviews_avg_rating, 1) }}/5 from {{ $astrologer->reviews_count }} users</span>
                    @else
                        <span class="text-base font-semibold text-white">No reviews yet</span>
                    @endif
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-300">
                    Session Ratings<br>
                    @if ($astrologer->rated_sessions_count > 0 && $astrologer->appointments_avg_rating)
                        <span class="text-base font-semibold text-white">{{ number_format((float) $astrologer->appointments_avg_rating, 1) }}/5 from {{ $astrologer->rated_sessions_count }} sessions</span>
                    @else
                        <span class="text-base font-semibold text-white">No session ratings yet</span>
                    @endif
                </div>
            </div>
        </aside>

        {{-- Right content panel including bio and booking form. --}}
        <div class="rounded-[2rem] border border-white/10 bg-white/5 p-7 backdrop-blur sm:p-8">
            <p class="text-sm uppercase tracking-[0.35em] text-amber-200/70">About the Astrologer</p>
            <h2 class="mt-3 text-4xl text-white [font-family:'Cormorant_Garamond',serif]">Guidance style and reading focus</h2>
            <p class="mt-6 text-base leading-8 text-slate-300">{{ $astrologer->bio }}</p>

            @if (session('status') === 'appointment-booked')
                <div class="mt-6 rounded-2xl border border-emerald-300/20 bg-emerald-300/10 px-5 py-4 text-sm text-emerald-200">
                    Appointment request submitted successfully.
                </div>
            @endif

            @if (session('status') === 'review-saved')
                <div class="mt-6 rounded-2xl border border-emerald-300/20 bg-emerald-300/10 px-5 py-4 text-sm text-emerald-200">
                    Review submitted successfully.
                </div>
            @endif

            @auth
                @if (auth()->user()->role === 'user')
                    {{-- Authenticated user booking form for appointment requests. --}}
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

            <div class="mt-8 rounded-[1.75rem] border border-white/10 bg-slate-900/70 p-6">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <p class="text-sm uppercase tracking-[0.3em] text-amber-200/70">Client Reviews</p>
                        <h3 class="mt-3 text-3xl text-white [font-family:'Cormorant_Garamond',serif]">What users are saying</h3>
                    </div>
                    @if ($astrologer->reviews_count > 0)
                        <p class="text-sm text-slate-400">Average rating {{ number_format((float) $astrologer->reviews_avg_rating, 1) }}/5</p>
                    @endif
                </div>

                <div class="mt-6 space-y-4">
                    @forelse ($reviews as $review)
                        <article class="rounded-2xl border border-white/10 bg-white/5 p-5">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-white">{{ $review->user->name }}</p>
                                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Rated {{ $review->rating }}/5</p>
                                </div>
                                <time class="text-xs uppercase tracking-[0.2em] text-slate-500" datetime="{{ $review->created_at->toDateString() }}">
                                    {{ $review->created_at->format('M j, Y') }}
                                </time>
                            </div>

                            <p class="mt-4 text-sm leading-7 text-slate-300">{{ $review->comment }}</p>
                        </article>
                    @empty
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-5 text-sm text-slate-300">
                            No reviews have been posted yet.
                        </div>
                    @endforelse
                </div>

                @auth
                    @if (auth()->user()->role === 'user')
                        @if ($hasCompletedSession)
                            <div class="mt-8 rounded-2xl border border-amber-300/20 bg-amber-300/10 p-5">
                                <p class="text-sm uppercase tracking-[0.25em] text-amber-200/70">Leave a review</p>
                                <p class="mt-2 text-sm text-slate-200">You can review this astrologer because you have completed at least one session.</p>

                                <form method="POST" action="{{ route('reviews.store', $astrologer) }}" class="mt-5 space-y-4">
                                    @csrf
                                    <input type="hidden" name="appointment_id" value="{{ $reviewAppointmentId }}">
                                    <input type="hidden" name="redirect_to" value="profile">

                                    <div>
                                        <label for="rating" class="text-xs uppercase tracking-[0.18em] text-slate-400">Rating</label>
                                        <select id="rating" name="rating" class="mt-2 block w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-slate-100 outline-none focus:border-amber-300/60 focus:ring-2 focus:ring-amber-300/30" required>
                                            <option value="" disabled {{ old('rating', $myReview?->rating ?? '') === '' ? 'selected' : '' }} class="bg-slate-100 text-slate-900">Select rating</option>
                                            @for ($rating = 5; $rating >= 1; $rating--)
                                                <option value="{{ $rating }}" @selected((string) old('rating', $myReview?->rating ?? '') === (string) $rating) class="bg-slate-100 text-slate-900">{{ $rating }}/5</option>
                                            @endfor
                                        </select>
                                        @error('rating')
                                            <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="comment" class="text-xs uppercase tracking-[0.18em] text-slate-400">Review</label>
                                        <textarea id="comment" name="comment" rows="4" class="mt-2 block w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-slate-100 outline-none focus:border-amber-300/60 focus:ring-2 focus:ring-amber-300/30" required>{{ old('comment', $myReview?->comment ?? '') }}</textarea>
                                        @error('comment')
                                            <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <button type="submit" class="inline-flex items-center justify-center rounded-full bg-amber-300 px-6 py-3 text-xs font-semibold uppercase tracking-[0.2em] text-slate-950 transition hover:bg-amber-200">
                                        {{ $myReview ? 'Update Review' : 'Submit Review' }}
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="mt-8 rounded-2xl border border-white/10 bg-white/5 p-5">
                                <p class="text-sm uppercase tracking-[0.25em] text-amber-200/70">Review after session</p>
                                <p class="mt-2 text-sm leading-7 text-slate-300">You can submit a review only after at least one appointment is marked completed.</p>
                            </div>
                        @endif
                    @endif
                @else
                    <div class="mt-8 rounded-2xl border border-white/10 bg-white/5 p-5">
                        <p class="text-sm uppercase tracking-[0.25em] text-amber-200/70">Sign in to review</p>
                        <p class="mt-2 text-sm leading-7 text-slate-300">Guests can read reviews here, but only signed-in users can leave their own review.</p>
                        <a href="{{ route('login') }}" class="mt-4 inline-flex items-center justify-center rounded-full bg-amber-300 px-6 py-3 text-xs font-semibold uppercase tracking-[0.2em] text-slate-950 transition hover:bg-amber-200">
                            Sign In
                        </a>
                    </div>
                @endauth
            </div>

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
