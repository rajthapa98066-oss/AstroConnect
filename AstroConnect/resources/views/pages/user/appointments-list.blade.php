{{-- View: resources\views\pages\user\appointments-list.blade.php --}}
@extends('layouts.user.master')

@section('title', 'AstroConnect | My Appointments')

@section('content')
{{-- Appointments page heading for logged-in users. --}}
<section class="mx-auto max-w-7xl px-4 pb-16 pt-14 sm:px-6 lg:px-8 lg:pb-24 lg:pt-20">
    <p class="text-sm uppercase tracking-[0.35em] text-amber-200/70">My Appointments</p>
    <h1 class="mt-4 text-5xl text-white sm:text-6xl [font-family:'Cormorant_Garamond',serif]">Track your consultation requests and upcoming sessions.</h1>
    <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-300">Completed sessions can be rated directly from this page.</p>
</section>

{{-- Paginated appointment table and status badges. --}}
<section class="mx-auto max-w-7xl px-4 pb-20 sm:px-6 lg:px-8">
    @if (session('status') === 'session-rating-saved')
        <div class="mb-6 rounded-2xl border border-emerald-300/20 bg-emerald-300/10 px-5 py-4 text-sm text-emerald-200">
            Session rating saved successfully.
        </div>
    @endif

    @if (session('status') === 'review-saved')
        <div class="mb-6 rounded-2xl border border-emerald-300/20 bg-emerald-300/10 px-5 py-4 text-sm text-emerald-200">
            Thanks for sharing your review.
        </div>
    @endif

    <div class="rounded-[2rem] border border-white/10 bg-gradient-to-b from-slate-900 to-slate-950 p-7 shadow-xl shadow-slate-950/30 sm:p-8">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm text-slate-300">
                <thead class="border-b border-white/10 text-xs uppercase tracking-[0.2em] text-slate-400">
                    <tr>
                        <th class="px-3 py-3">Astrologer</th>
                        <th class="px-3 py-3">Topic</th>
                        <th class="px-3 py-3">Schedule</th>
                        <th class="px-3 py-3">Status</th>
                        <th class="px-3 py-3">Payment</th>
                        <th class="px-3 py-3">Feedback Prompt</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($appointments as $appointment)
                        @php($myReview = $myReviews->get($appointment->astrologer_id))
                        <tr class="border-b border-white/5">
                            <td class="px-3 py-4">{{ $appointment->astrologer->user->name }}</td>
                            <td class="px-3 py-4">{{ $appointment->topic }}</td>
                            <td class="px-3 py-4">{{ $appointment->scheduled_at->format('M d, Y h:i A') }}</td>
                            <td class="px-3 py-4">
                                <span class="rounded-full border border-white/15 bg-white/5 px-3 py-1 text-xs uppercase tracking-[0.18em] text-white">{{ $appointment->status }}</span>
                            </td>
                            <td class="px-3 py-4">
                                @if ($appointment->isPaid())
                                    <span class="inline-flex items-center gap-1.5 text-xs font-medium uppercase tracking-[0.15em] text-emerald-400">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                                        Paid
                                    </span>
                                @elseif ($appointment->status === 'completed')
                                    <form method="POST" action="{{ route('khalti.initiate', $appointment) }}">
                                        @csrf
                                        <button type="submit" class="group relative flex items-center gap-2 rounded-full border border-amber-300/30 bg-amber-300/10 px-4 py-1.5 text-[10px] font-bold uppercase tracking-[0.2em] text-amber-200 transition-all hover:bg-amber-300 hover:text-slate-950">
                                            <span>Pay Rs. {{ number_format($appointment->astrologer->consultation_fee, 2) }}</span>
                                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14m-7-7 7 7-7 7"/></svg>
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs uppercase tracking-[0.15em] text-slate-500 italic">Bill Pending</span>
                                @endif
                            </td>
                            <td class="px-3 py-4 align-top">
                                @if ($appointment->status === 'completed')
                                    <p class="mb-2 text-xs uppercase tracking-[0.16em] text-amber-200/80">Session completed. Review is optional.</p>
                                    <form method="POST" action="{{ route('reviews.store', $appointment->astrologer) }}" class="space-y-3">
                                        @csrf
                                        <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">
                                        <input type="hidden" name="redirect_to" value="appointments">

                                        <div>
                                            <label for="rating-{{ $appointment->id }}" class="sr-only">Session rating</label>
                                            <select id="rating-{{ $appointment->id }}" name="rating" class="block w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-xs uppercase tracking-[0.16em] text-slate-100 outline-none">
                                                <option value="" disabled {{ old('rating', $myReview?->rating ?? '') === '' ? 'selected' : '' }} class="bg-slate-100 text-slate-900">Select rating</option>
                                                @for ($rating = 5; $rating >= 1; $rating--)
                                                    <option value="{{ $rating }}" @selected((string) old('rating', $myReview?->rating ?? '') === (string) $rating) class="bg-slate-100 text-slate-900">{{ $rating }}/5</option>
                                                @endfor
                                            </select>
                                            @error('rating')
                                                <p class="mt-2 text-xs text-rose-300">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="comment-{{ $appointment->id }}" class="sr-only">Review comment</label>
                                            <textarea id="comment-{{ $appointment->id }}" name="comment" rows="3" class="block w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-xs text-slate-100 outline-none" placeholder="Share your experience..." required>{{ old('comment', $myReview?->comment ?? '') }}</textarea>
                                            @error('comment')
                                                <p class="mt-2 text-xs text-rose-300">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <button type="submit" class="rounded-full bg-amber-300 px-3 py-2 text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-950">
                                            {{ $myReview ? 'Update Review' : 'Submit Review' }}
                                        </button>

                                        @if ($myReview)
                                            <p class="text-xs uppercase tracking-[0.18em] text-amber-200/80">Current review: {{ $myReview->rating }}/5</p>
                                        @endif
                                    </form>
                                @else
                                    <span class="text-slate-500">Prompt appears after completion</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-8 text-center text-slate-400">No appointments booked yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination controls with shared user-theme styling. --}}
        <div class="mt-8 [&_nav]:flex [&_nav]:justify-center [&_nav]:text-slate-300 [&_span]:border-white/10 [&_span]:bg-white/5 [&_span]:text-slate-300 [&_a]:border-white/10 [&_a]:bg-white/5 [&_a]:text-slate-200 [&_a:hover]:bg-white/10">
            {{ $appointments->links() }}
        </div>
    </div>
</section>
@endsection
