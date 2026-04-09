{{-- View: resources\views\layouts\astrologer\master.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AstroConnect')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-950 text-slate-100 [font-family:'Outfit',sans-serif]">
    <div x-data="{ sidebarOpen: false }" class="relative isolate min-h-screen overflow-x-hidden">
        <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-[38rem] bg-[radial-gradient(circle_at_top,_rgba(251,191,36,0.18),_transparent_38%),radial-gradient(circle_at_top_right,_rgba(99,102,241,0.12),_transparent_30%)]"></div>
        <div class="pointer-events-none absolute inset-x-0 top-72 -z-10 h-80 bg-[linear-gradient(180deg,rgba(15,23,42,0),rgba(15,23,42,0.8),rgba(2,6,23,1))]"></div>

        <div class="lg:flex lg:min-h-screen lg:items-stretch">
            <aside class="hidden lg:block lg:w-72 lg:shrink-0 lg:self-stretch">
                @include('layouts.astrologer.body.sidebar')
            </aside>

            <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-slate-950/80 lg:hidden" @click="sidebarOpen = false"></div>
            <aside x-show="sidebarOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="fixed inset-y-0 left-0 z-50 w-72 lg:hidden" @click.outside="sidebarOpen = false">
                @include('layouts.astrologer.body.sidebar')
            </aside>

            <div class="flex min-h-screen flex-1 flex-col">
                <div class="sticky top-0 z-30 border-b border-white/10 bg-slate-950/85 backdrop-blur-xl">
                    <div class="flex items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                        <button type="button" @click="sidebarOpen = true" class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/10 text-slate-200 transition hover:border-white/20 hover:text-white lg:hidden" aria-label="Open sidebar">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.75 6.75h14.5M4.75 12h14.5m-14.5 5.25h14.5" />
                            </svg>
                        </button>

                        <div>
                            <p class="text-xs uppercase tracking-[0.3em] text-amber-200/70">Astrologer Workspace</p>
                            <p class="text-lg text-white [font-family:'Cormorant_Garamond',serif]">Manage Your AstroConnect Presence</p>
                        </div>

                        <a href="{{ route('astrologer.dashboard') }}" class="hidden rounded-full border border-white/15 px-4 py-2 text-sm font-medium text-slate-200 transition hover:border-white/25 hover:text-white sm:inline-flex">
                            Panel Home
                        </a>
                    </div>
                </div>

                <main class="flex-1">
                    @yield('content')
                </main>
            </div>
        </div>
    </div>
</body>

</html>
