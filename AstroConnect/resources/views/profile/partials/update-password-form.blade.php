{{-- View: resources\views\profile\partials\update-password-form.blade.php --}}
<section>
    <header>
        <h2 class="text-2xl text-white [font-family:'Cormorant_Garamond',serif]">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-2 text-sm leading-7 text-slate-300">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-8 space-y-6">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="text-xs font-medium uppercase tracking-[0.2em] text-slate-400">{{ __('Current Password') }}</label>
            <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password"
                class="mt-2 block w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-slate-100 outline-none transition placeholder:text-slate-500 focus:border-amber-300/60 focus:ring-2 focus:ring-amber-300/30" />
            @if ($errors->updatePassword->has('current_password'))
                <p class="mt-2 text-sm text-rose-300">{{ $errors->updatePassword->first('current_password') }}</p>
            @endif
        </div>

        <div>
            <label for="update_password_password" class="text-xs font-medium uppercase tracking-[0.2em] text-slate-400">{{ __('New Password') }}</label>
            <input id="update_password_password" name="password" type="password" autocomplete="new-password"
                class="mt-2 block w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-slate-100 outline-none transition placeholder:text-slate-500 focus:border-amber-300/60 focus:ring-2 focus:ring-amber-300/30" />
            @if ($errors->updatePassword->has('password'))
                <p class="mt-2 text-sm text-rose-300">{{ $errors->updatePassword->first('password') }}</p>
            @endif
        </div>

        <div>
            <label for="update_password_password_confirmation" class="text-xs font-medium uppercase tracking-[0.2em] text-slate-400">{{ __('Confirm Password') }}</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                class="mt-2 block w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-slate-100 outline-none transition placeholder:text-slate-500 focus:border-amber-300/60 focus:ring-2 focus:ring-amber-300/30" />
            @if ($errors->updatePassword->has('password_confirmation'))
                <p class="mt-2 text-sm text-rose-300">{{ $errors->updatePassword->first('password_confirmation') }}</p>
            @endif
        </div>

        <div class="flex flex-wrap items-center gap-4">
            <button type="submit" class="inline-flex items-center justify-center rounded-full bg-amber-300 px-6 py-3 text-xs font-semibold uppercase tracking-[0.2em] text-slate-950 transition hover:bg-amber-200">
                {{ __('Save') }}
            </button>

            @if (session('status') === 'password-updated')
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
