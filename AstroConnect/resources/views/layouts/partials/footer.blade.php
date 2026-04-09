{{-- View: resources\views\layouts\partials\footer.blade.php --}}
<footer class="mt-14 border-t border-white/10 bg-slate-950 text-slate-100">
    <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8 lg:py-16">
        <div class="grid gap-12 lg:grid-cols-[1.1fr_0.9fr_0.9fr_1.1fr]">
            <div>
                <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                    <span class="flex h-12 w-12 items-center justify-center text-2xl text-amber-300">☼</span>
                    <span class="text-3xl font-semibold tracking-wide text-amber-300 [font-family:'Cormorant_Garamond',serif]">Astrology</span>
                </a>

                <p class="mt-5 max-w-sm text-base leading-8 text-slate-300">
                    Explore personalized astrological guidance, trusted consultations, and cosmic insights built for your journey.
                </p>

                <div class="mt-8">
                    <p class="text-lg font-medium text-slate-100">Follow Us</p>
                    <div class="mt-4 flex gap-3">
                        <a href="#" class="flex h-11 w-11 items-center justify-center rounded-full border border-white/10 bg-white/5 text-sm font-semibold text-slate-200 transition hover:border-amber-300/30 hover:bg-amber-300 hover:text-slate-950">f</a>
                        <a href="#" class="flex h-11 w-11 items-center justify-center rounded-full border border-white/10 bg-white/5 text-sm font-semibold text-slate-200 transition hover:border-amber-300/30 hover:bg-amber-300 hover:text-slate-950">t</a>
                        <a href="#" class="flex h-11 w-11 items-center justify-center rounded-full border border-white/10 bg-white/5 text-sm font-semibold text-slate-200 transition hover:border-amber-300/30 hover:bg-amber-300 hover:text-slate-950">G+</a>
                        <a href="#" class="flex h-11 w-11 items-center justify-center rounded-full border border-white/10 bg-white/5 text-sm font-semibold text-slate-200 transition hover:border-amber-300/30 hover:bg-amber-300 hover:text-slate-950">▶</a>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-2xl font-semibold text-slate-100 [font-family:'Cormorant_Garamond',serif]">Our Services</h3>
                <div class="mt-4 h-0.5 w-24 bg-amber-300/80"></div>
                <ul class="mt-6 space-y-4 text-[15px] text-slate-300">
                    <li><a href="{{ url('/horoscope') }}" class="transition hover:text-amber-300">Horoscopes</a></li>
                    <li><a href="{{ url('/services') }}" class="transition hover:text-amber-300">Gemstones</a></li>
                    <li><a href="{{ url('/services') }}" class="transition hover:text-amber-300">Numerology</a></li>
                    <li><a href="{{ url('/services') }}" class="transition hover:text-amber-300">Tarot Cards</a></li>
                    <li><a href="{{ url('/services') }}" class="transition hover:text-amber-300">Birth Journal</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-2xl font-semibold text-slate-100 [font-family:'Cormorant_Garamond',serif]">Quick Links</h3>
                <div class="mt-4 h-0.5 w-24 bg-amber-300/80"></div>
                <ul class="mt-6 space-y-4 text-[15px] text-slate-300">
                    <li><a href="{{ url('/about') }}" class="transition hover:text-amber-300">About Us</a></li>
                    <li><a href="{{ url('/blog') }}" class="transition hover:text-amber-300">Blog</a></li>
                    <li><a href="{{ url('/astrologers') }}" class="transition hover:text-amber-300">Astrologers</a></li>
                    <li><a href="{{ url('/dashboard') }}" class="transition hover:text-amber-300">Appointment</a></li>
                    <li><a href="{{ url('/contact') }}" class="transition hover:text-amber-300">Contact Us</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-2xl font-semibold text-slate-100 [font-family:'Cormorant_Garamond',serif]">Contact Us</h3>
                <div class="mt-4 h-0.5 w-24 bg-amber-300/80"></div>

                <ul class="mt-6 space-y-5 text-[15px] text-slate-300">
                    <li class="flex gap-3">
                        <span class="mt-1 text-xl text-amber-300">⌂</span>
                        <span>Pokhara Nepal</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="mt-1 text-xl text-amber-300">✉</span>
                        <span>astrology@example.com<br>astro@example.com</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="mt-1 text-xl text-amber-300">☎</span>
                        <span>+ (977) 1800-124-105<br>+ (977) 1800-326-324</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="border-t border-white/10 bg-slate-950/95">
        <div class="mx-auto max-w-7xl px-4 py-4 text-center text-sm text-slate-400 sm:px-6 lg:px-8">
            Copyright &copy; {{ date('          Y') }} AstroConnect. All Right Reserved.
        </div>
    </div>
</footer>
