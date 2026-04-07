{{-- View: resources\views\profile\partials\delete-user-form.blade.php --}}
<section class="space-y-6">
    <header>
        <h2 class="text-2xl text-white [font-family:'Cormorant_Garamond',serif]">
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-2 text-sm leading-7 text-slate-200">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <div class="rounded-2xl border border-red-400/25 bg-red-400/5 p-5 text-sm leading-7 text-rose-100">
        {{ __('This action is permanent. Enter your password to confirm account deletion.') }}
    </div>

    <form method="post" action="{{ route('profile.destroy') }}" class="space-y-4">
        @csrf
        @method('delete')

        <div>
            <label for="delete_password" class="text-xs font-medium uppercase tracking-[0.2em] text-rose-200/80">{{ __('Password') }}</label>
            <input id="delete_password" name="password" type="password" placeholder="{{ __('Enter current password') }}"
                class="mt-2 block w-full rounded-2xl border border-rose-300/25 bg-white/5 px-4 py-3 text-slate-100 outline-none transition placeholder:text-slate-400 focus:border-rose-300/70 focus:ring-2 focus:ring-rose-300/30" />
            @if ($errors->userDeletion->has('password'))
                <p class="mt-2 text-sm text-rose-300">{{ $errors->userDeletion->first('password') }}</p>
            @endif
        </div>

        <button type="submit" class="inline-flex items-center justify-center rounded-full border border-rose-300/30 bg-rose-400/20 px-6 py-3 text-xs font-semibold uppercase tracking-[0.2em] text-rose-100 transition hover:bg-rose-400/30">
            {{ __('Delete Account') }}
        </button>
    </form>
</section>
