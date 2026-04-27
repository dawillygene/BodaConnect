<x-app-layout title="Welcome to BodaConnect">
    <section class="glass-panel relative overflow-hidden rounded-2xl p-8 text-slate-900 sm:p-12">
        <div class="grid items-center gap-8 lg:grid-cols-[1fr_auto]">
            <div class="max-w-2xl">
                <p class="text-sm font-semibold uppercase tracking-widest text-secondary">Fast. Safe. Reliable.</p>
                <h1 class="mt-3 text-3xl font-black leading-tight sm:text-5xl text-slate-900">Welcome to BodaConnect</h1>
                <p class="mt-4 text-base text-slate-600 sm:text-lg">
                    Request a bodaboda ride in seconds, track ride status, and reach your destination quickly.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center rounded-lg bg-primary px-5 py-2.5 text-sm font-bold text-white">
                            Open Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center rounded-lg bg-primary px-5 py-2.5 text-sm font-bold text-white">Login</a>
                        <a href="{{ route('register') }}" class="inline-flex items-center rounded-lg bg-secondary px-5 py-2.5 text-sm font-bold text-slate-900">Register</a>
                    @endauth
                    <a href="{{ auth()->check() && auth()->user()->role === 'customer' ? route('rides.create') : route('login') }}" class="inline-flex items-center rounded-lg bg-tertiary px-5 py-2.5 text-sm font-bold text-white">
                        Request Ride
                    </a>
                </div>
            </div>
            <div class="hidden lg:block">
                <x-logo-mark class="h-36 w-36" />
            </div>
        </div>
    </section>
</x-app-layout>
