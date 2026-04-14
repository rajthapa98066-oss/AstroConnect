{{-- View: resources\views\layouts\app.blade.php --}}
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
    <div class="relative isolate overflow-hidden">
        <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-[38rem] bg-[radial-gradient(circle_at_top,_rgba(251,191,36,0.18),_transparent_38%),radial-gradient(circle_at_top_right,_rgba(99,102,241,0.12),_transparent_30%)]"></div>
        <div class="pointer-events-none absolute inset-x-0 top-72 -z-10 h-80 bg-[linear-gradient(180deg,rgba(15,23,42,0),rgba(15,23,42,0.8),rgba(2,6,23,1))]"></div>

        @include('layouts.navigation')

        <main>
            {{-- Session Alerts --}}
            @if(session('error') || session('success') || session('info'))
                <div class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
                    @if(session('error'))
                        <div class="rounded-2xl border border-red-500/20 bg-red-500/10 p-4 text-sm text-red-200">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if(session('success'))
                        <div class="rounded-2xl border border-green-500/20 bg-green-500/10 p-4 text-sm text-green-200">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('info'))
                        <div class="rounded-2xl border border-amber-300/20 bg-amber-300/10 p-4 text-sm text-amber-200">
                            {{ session('info') }}
                        </div>
                    @endif
                </div>
            @endif

            @isset($header)
                <header class="border-b border-white/10 bg-slate-950/80 backdrop-blur">
                    <div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            @hasSection('content')
                @yield('content')
            @else
                {{ $slot ?? '' }}
            @endif
        </main>

        @include('layouts.partials.footer')
    </div>

</body>
</html>
