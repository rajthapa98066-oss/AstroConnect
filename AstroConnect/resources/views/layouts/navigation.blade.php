{{-- View: resources\views\layouts\navigation.blade.php --}}
@php
    $astrologerProfile = Auth::check() ? Auth::user()->astrologer : null;
    $isApprovedAstrologer = $astrologerProfile?->verification_status === 'approved';
    $isAdmin = Auth::check() ? Auth::user()->isAdmin() : false;

    $navigationLinks = [
        ['label' => 'Home', 'href' => url('/'), 'active' => request()->is('/')],
        ['label' => 'About', 'href' => url('/about'), 'active' => request()->is('about')],
        ['label' => 'Services', 'href' => url('/services'), 'active' => request()->is('services')],
        ['label' => 'Astrologers', 'href' => url('/astrologers'), 'active' => request()->is('astrologers') || request()->is('astrologers/*')],
        ['label' => 'Horoscope', 'href' => url('/horoscope'), 'active' => request()->is('horoscope')],
        ['label' => 'Blog', 'href' => url('/blog'), 'active' => request()->is('blog')],
        ['label' => 'Contact', 'href' => url('/contact'), 'active' => request()->is('contact')],
    ];
@endphp

<header x-data="{ open: false }" class="sticky top-0 z-50 border-b border-white/10 bg-slate-950/85 backdrop-blur-xl">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
        <a href="{{ url('/') }}" class="flex items-center gap-3">
            <span class="flex h-11 w-11 items-center justify-center rounded-full border border-amber-300/40 bg-amber-300/10 text-lg text-amber-200 shadow-[0_0_30px_rgba(251,191,36,0.18)]">✦</span>
            <div>
                <p class="text-xs uppercase tracking-[0.35em] text-amber-200/70">Cosmic Guidance</p>
                <p class="text-2xl text-white [font-family:'Cormorant_Garamond',serif]">AstroConnect</p>
            </div>
        </a>

        <nav class="hidden items-center gap-6 lg:flex">
            @foreach ($navigationLinks as $link)
                <a href="{{ $link['href'] }}" class="text-sm font-medium transition {{ $link['active'] ? 'text-amber-300' : 'text-slate-300 hover:text-white' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="hidden items-center gap-3 lg:flex">
            @auth
                <a href="{{ $isAdmin ? route('admin.dashboard') : ($isApprovedAstrologer ? route('astrologer.dashboard') : route('dashboard')) }}" class="rounded-full border border-white/10 px-4 py-2 text-sm font-medium text-slate-200 transition hover:border-white/20 hover:text-white">
                    {{ $isAdmin ? 'Admin Panel' : ($isApprovedAstrologer ? 'Astrologer Panel' : 'Dashboard') }}
                </a>

                @if (! $isApprovedAstrologer && ! $isAdmin)
                    <a href="{{ route('astrologer.apply') }}" class="rounded-full bg-amber-300 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-amber-200">
                        Apply as Astrologer
                    </a>
                @endif

                <div x-data="{ menu: false }" class="relative">
                    <button @click="menu = ! menu" type="button" class="flex items-center gap-2 rounded-full border border-white/10 px-3 py-2 text-sm text-slate-200 transition hover:border-white/20 hover:text-white">
                        <span>{{ Auth::user()->name }}</span>
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div x-show="menu" x-transition @click.outside="menu = false" class="absolute right-0 mt-3 w-52 overflow-hidden rounded-2xl border border-white/10 bg-slate-900/95 shadow-2xl shadow-slate-950/60">
                        @if (Auth::user()->role === 'user' && ! $isApprovedAstrologer && ! $isAdmin)
                            <a href="{{ route('appointments.user.index') }}" class="block px-4 py-3 text-sm text-slate-200 transition hover:bg-white/5 hover:text-white">
                                My Appointments
                            </a>
                        @endif
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-3 text-sm text-slate-200 transition hover:bg-white/5 hover:text-white">
                            Profile
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full px-4 py-3 text-left text-sm text-slate-200 transition hover:bg-white/5 hover:text-white">
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="rounded-full border border-white/10 px-4 py-2 text-sm font-medium text-slate-200 transition hover:border-white/20 hover:text-white">
                    Log in
                </a>

                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="rounded-full bg-amber-300 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-amber-200">
                        Register
                    </a>
                @endif
            @endauth
        </div>

        <button @click="open = ! open" type="button" class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/10 text-slate-200 transition hover:border-white/20 hover:text-white lg:hidden">
            <svg x-show="!open" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.75 6.75h14.5M4.75 12h14.5m-14.5 5.25h14.5" />
            </svg>
            <svg x-show="open" x-cloak class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 6.75l10.5 10.5m0-10.5l-10.5 10.5" />
            </svg>
        </button>
    </div>

    <div x-show="open" x-transition.origin.top class="border-t border-white/10 bg-slate-950/95 lg:hidden">
        <div class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-4 sm:px-6">
            @foreach ($navigationLinks as $link)
                <a href="{{ $link['href'] }}" class="rounded-2xl px-4 py-3 text-sm font-medium transition {{ $link['active'] ? 'bg-amber-300 text-slate-950' : 'text-slate-200 hover:bg-white/5 hover:text-white' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach

            <div class="mt-3 grid gap-2 border-t border-white/10 pt-3">
                @auth
                    <a href="{{ $isAdmin ? route('admin.dashboard') : ($isApprovedAstrologer ? route('astrologer.dashboard') : route('dashboard')) }}" class="rounded-2xl px-4 py-3 text-sm font-medium text-slate-200 transition hover:bg-white/5 hover:text-white">
                        {{ $isAdmin ? 'Admin Panel' : ($isApprovedAstrologer ? 'Astrologer Panel' : 'Dashboard') }}
                    </a>
                    @if (! $isApprovedAstrologer && ! $isAdmin)
                        <a href="{{ route('astrologer.apply') }}" class="rounded-2xl px-4 py-3 text-sm font-medium text-slate-200 transition hover:bg-white/5 hover:text-white">
                            Apply as Astrologer
                        </a>
                    @endif
                    @if (Auth::user()->role === 'user' && ! $isApprovedAstrologer && ! $isAdmin)
                        <a href="{{ route('appointments.user.index') }}" class="rounded-2xl px-4 py-3 text-sm font-medium text-slate-200 transition hover:bg-white/5 hover:text-white">
                            My Appointments
                        </a>
                    @endif
                    <a href="{{ route('profile.edit') }}" class="rounded-2xl px-4 py-3 text-sm font-medium text-slate-200 transition hover:bg-white/5 hover:text-white">
                        Profile
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full rounded-2xl px-4 py-3 text-left text-sm font-medium text-slate-200 transition hover:bg-white/5 hover:text-white">
                            Log Out
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="rounded-2xl px-4 py-3 text-sm font-medium text-slate-200 transition hover:bg-white/5 hover:text-white">
                        Log in
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="rounded-2xl bg-amber-300 px-4 py-3 text-sm font-semibold text-slate-950 transition hover:bg-amber-200">
                            Register
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</header>
