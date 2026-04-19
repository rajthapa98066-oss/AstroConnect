{{-- View: resources\views\pages\astrologer\apply.blade.php --}}
@extends('layouts.app')

@section('title', 'AstroConnect | Apply as Astrologer')

@section('content')
<section class="mx-auto max-w-7xl px-4 pb-16 pt-14 sm:px-6 lg:px-8 lg:pb-24 lg:pt-20">
    <div class="grid gap-10 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
        <div>
            <p class="text-sm uppercase tracking-[0.35em] text-amber-200/70">Astrologer Application</p>
            <h1 class="mt-4 text-5xl text-white sm:text-6xl [font-family:'Cormorant_Garamond',serif]">Share your practice and apply to guide seekers on AstroConnect.</h1>
            <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-300">Complete your profile with specialization, experience, and consultation details. Your application will be reviewed by admin before going live.</p>
        </div>

        <div class="rounded-[2rem] border border-white/10 bg-white/5 p-8 backdrop-blur">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl border border-white/10 bg-slate-950/70 p-5">
                    <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Current status</p>
                    <p class="mt-2 text-2xl text-amber-200 [font-family:'Cormorant_Garamond',serif]">{{ ucfirst($astrologer?->verification_status ?? 'not applied') }}</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-slate-950/70 p-5">
                    <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Review flow</p>
                    <p class="mt-2 text-2xl text-amber-200 [font-family:'Cormorant_Garamond',serif]">Admin Approval</p>
                </div>
            </div>
            <p class="mt-6 text-sm leading-7 text-slate-300">When approved, your profile appears in the astrologer directory and dashboard tools become fully available.</p>
        </div>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 pb-20 sm:px-6 lg:px-8">
    <div class="rounded-[2rem] border border-white/10 bg-gradient-to-b from-slate-900 to-slate-950 p-7 shadow-xl shadow-slate-950/30 sm:p-8">
        @if (session('status') === 'application-submitted')
            <div class="mb-6 rounded-2xl border border-emerald-300/20 bg-emerald-300/10 px-5 py-4 text-sm font-medium text-emerald-200">
                Application submitted successfully. Your profile is now under review.
            </div>
        @endif

        @if (session('status') === 'application-in-review')
            <div class="mb-6 rounded-2xl border border-amber-300/20 bg-amber-300/10 px-5 py-4 text-sm font-medium text-amber-200">
                Your application is still pending review. You can apply again only after it is rejected.
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-2xl border border-rose-300/20 bg-rose-300/10 px-5 py-4 text-sm text-rose-200">
                <ul class="space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($canApply)
        <form method="POST" action="{{ route('astrologer.apply.store') }}" enctype="multipart/form-data" class="grid gap-6 lg:grid-cols-2">
            @csrf

            <div>
                <label for="specialization" class="text-xs font-medium uppercase tracking-[0.2em] text-slate-400">Specialization</label>
                <input id="specialization" type="text" name="specialization" value="{{ old('specialization', $astrologer?->specialization) }}" required
                    class="mt-2 block w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-slate-100 outline-none transition focus:border-amber-300/60 focus:ring-2 focus:ring-amber-300/30">
            </div>

            <div>
                <label for="experience_years" class="text-xs font-medium uppercase tracking-[0.2em] text-slate-400">Experience (Years)</label>
                <input id="experience_years" type="number" min="0" name="experience_years" value="{{ old('experience_years', $astrologer?->experience_years) }}" required
                    class="mt-2 block w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-slate-100 outline-none transition focus:border-amber-300/60 focus:ring-2 focus:ring-amber-300/30">
            </div>

            <div>
                <label for="consultation_fee" class="text-xs font-medium uppercase tracking-[0.2em] text-slate-400">Consultation Fee</label>
                <input id="consultation_fee" type="number" step="0.01" min="0" name="consultation_fee" value="{{ old('consultation_fee', $astrologer?->consultation_fee) }}" required
                    class="mt-2 block w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-slate-100 outline-none transition focus:border-amber-300/60 focus:ring-2 focus:ring-amber-300/30">
            </div>

            <div>
                <label for="availability_status" class="text-xs font-medium uppercase tracking-[0.2em] text-slate-400">Availability</label>
                <select id="availability_status" name="availability_status" required
                    class="mt-2 block w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-slate-100 outline-none transition focus:border-amber-300/60 focus:ring-2 focus:ring-amber-300/30">
                    @php($availability = old('availability_status', $astrologer?->availability_status ?? 'available'))
                    <option value="available" @selected($availability === 'available') class="bg-slate-100 text-slate-900">Available</option>
                    <option value="busy" @selected($availability === 'busy') class="bg-slate-100 text-slate-900">Busy</option>
                    <option value="unavailable" @selected($availability === 'unavailable') class="bg-slate-100 text-slate-900">Unavailable</option>
                </select>
            </div>

            <div class="lg:col-span-2">
                <label for="bio" class="text-xs font-medium uppercase tracking-[0.2em] text-slate-400">Bio</label>
                <textarea id="bio" name="bio" rows="5" required
                    class="mt-2 block w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-slate-100 outline-none transition placeholder:text-slate-500 focus:border-amber-300/60 focus:ring-2 focus:ring-amber-300/30">{{ old('bio', $astrologer?->bio) }}</textarea>
            </div>

            <div class="lg:col-span-2">
                <label for="profile_photo" class="text-xs font-medium uppercase tracking-[0.2em] text-slate-400">Profile Photo</label>
                <input id="profile_photo" type="file" name="profile_photo" accept="image/*"
                    class="mt-2 block w-full rounded-2xl border border-dashed border-white/20 bg-white/5 px-4 py-3 text-sm text-slate-300 file:mr-4 file:rounded-full file:border-0 file:bg-amber-300 file:px-4 file:py-2 file:text-xs file:font-semibold file:uppercase file:tracking-[0.2em] file:text-slate-950 hover:file:bg-amber-200">
            </div>

            <div class="lg:col-span-2 flex justify-end">
                <button type="submit" class="inline-flex items-center justify-center rounded-full bg-amber-300 px-7 py-3 text-xs font-semibold uppercase tracking-[0.2em] text-slate-950 transition hover:bg-amber-200">
                    Submit Application
                </button>
            </div>
        </form>
        @else
        <div class="rounded-2xl border border-amber-300/20 bg-amber-300/10 px-5 py-4 text-sm leading-7 text-amber-100">
            Your current application is pending review. Re-application is available only when your status changes to rejected.
        </div>
        @endif
    </div>
</section>
@endsection
