<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $astrologer->user->name }} - Astrologer Profile</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900">
    <div class="max-w-4xl mx-auto p-6">
        <a href="{{ route('astrologers.index') }}" class="text-indigo-600 hover:underline">Back to Astrologers</a>

        <div class="mt-4 bg-white border rounded-lg p-6 shadow-sm">
            <h1 class="text-3xl font-bold">{{ $astrologer->user->name }}</h1>
            <p class="mt-2 text-gray-700">Specialization: {{ $astrologer->specialization }}</p>
            <p class="mt-1 text-gray-700">Experience: {{ $astrologer->experience_years }} years</p>
            <p class="mt-1 text-gray-700">Consultation Fee: {{ number_format((float) $astrologer->consultation_fee, 2) }}</p>
            <p class="mt-1 text-gray-700">Availability: {{ ucfirst($astrologer->availability_status) }}</p>

            <div class="mt-5 border-t pt-4">
                <h2 class="text-lg font-semibold">About</h2>
                <p class="mt-2 text-gray-700">{{ $astrologer->bio }}</p>
            </div>
        </div>
    </div>
</body>
</html>
