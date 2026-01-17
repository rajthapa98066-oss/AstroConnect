<x-guest-layout>

    <div class="flex flex-col items-center justify-center mt-16 mb-16">
        <h1 class="text-4xl font-semibold mb-2">
            Begin Your <span class="text-yellow-400">Journey</span>
        </h1>

        <p class="text-gray-400 mb-10">
            Create your account and unlock cosmic insights
        </p>

        <div class="bg-[#141022]/90 backdrop-blur-md w-[440px] rounded-2xl p-8 shadow-xl">
            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <div>
                    <x-input-label for="name" value="Full Name" class="text-gray-300 mb-1" />
                    <x-text-input id="name"
                        class="w-full bg-[#1c1633] border border-gray-700 text-white rounded-lg px-4 py-2 focus:ring-yellow-400"
                        type="text"
                        name="name"
                        :value="old('name')" required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="email" value="Email Address" class="text-gray-300 mb-1" />
                    <x-text-input id="email"
                        class="w-full bg-[#1c1633] border border-gray-700 text-white rounded-lg px-4 py-2 focus:ring-yellow-400"
                        type="email"
                        name="email"
                        :value="old('email')" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password" value="Password" class="text-gray-300 mb-1" />
                    <x-text-input id="password"
                        class="w-full bg-[#1c1633] border border-gray-700 text-white rounded-lg px-4 py-2 focus:ring-yellow-400"
                        type="password"
                        name="password" required />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" value="Confirm Password"
                        class="text-gray-300 mb-1" />
                    <x-text-input id="password_confirmation"
                        class="w-full bg-[#1c1633] border border-gray-700 text-white rounded-lg px-4 py-2 focus:ring-yellow-400"
                        type="password"
                        name="password_confirmation" required />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <x-primary-button
                    class="w-full justify-center bg-yellow-400 text-black py-2 rounded-xl font-semibold">
                    Create Account
                </x-primary-button>

                <p class="text-center text-sm text-gray-400">
                    Already have an account?
                    <a href="{{ route('login') }}" class="text-yellow-400 hover:underline">
                        Sign In
                    </a>
                </p>
            </form>
        </div>
    </div>

</x-guest-layout>
