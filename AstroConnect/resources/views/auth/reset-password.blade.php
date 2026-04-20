@section('title', 'Set New Password | AstroConnect')

<x-guest-layout>
    <div class="flex min-h-[calc(100vh-200px)] items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
        <div class="w-full max-w-md space-y-8">
            <div class="relative overflow-hidden rounded-3xl border border-slate-800 bg-slate-900/50 p-8 backdrop-blur-xl shadow-2xl transition-all duration-300 hover:border-violet-500/30 group">
                <!-- Cosmic Glow Effect -->
                <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-violet-500/5 blur-[100px] transition-all group-hover:bg-violet-500/10"></div>
                
                <div class="relative">
                    <div class="text-center">
                        <div class="inline-flex h-20 w-20 items-center justify-center rounded-2xl bg-violet-500/10 mb-6 border border-violet-500/20 shadow-[0_0_20px_rgba(139,92,246,0.1)]">
                            <svg class="h-10 w-10 text-violet-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                        </div>
                        <h2 class="font-['Cormorant_Garamond'] text-4xl font-bold tracking-tight text-white">New Alignment</h2>
                        <p class="mt-4 text-sm text-slate-400">
                            Create a strong new password to secure your celestial journey.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('password.store') }}" class="mt-8 space-y-5">
                        @csrf

                        <!-- Password Reset Token -->
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                        <!-- Email Address -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-300 mb-2">Email Address</label>
                            <input id="email" 
                                class="block w-full rounded-xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-white placeholder-slate-500 transition-all focus:border-violet-500 focus:ring-1 focus:ring-violet-500 focus:outline-none sm:text-sm bg-slate-900/50 pointer-events-none opacity-60" 
                                type="email" 
                                name="email" 
                                value="{{ old('email', $request->email) }}" 
                                required 
                                readonly>
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-300 mb-2">New Cosmic Password</label>
                            <div class="relative group">
                                <input id="password" 
                                    class="block w-full rounded-xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-white placeholder-slate-500 transition-all focus:border-violet-500 focus:ring-1 focus:ring-violet-500 focus:outline-none sm:text-sm" 
                                    type="password" 
                                    name="password" 
                                    required 
                                    placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;">
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-slate-300 mb-2">Confirm Alignment</label>
                            <input id="password_confirmation" 
                                class="block w-full rounded-xl border border-slate-700 bg-slate-800/50 py-3 px-4 text-white placeholder-slate-500 transition-all focus:border-violet-500 focus:ring-1 focus:ring-violet-500 focus:outline-none sm:text-sm" 
                                type="password"
                                name="password_confirmation" 
                                required 
                                placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;">
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="relative group flex w-full justify-center overflow-hidden rounded-xl bg-gradient-to-r from-violet-600 to-indigo-600 px-4 py-3 text-sm font-bold text-white shadow-lg transition-all hover:scale-[1.02] active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2 focus:ring-offset-slate-900">
                                <span class="relative z-10">Reset Cosmic Password</span>
                                <div class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/10 to-transparent transition-transform duration-1000 group-hover:translate-x-full"></div>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <p class="text-center text-xs text-slate-500">
                Need help? <a href="/contact" class="text-violet-400 hover:text-violet-300">Contact cosmic support</a>
            </p>
        </div>
    </div>
</x-guest-layout>
