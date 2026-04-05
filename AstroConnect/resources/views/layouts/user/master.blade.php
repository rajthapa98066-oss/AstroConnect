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

        @include('layouts.user.body.header')

        <main>
            @yield('content')
        </main>

        @include('layouts.user.body.footer')
    </div>
</body>

</html>
