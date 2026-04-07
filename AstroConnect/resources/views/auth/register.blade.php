{{-- View: resources\views\auth\register.blade.php --}}
<x-guest-layout>

<section class="flex min-h-[calc(100vh-5rem)] items-center justify-center px-4 py-16 sm:px-6">
    <div class="w-full max-w-md">

        <div class="mb-8 text-center">
            <span class="inline-flex items-center rounded-full border border-amber-300/25 bg-amber-300/10 px-4 py-1.5 text-xs font-medium uppercase tracking-[0.3em] text-amber-200">
                New here?
            </span>
            <h1 class="mt-5 text-4xl text-white sm:text-5xl [font-family:'Cormorant_Garamond',serif]">
                Begin Your <span class="text-amber-300">Journey</span>
            </h1>
            <p class="mt-3 text-slate-400">Create your account and unlock cosmic insights</p>
        </div>

        <div class="rounded-3xl border border-white/10 bg-white/5 p-8 backdrop-blur-sm">
            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="name" class="mb-1.5 block text-sm font-medium text-slate-300">Full Name</label>
                    <input id="name"
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        required autofocus autocomplete="name"
                        class="w-full rounded-xl border border-white/10 bg-slate-900/80 px-4 py-3 text-white placeholder-slate-500 transition focus:border-amber-300/50 focus:outline-none focus:ring-2 focus:ring-amber-300/20" />
                    <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
                </div>

                <div>
                    <label for="email" class="mb-1.5 block text-sm font-medium text-slate-300">Email Address</label>
                    <input id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required autocomplete="username"
                        class="w-full rounded-xl border border-white/10 bg-slate-900/80 px-4 py-3 text-white placeholder-slate-500 transition focus:border-amber-300/50 focus:outline-none focus:ring-2 focus:ring-amber-300/20" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                </div>

                <div>
                    <label for="password" class="mb-1.5 block text-sm font-medium text-slate-300">Password</label>
                    <input id="password"
                        type="password"
                        name="password"
                        required autocomplete="new-password"
                        class="w-full rounded-xl border border-white/10 bg-slate-900/80 px-4 py-3 text-white placeholder-slate-500 transition focus:border-amber-300/50 focus:outline-none focus:ring-2 focus:ring-amber-300/20" />
                    <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
                </div>

                <div>
                    <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-slate-300">Confirm Password</label>
                    <input id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        required autocomplete="new-password"
                        class="w-full rounded-xl border border-white/10 bg-slate-900/80 px-4 py-3 text-white placeholder-slate-500 transition focus:border-amber-300/50 focus:outline-none focus:ring-2 focus:ring-amber-300/20" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
                </div>

                <button type="submit"
                    class="w-full rounded-xl bg-amber-300 px-6 py-3 text-sm font-semibold uppercase tracking-[0.15em] text-slate-950 transition hover:bg-amber-200">
                    Create Account
                </button>

                <p class="text-center text-sm text-slate-400">
                    Already have an account?
                    <a href="{{ route('login') }}" class="text-amber-300 transition hover:text-amber-200">Sign in</a>
                </p>
            </form>
        </div>

    </div>
</section>

</x-guest-layout>
