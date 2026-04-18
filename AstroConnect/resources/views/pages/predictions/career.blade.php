@extends('layouts.app')

@section('title', 'AI Career Prediction - AstroConnect')

@section('content')
<div class="relative py-16 sm:py-24">
    <!-- Starry background effects -->
    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-1/4 -left-1/4 h-[1000px] w-[1000px] rounded-full bg-indigo-900/10 blur-[120px]"></div>
        <div class="absolute top-1/4 -right-1/4 h-[1000px] w-[1000px] rounded-full bg-amber-600/10 blur-[120px]"></div>
    </div>

    <div class="relative z-10 mx-auto max-w-2xl px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="font-serif text-4xl font-bold tracking-tight text-white sm:text-5xl lg:text-6xl mb-6">
            <span class="block bg-gradient-to-r from-amber-200 via-yellow-400 to-amber-200 bg-clip-text text-transparent transform hover:scale-105 transition-transform duration-500">Discover Your Cosmic Calling</span>
        </h1>
        <p class="mt-4 text-xl text-indigo-200/80 mb-12">
            Let our advanced AI analyze your planetary alignments to reveal the career path the universe has mapped out for you.
        </p>

        <div class="rounded-3xl border border-white/10 bg-slate-900/60 backdrop-blur-2xl shadow-2xl p-8 sm:p-12 transform hover:border-indigo-500/30 transition-colors duration-500 text-left">
            
            <form action="{{ route('predictions.career.process') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 gap-y-6 gap-x-8 sm:grid-cols-2">
                    <!-- Date of Birth -->
                    <div class="sm:col-span-1">
                        <label for="dob" class="block text-sm font-medium text-indigo-300">Date of Birth</label>
                        <div class="mt-2">
                            <input type="date" name="dob" id="dob" required
                                class="block w-full rounded-xl border-0 bg-slate-800/50 py-3 px-4 text-white shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6 transition-all">
                        </div>
                        @error('dob') <span class="text-sm text-red-400 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Time of Birth -->
                    <div class="sm:col-span-1">
                        <label for="time" class="block text-sm font-medium text-indigo-300">Time of Birth</label>
                        <div class="mt-2">
                            <input type="time" name="time" id="time" required
                                class="block w-full rounded-xl border-0 bg-slate-800/50 py-3 px-4 text-white shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6 transition-all">
                        </div>
                        @error('time') <span class="text-sm text-red-400 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Latitude -->
                    <div class="sm:col-span-1">
                        <label for="lat" class="block text-sm font-medium text-indigo-300">Latitude (e.g. 27.7172)</label>
                        <div class="mt-2">
                            <input type="text" name="lat" id="lat" required placeholder="e.g. 27.7172"
                                class="block w-full rounded-xl border-0 bg-slate-800/50 py-3 px-4 text-white shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6 transition-all">
                        </div>
                        @error('lat') <span class="text-sm text-red-400 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Longitude -->
                    <div class="sm:col-span-1">
                        <label for="lon" class="block text-sm font-medium text-indigo-300">Longitude (e.g. 85.3240)</label>
                        <div class="mt-2">
                            <input type="text" name="lon" id="lon" required placeholder="e.g. 85.3240"
                                class="block w-full rounded-xl border-0 bg-slate-800/50 py-3 px-4 text-white shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6 transition-all">
                        </div>
                        @error('lon') <span class="text-sm text-red-400 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Timezone -->
                    <div class="sm:col-span-2">
                        <label for="tzone" class="block text-sm font-medium text-indigo-300">Timezone Offset (e.g. 5.5 for IST)</label>
                        <div class="mt-2">
                            <input type="text" name="tzone" id="tzone" required placeholder="e.g. 5.5"
                                class="block w-full rounded-xl border-0 bg-slate-800/50 py-3 px-4 text-white shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6 transition-all">
                        </div>
                        @error('tzone') <span class="text-sm text-red-400 mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-8 pt-4">
                    <button type="submit"
                        class="w-full rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 px-4 py-4 text-center text-sm font-semibold text-white shadow-lg hover:from-indigo-400 hover:to-purple-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500 tracking-wide transition-all duration-300 hover:scale-[1.02] active:scale-[0.98]">
                        Reveal My Cosmic Career ✦
                    </button>
                </div>
            </form>
        </div>
        
    </div>
</div>
@endsection
