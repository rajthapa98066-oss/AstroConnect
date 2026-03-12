<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Astrologer Profile
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                @if (session('status') === 'profile-updated')
                    <div class="mb-4 rounded border border-green-200 bg-green-50 text-green-800 px-4 py-3">
                        Profile updated successfully.
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 rounded border border-red-200 bg-red-50 text-red-700 px-4 py-3">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('astrologer.profile.update') }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Specialization</label>
                        <input type="text" name="specialization" value="{{ old('specialization', $astrologer->specialization) }}"
                            class="mt-1 block w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Experience (Years)</label>
                        <input type="number" min="0" name="experience_years" value="{{ old('experience_years', $astrologer->experience_years) }}"
                            class="mt-1 block w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bio</label>
                        <textarea name="bio" rows="4"
                            class="mt-1 block w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" required>{{ old('bio', $astrologer->bio) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Consultation Fee</label>
                        <input type="number" step="0.01" min="0" name="consultation_fee" value="{{ old('consultation_fee', $astrologer->consultation_fee) }}"
                            class="mt-1 block w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Availability</label>
                        <select name="availability_status"
                            class="mt-1 block w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" required>
                            @php($availability = old('availability_status', $astrologer->availability_status))
                            <option value="available" @selected($availability === 'available')>Available</option>
                            <option value="busy" @selected($availability === 'busy')>Busy</option>
                            <option value="unavailable" @selected($availability === 'unavailable')>Unavailable</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Profile Photo</label>
                        <input type="file" name="profile_photo" accept="image/*"
                            class="mt-1 block w-full text-sm text-gray-900 dark:text-gray-100">
                        @if ($astrologer->profile_photo)
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Current photo available.</p>
                        @endif
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button>Save Changes</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
