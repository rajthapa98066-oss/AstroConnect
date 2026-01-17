<x-guest-layout>

    <div class="flex flex-col items-center justify-center mt-16">
        <h1 class="text-4xl font-semibold mb-2">
            Welcome <span class="text-yellow-400">Back</span>
        </h1>

        <p class="text-gray-400 mb-10">
            Sign in to continue your cosmic journey
        </p>

        <div class="bg-[#141022]/90 backdrop-blur-md w-[420px] rounded-2xl p-8 shadow-xl">
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email -->
                <div>
                    <x-input-label for="email" value="Email Address"
                        class="text-gray-300 mb-1" />

                    <x-text-input id="email"
                        class="w-full bg-[#1c1633] border border-gray-700 text-white rounded-lg px-4 py-2 focus:ring-yellow-400"
                        type="email"
                        name="email"
                        :value="old('email')"
                        required autofocus />

                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div>
                    <x-input-label for="password" value="Password"
                        class="text-gray-300 mb-1" />

                    <x-text-input id="password"
                        class="w-full bg-[#1c1633] border border-gray-700 text-white rounded-lg px-4 py-2 focus:ring-yellow-400"
                        type="password"
                        name="password"
                        required />

                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember -->
                <div class="flex justify-between items-center text-sm">
                    <label class="flex items-center gap-2 text-gray-400">
                        <input type="checkbox" name="remember" class="accent-yellow-400">
                        Remember me
                    </label>

                    <a href="{{ route('password.request') }}"
                       class="text-yellow-400 hover:underline">
                        Forgot password?
                    </a>
                </div>

                <x-primary-button
                    class="w-full justify-center bg-yellow-400 text-black py-2 rounded-xl font-semibold">
                    Log in
                </x-primary-button>

                <p class="text-center text-sm text-gray-400">
                    Don’t have an account?
                    <a href="{{ route('register') }}" class="text-yellow-400 hover:underline">
                        Create Account
                    </a>
                </p>
            </form>
        </div>
    </div>

</x-guest-layout>
