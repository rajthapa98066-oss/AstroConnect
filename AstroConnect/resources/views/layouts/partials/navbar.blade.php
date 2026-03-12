@php
    $astrologerProfile = Auth::check() ? Auth::user()->astrologer : null;
@endphp

<nav class="bg-slate-900 border-b border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo / Brand --}}
            <a href="{{ url('/') }}" class="text-indigo-400 font-bold text-xl tracking-wide">
                AstroConnect
            </a>

            {{-- Desktop Links --}}
            <div class="hidden sm:flex sm:items-center sm:gap-6">

                <a href="{{ route('astrologers.index') }}"
                   class="text-sm text-slate-300 hover:text-white transition">
                    Astrologers
                </a>

                @auth
                    <a href="{{ route('dashboard') }}"
                       class="text-sm text-slate-300 hover:text-white transition">
                        Dashboard
                    </a>

                    @if ($astrologerProfile?->verification_status === 'approved')
                        <a href="{{ route('astrologer.dashboard') }}"
                           class="text-sm text-indigo-400 hover:text-indigo-300 transition font-medium">
                            Astrologer Panel
                        </a>
                    @else
                        <a href="{{ route('astrologer.apply') }}"
                           class="text-sm text-slate-300 hover:text-white transition">
                            Apply as Astrologer
                        </a>
                    @endif

                    {{-- User Dropdown --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open"
                            class="inline-flex items-center gap-2 text-sm text-slate-300 hover:text-white transition focus:outline-none">
                            <span>{{ Auth::user()->name }}</span>
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>

                        <div x-show="open" @click.outside="open = false"
                             class="absolute right-0 mt-2 w-44 bg-slate-800 border border-slate-700 rounded-lg shadow-lg z-50"
                             x-transition>
                            <a href="{{ route('profile.edit') }}"
                               class="block px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 hover:text-white rounded-t-lg">
                                Profile
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 hover:text-white rounded-b-lg">
                                    Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}"
                       class="text-sm text-slate-300 hover:text-white transition">
                        Log in
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                           class="text-sm bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-1.5 rounded transition">
                            Register
                        </a>
                    @endif
                @endauth
            </div>

            {{-- Mobile Hamburger --}}
            <div class="sm:hidden" x-data="{ open: false }">
                <button @click="open = !open"
                    class="text-slate-400 hover:text-white focus:outline-none">
                    <svg class="w-6 h-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open}" class="inline-flex"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <div :class="{'block': open, 'hidden': !open}"
                     class="hidden absolute top-16 left-0 right-0 bg-slate-900 border-b border-slate-800 px-4 py-3 space-y-2 z-40">

                    <a href="{{ route('astrologers.index') }}"
                       class="block text-sm text-slate-300 hover:text-white py-1">Astrologers</a>

                    @auth
                        <a href="{{ route('dashboard') }}"
                           class="block text-sm text-slate-300 hover:text-white py-1">Dashboard</a>

                        @if ($astrologerProfile?->verification_status === 'approved')
                            <a href="{{ route('astrologer.dashboard') }}"
                               class="block text-sm text-indigo-400 hover:text-indigo-300 py-1">Astrologer Panel</a>
                        @else
                            <a href="{{ route('astrologer.apply') }}"
                               class="block text-sm text-slate-300 hover:text-white py-1">Apply as Astrologer</a>
                        @endif

                        <a href="{{ route('profile.edit') }}"
                           class="block text-sm text-slate-300 hover:text-white py-1">Profile</a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="block text-sm text-slate-300 hover:text-white py-1 w-full text-left">
                                Log Out
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}"
                           class="block text-sm text-slate-300 hover:text-white py-1">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                               class="block text-sm text-slate-300 hover:text-white py-1">Register</a>
                        @endif
                    @endauth
                </div>
            </div>

        </div>
    </div>
</nav>
