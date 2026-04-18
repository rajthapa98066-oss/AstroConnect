{{-- View: resources\views\pages\user\payment-result.blade.php --}}
@extends('layouts.user.master')

@section('title', 'AstroConnect | Payment Result')

@section('content')
<section class="mx-auto max-w-7xl px-4 pb-20 pt-14 sm:px-6 lg:px-8 lg:pb-32 lg:pt-20">
    <div class="flex flex-col items-center justify-center text-center">
        @if ($success)
            <div class="mb-8 flex h-24 w-24 items-center justify-center rounded-full bg-emerald-500/10 text-emerald-400 shadow-[0_0_50px_rgba(16,185,129,0.2)]">
                <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-5xl text-white [font-family:'Cormorant_Garamond',serif]">Payment Successful</h1>
            <p class="mt-6 max-w-lg text-lg text-slate-300">
                Thank you for your payment. Your consultation fee has been processed successfully.
            </p>
            <div class="mt-8 rounded-2xl border border-white/10 bg-white/5 p-6 backdrop-blur">
                <p class="text-xs uppercase tracking-widest text-slate-400">Transaction ID</p>
                <p class="mt-1 font-mono text-amber-200">{{ $transaction_id }}</p>
            </div>
        @else
            <div class="mb-8 flex h-24 w-24 items-center justify-center rounded-full bg-rose-500/10 text-rose-400 shadow-[0_0_50px_rgba(244,63,94,0.2)]">
                <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <h1 class="text-5xl text-white [font-family:'Cormorant_Garamond',serif]">Payment Failed</h1>
            <p class="mt-6 max-w-lg text-lg text-slate-300">
                Something went wrong with your transaction. {{ $error ?? 'Please try again later.' }}
            </p>
        @endif

        <div class="mt-12 flex gap-4">
            <a href="{{ route('appointments.user.index') }}" class="rounded-full bg-white/10 px-8 py-3 text-sm font-semibold uppercase tracking-widest text-white transition hover:bg-white/20">
                Back to Appointments
            </a>
            @if (!$success)
                <a href="{{ url('/') }}" class="rounded-full bg-amber-300 px-8 py-3 text-sm font-semibold uppercase tracking-widest text-slate-950 transition hover:bg-amber-200">
                    Return Home
                </a>
            @endif
        </div>
    </div>
</section>
@endsection
