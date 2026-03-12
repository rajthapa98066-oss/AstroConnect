<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Astrologers</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900">
    <div class="max-w-7xl mx-auto p-6">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold">Find Astrologers</h1>
            <div class="flex gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-800 text-white rounded">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 bg-gray-800 text-white rounded">Login</a>
                @endauth
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse ($astrologers as $astrologer)
                <div class="bg-white rounded-lg border p-5 shadow-sm">
                    <h2 class="text-xl font-semibold">{{ $astrologer->user->name }}</h2>
                    <p class="mt-2 text-sm text-gray-600">{{ $astrologer->specialization }}</p>
                    <p class="mt-1 text-sm text-gray-600">Experience: {{ $astrologer->experience_years }} years</p>
                    <p class="mt-1 text-sm text-gray-600">Fee: {{ number_format((float) $astrologer->consultation_fee, 2) }}</p>
                    <a href="{{ route('astrologers.show', $astrologer) }}" class="inline-block mt-4 text-indigo-600 font-medium hover:underline">
                        View Profile
                    </a>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-lg border p-5 text-gray-600">
                    No approved astrologers are available right now.
                </div>
            @endforelse
        </div>

        <div class="mt-6">{{ $astrologers->links() }}</div>
    </div>
</body>
</html>
