<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AstroConnect</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-[#2b1b47] via-[#140b2d] to-[#0b0614] text-white flex flex-col">

    <!-- Navbar -->
    <header class="flex justify-between items-center px-20 py-6">
        <div class="text-xl font-semibold text-yellow-400">
            ☾ AstroConnect
        </div>

        <nav class="space-x-6 text-sm">
            <a href="/" class="hover:text-yellow-400">Home</a>
            <a href="#" class="hover:text-yellow-400">Astrologers</a>

            @auth
                <a href="{{ route('dashboard') }}" class="text-yellow-400">Dashboard</a>
            @else
                <a href="{{ route('login') }}"
                   class="border border-yellow-400 px-4 py-1 rounded-full">
                    Login
                </a>
                <a href="{{ route('register') }}"
                   class="bg-yellow-400 text-black px-4 py-1 rounded-full font-medium">
                    Register
                </a>
            @endauth
        </nav>
    </header>

    <!-- Page Content -->
    <main class="flex-grow">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="border-t border-gray-800">
        <div class="grid grid-cols-4 gap-10 px-20 py-12 text-sm text-gray-400">

            <div>
                <h3 class="text-yellow-400 text-lg font-semibold mb-3">AstroConnect</h3>
                <p>Discover your cosmic destiny with AI-powered astrology.</p>
            </div>

            <div>
                <h4 class="text-yellow-400 mb-3">Quick Links</h4>
                <a class="block hover:text-yellow-400" href="/">Home</a>
                <a class="block hover:text-yellow-400" href="#">Astrologers</a>
                <a class="block hover:text-yellow-400" href="{{ route('login') }}">Login</a>
                <a class="block hover:text-yellow-400" href="{{ route('register') }}">Register</a>
            </div>

            <div>
                <h4 class="text-yellow-400 mb-3">Services</h4>
                <p>Verefied Astrologers</p>
                <p>Nepali Calendar </p>
                <p></p>
                <p></p>
            </div>

            <div>
                <h4 class="text-yellow-400 mb-3">Contact</h4>
                <p>contact@astroconnect.com</p>
                <p>+977 123-4567</p>
                <p>Cosmic Heights, Universe</p>
            </div>
        </div>

        <div class="text-center text-xs text-gray-500 py-4">
            © 2024 AstroConnect. All rights reserved.
        </div>
    </footer>

</body>
</html>
