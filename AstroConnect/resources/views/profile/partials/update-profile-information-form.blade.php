<section>
    <header>
        <h2 class="text-2xl text-white [font-family:'Cormorant_Garamond',serif]">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-2 text-sm leading-7 text-slate-300">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-8 space-y-6">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="text-xs font-medium uppercase tracking-[0.2em] text-slate-400">{{ __('Name') }}</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name"
                class="mt-2 block w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-slate-100 outline-none transition placeholder:text-slate-500 focus:border-amber-300/60 focus:ring-2 focus:ring-amber-300/30" />
            @error('name')
                <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="text-xs font-medium uppercase tracking-[0.2em] text-slate-400">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username"
                class="mt-2 block w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-slate-100 outline-none transition placeholder:text-slate-500 focus:border-amber-300/60 focus:ring-2 focus:ring-amber-300/30" />
            @error('email')
                <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="mt-3 text-sm text-slate-300">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="font-semibold text-amber-200 underline decoration-amber-200/70 underline-offset-4 transition hover:text-amber-100">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm font-medium text-emerald-300">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex flex-wrap items-center gap-4">
            <button type="submit" class="inline-flex items-center justify-center rounded-full bg-amber-300 px-6 py-3 text-xs font-semibold uppercase tracking-[0.2em] text-slate-950 transition hover:bg-amber-200">
                {{ __('Save') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm font-medium text-emerald-300"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
