@section('title', 'Forgot Password | AstroConnect')

<x-guest-layout>
    <div class="flex min-h-[calc(100vh-200px)] items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
        <div class="w-full max-w-md space-y-8">
            <div class="relative overflow-hidden rounded-3xl border border-slate-800 bg-slate-900/50 p-8 backdrop-blur-xl shadow-2xl transition-all duration-300 hover:border-amber-500/30 group">
                <!-- Cosmic Glow Effect -->
                <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-amber-500/5 blur-[100px] transition-all group-hover:bg-amber-500/10"></div>
                
                <div class="relative">
                    <div class="text-center">
                        <div class="inline-flex h-20 w-20 items-center justify-center rounded-2xl bg-amber-500/10 mb-6 border border-amber-500/20 shadow-[0_0_20px_rgba(245,158,11,0.1)]">
                            <svg class="h-10 w-10 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                            </svg>
                        </div>
                        <h2 class="font-['Cormorant_Garamond'] text-4xl font-bold tracking-tight text-white">Reset Password</h2>
                        <p class="mt-4 text-sm text-slate-400">
                            Forgot your cosmic keys? No problem. Enter your email and we'll send a guidance link to your inbox.
                        </p>
                    </div>

                    <!-- Session Status -->
                    <x-auth-session-status class="mt-6" :status="session('status')" />

                    <form method="POST" action="{{ route('password.email') }}" class="mt-8 space-y-6">
                        @csrf

                        <!-- Email Address -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-300 mb-2">Celestial Email</label>
                            <div class="relative group">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-500 group-focus-within:text-amber-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                    </svg>
                                </span>
                                <input id="email" 
                                    class="block w-full rounded-xl border border-slate-700 bg-slate-800/50 py-3 pl-11 pr-4 text-white placeholder-slate-500 transition-all focus:border-amber-500 focus:ring-1 focus:ring-amber-500 focus:outline-none sm:text-sm" 
                                    type="email" 
                                    name="email" 
                                    value="{{ old('email') }}" 
                                    required 
                                    autofocus 
                                    placeholder="Enter your registered email">
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <button type="submit" class="relative group flex w-full justify-center overflow-hidden rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 px-4 py-3 text-sm font-bold text-slate-900 shadow-lg transition-all hover:scale-[1.02] active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 focus:ring-offset-slate-900">
                                <span class="relative z-10">Request Reset Link</span>
                                <div class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/20 to-transparent transition-transform duration-1000 group-hover:translate-x-full"></div>
                            </button>
                        </div>

                        <div class="text-center">
                            <a href="{{ route('login') }}" class="text-sm font-medium text-amber-500 hover:text-amber-400 transition-colors flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Back to cosmic entry
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Secondary Guidance -->
            <p class="text-center text-xs text-slate-500">
                AstroConnect &bull; Guided by the stars, secured by tech.
            </p>
        </div>
    </div>
</x-guest-layout>
