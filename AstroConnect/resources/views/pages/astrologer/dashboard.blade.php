<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Astrologer Dashboard
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Welcome, {{ auth()->user()->name }}</h3>
                <p class="mt-2 text-gray-700 dark:text-gray-300">Your profile is approved and live for users.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-5">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Specialization</p>
                    <p class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $astrologer->specialization }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-5">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Experience</p>
                    <p class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $astrologer->experience_years }} years</p>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-5">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Consultation Fee</p>
                    <p class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ number_format((float) $astrologer->consultation_fee, 2) }}</p>
                </div>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('astrologer.profile') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                    Edit Profile
                </a>
                <a href="{{ route('astrologer.appointments') }}" class="inline-flex items-center px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-800">
                    View Appointments
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
