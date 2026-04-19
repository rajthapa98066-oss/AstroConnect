{{-- View: resources\views\layouts\astrologer\body\sidebar.blade.php --}}
@php
    $canAccessAstrologerPanel = auth()->check() && auth()->user()->canAccessAstrologerPanel();

    $items = [
        [
            'label' => 'Dashboard',
            'route' => $canAccessAstrologerPanel ? route('astrologer.dashboard') : route('astrologer.apply'),
            'active' => request()->routeIs('astrologer.dashboard'),
        ],
        [
            'label' => 'Profile',
            'route' => route('astrologer.profile'),
            'active' => request()->routeIs('astrologer.profile*'),
        ],
        [
            'label' => 'Appointments',
            'route' => route('astrologer.appointments'),
            'active' => request()->routeIs('astrologer.appointments*'),
        ],
        [
            'label' => 'Blogs',
            'route' => route('astrologer.blogs.index'),
            'active' => request()->routeIs('astrologer.blogs.index') || request()->routeIs('astrologer.blogs.edit'),
        ],
        [
            'label' => 'Create Blog',
            'route' => route('astrologer.blogs.create'),
            'active' => request()->routeIs('astrologer.blogs.create'),
        ],
    ];
@endphp

<div class="flex h-full min-h-screen flex-col overflow-y-auto border-r border-white/10 bg-slate-950/95 p-6 backdrop-blur-xl">
    <a href="{{ $canAccessAstrologerPanel ? route('astrologer.dashboard') : route('astrologer.apply') }}" class="flex items-center gap-3 border-b border-white/10 pb-5">
        <span class="flex h-10 w-10 items-center justify-center rounded-full border border-amber-300/40 bg-amber-300/10 text-amber-200">✦</span>
        <div>
            <p class="text-xs uppercase tracking-[0.3em] text-amber-200/70">Astrologer Panel</p>
            <p class="text-xl text-white [font-family:'Cormorant_Garamond',serif]">AstroConnect</p>
        </div>
    </a>

    <div class="mt-6 rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Signed in as</p>
        <p class="mt-1 text-sm font-medium text-white">{{ auth()->user()->name }}</p>
    </div>

    <nav class="mt-6 space-y-2" aria-label="Astrologer features">
        @foreach ($items as $item)
            <a href="{{ $item['route'] }}"
                class="block rounded-xl px-4 py-3 text-sm font-medium transition {{ $item['active'] ? 'bg-amber-300 text-slate-950' : 'text-slate-200 hover:bg-white/10 hover:text-white' }}">
                {{ $item['label'] }}
            </a>
        @endforeach
    </nav>

    <div class="mt-auto pt-6">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full rounded-xl border border-white/15 px-4 py-3 text-left text-sm font-medium text-slate-200 transition hover:border-white/30 hover:bg-white/10 hover:text-white">
                Log Out
            </button>
        </form>
    </div>
</div>
