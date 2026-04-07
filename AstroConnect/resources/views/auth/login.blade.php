{{-- View: resources\views\auth\login.blade.php --}}
<x-guest-layout>

<section class="flex min-h-[calc(100vh-5rem)] items-center justify-center px-4 py-16 sm:px-6">
    <div class="w-full max-w-md">

        <div class="mb-8 text-center">
            <span class="inline-flex items-center rounded-full border border-amber-300/25 bg-amber-300/10 px-4 py-1.5 text-xs font-medium uppercase tracking-[0.3em] text-amber-200">
                Welcome back
            </span>
            <h1 class="mt-5 text-4xl text-white sm:text-5xl [font-family:'Cormorant_Garamond',serif]">
                Sign in to <span class="text-amber-300">AstroConnect</span>
            </h1>
            <p class="mt-3 text-slate-400">Continue your cosmic journey</p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <div class="rounded-3xl border border-white/10 bg-white/5 p-8 backdrop-blur-sm">
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="mb-1.5 block text-sm font-medium text-slate-300">Email Address</label>
                    <input id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required autofocus autocomplete="username"
                        class="w-full rounded-xl border border-white/10 bg-slate-900/80 px-4 py-3 text-white placeholder-slate-500 transition focus:border-amber-300/50 focus:outline-none focus:ring-2 focus:ring-amber-300/20" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                </div>

                <div>
                    <label for="password" class="mb-1.5 block text-sm font-medium text-slate-300">Password</label>
                    <input id="password"
                        type="password"
                        name="password"
                        required autocomplete="current-password"
                        class="w-full rounded-xl border border-white/10 bg-slate-900/80 px-4 py-3 text-white placeholder-slate-500 transition focus:border-amber-300/50 focus:outline-none focus:ring-2 focus:ring-amber-300/20" />
                    <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
                </div>

                <div class="flex items-center justify-between text-sm">
                    <label class="flex cursor-pointer select-none items-center gap-2 text-slate-400">
                        <input type="checkbox" name="remember"
                            class="rounded border-white/20 bg-slate-900 accent-amber-300">
                        Remember me
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-amber-300 transition hover:text-amber-200">
                            Forgot password?
                        </a>
                    @endif
                </div>

                <button type="submit"
                    class="w-full rounded-xl bg-amber-300 px-6 py-3 text-sm font-semibold uppercase tracking-[0.15em] text-slate-950 transition hover:bg-amber-200">
                    Sign In
                </button>

                <p class="text-center text-sm text-slate-400">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="text-amber-300 transition hover:text-amber-200">Create one</a>
                </p>
            </form>
        </div>

    </div>
</section>

</x-guest-layout>
