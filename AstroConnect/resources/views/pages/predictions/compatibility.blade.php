@extends('layouts.app')

@section('title', 'AI Compatibility Check - AstroConnect')

@section('content')
<div class="relative py-16 sm:py-24">
    <!-- Starry background effects -->
    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-1/4 -right-1/4 h-[1000px] w-[1000px] rounded-full bg-pink-900/10 blur-[120px]"></div>
        <div class="absolute top-1/4 -left-1/4 h-[1000px] w-[1000px] rounded-full bg-purple-600/10 blur-[120px]"></div>
    </div>

    <div class="relative z-10 mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="font-serif text-4xl font-bold tracking-tight text-white sm:text-5xl lg:text-6xl mb-6">
            <span class="block bg-gradient-to-r from-pink-300 via-purple-300 to-indigo-300 bg-clip-text text-transparent transform hover:scale-105 transition-transform duration-500">Celestial Matchmaking</span>
        </h1>
        <p class="mt-4 text-xl text-purple-200/80 mb-12 max-w-2xl mx-auto">
            Discover the profound cosmic connection between two souls. Our AI evaluates traditional Ashtakoot scores deeply to reveal your compatibility.
        </p>

        <form action="{{ route('predictions.compatibility.process') }}" method="POST" class="space-y-8">
            @csrf
            
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                
                <!-- Person 1 details -->
                <div class="rounded-3xl border border-pink-500/10 bg-slate-900/60 backdrop-blur-2xl shadow-xl p-8 transform hover:border-pink-500/30 transition-colors duration-500 text-left relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-pink-500/50 to-transparent"></div>
                    <h2 class="text-2xl font-serif text-pink-200 mb-6">Person 1 Details</h2>
                    
                    <div class="space-y-5">
                        <div>
                            <label for="p1_dob" class="block text-sm font-medium text-purple-300">Date of Birth</label>
                            <input type="date" name="p1_dob" id="p1_dob" required
                                class="mt-2 block w-full rounded-xl border-0 bg-slate-800/50 py-3 px-4 text-white shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-pink-500 sm:text-sm sm:leading-6 transition-all">
                        </div>
                        <div>
                            <label for="p1_time" class="block text-sm font-medium text-purple-300">Time of Birth</label>
                            <input type="time" name="p1_time" id="p1_time" required
                                class="mt-2 block w-full rounded-xl border-0 bg-slate-800/50 py-3 px-4 text-white shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-pink-500 sm:text-sm sm:leading-6 transition-all">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="p1_lat" class="block text-sm font-medium text-purple-300">Lat (e.g. 27.7)</label>
                                <input type="text" name="p1_lat" id="p1_lat" required placeholder="27.7172"
                                    class="mt-2 block w-full rounded-xl border-0 bg-slate-800/50 py-3 px-4 text-white shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-pink-500 sm:text-sm sm:leading-6 transition-all">
                            </div>
                            <div>
                                <label for="p1_lon" class="block text-sm font-medium text-purple-300">Lon (e.g. 85.3)</label>
                                <input type="text" name="p1_lon" id="p1_lon" required placeholder="85.3240"
                                    class="mt-2 block w-full rounded-xl border-0 bg-slate-800/50 py-3 px-4 text-white shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-pink-500 sm:text-sm sm:leading-6 transition-all">
                            </div>
                        </div>
                        <div>
                            <label for="p1_tzone" class="block text-sm font-medium text-purple-300">Timezone Offset (e.g. 5.5)</label>
                            <input type="text" name="p1_tzone" id="p1_tzone" required placeholder="5.5"
                                class="mt-2 block w-full rounded-xl border-0 bg-slate-800/50 py-3 px-4 text-white shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-pink-500 sm:text-sm sm:leading-6 transition-all">
                        </div>
                    </div>
                </div>

                <!-- Person 2 details -->
                <div class="rounded-3xl border border-indigo-500/10 bg-slate-900/60 backdrop-blur-2xl shadow-xl p-8 transform hover:border-indigo-500/30 transition-colors duration-500 text-left relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-indigo-500/50 to-transparent"></div>
                    <h2 class="text-2xl font-serif text-indigo-200 mb-6">Person 2 Details</h2>
                    
                    <div class="space-y-5">
                        <div>
                            <label for="p2_dob" class="block text-sm font-medium text-indigo-300">Date of Birth</label>
                            <input type="date" name="p2_dob" id="p2_dob" required
                                class="mt-2 block w-full rounded-xl border-0 bg-slate-800/50 py-3 px-4 text-white shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6 transition-all">
                        </div>
                        <div>
                            <label for="p2_time" class="block text-sm font-medium text-indigo-300">Time of Birth</label>
                            <input type="time" name="p2_time" id="p2_time" required
                                class="mt-2 block w-full rounded-xl border-0 bg-slate-800/50 py-3 px-4 text-white shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6 transition-all">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="p2_lat" class="block text-sm font-medium text-indigo-300">Lat (e.g. 27.7)</label>
                                <input type="text" name="p2_lat" id="p2_lat" required placeholder="27.7172"
                                    class="mt-2 block w-full rounded-xl border-0 bg-slate-800/50 py-3 px-4 text-white shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6 transition-all">
                            </div>
                            <div>
                                <label for="p2_lon" class="block text-sm font-medium text-indigo-300">Lon (e.g. 85.3)</label>
                                <input type="text" name="p2_lon" id="p2_lon" required placeholder="85.3240"
                                    class="mt-2 block w-full rounded-xl border-0 bg-slate-800/50 py-3 px-4 text-white shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6 transition-all">
                            </div>
                        </div>
                        <div>
                            <label for="p2_tzone" class="block text-sm font-medium text-indigo-300">Timezone Offset (e.g. 5.5)</label>
                            <input type="text" name="p2_tzone" id="p2_tzone" required placeholder="5.5"
                                class="mt-2 block w-full rounded-xl border-0 bg-slate-800/50 py-3 px-4 text-white shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6 transition-all">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-12 max-w-md mx-auto">
                <button type="submit"
                    class="w-full rounded-xl bg-gradient-to-r from-pink-600 to-indigo-600 px-4 py-4 text-center text-sm font-semibold text-white shadow-[0_0_20px_rgba(219,39,119,0.3)] hover:shadow-[0_0_25px_rgba(219,39,119,0.5)] hover:from-pink-500 hover:to-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pink-500 tracking-wide transition-all duration-300 hover:scale-[1.02] active:scale-[0.98]">
                    Calculate Compatibility ✦
                </button>
            </div>
            
            @if($errors->any())
                <div class="mt-4 p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-200 text-sm">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </form>
    </div>
</div>
@endsection
