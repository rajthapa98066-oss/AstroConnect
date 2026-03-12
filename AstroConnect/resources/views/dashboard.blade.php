<x-app-layout>
    @php
        $astrologerProfile = auth()->user()?->astrologer;
    @endphp

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("You're logged in!") }}
                </div>
            </div>

            <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold">Astrologer Access</h3>

                    @if (! $astrologerProfile)
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Apply to become an astrologer and get listed on the platform.</p>
                        <a href="{{ route('astrologer.apply') }}" class="inline-flex mt-4 items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                            Apply as Astrologer
                        </a>
                    @elseif ($astrologerProfile->verification_status === 'approved')
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Your astrologer profile is approved. Access your astrologer dashboard.</p>
                        <a href="{{ route('astrologer.dashboard') }}" class="inline-flex mt-4 items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-500">
                            Go to Astrologer Dashboard
                        </a>
                    @elseif ($astrologerProfile->verification_status === 'pending')
                        <p class="mt-2 text-sm text-yellow-600 dark:text-yellow-400">Your astrologer application is pending admin review.</p>
                        <a href="{{ route('astrologer.apply') }}" class="inline-flex mt-4 items-center px-4 py-2 bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600">
                            View Application
                        </a>
                    @else
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">Your previous astrologer application was rejected. You can update details and reapply.</p>
                        <a href="{{ route('astrologer.apply') }}" class="inline-flex mt-4 items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                            Reapply as Astrologer
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
