<footer class="bg-slate-900 border-t border-slate-800 mt-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <span class="text-indigo-400 font-bold text-lg">AstroConnect</span>
            <p class="text-sm text-slate-500">&copy; {{ date('Y') }} AstroConnect. All rights reserved.</p>
            <div class="flex gap-4 text-sm text-slate-400">
                <a href="{{ route('astrologers.index') }}" class="hover:text-white transition">Find Astrologers</a>
                <a href="{{ route('login') }}" class="hover:text-white transition">Login</a>
            </div>
        </div>
    </div>
</footer>
