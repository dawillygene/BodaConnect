@props(['role'])
@php
    $links = match ($role) {
        'admin' => [
            ['label' => 'Dashboard', 'route' => 'admin.dashboard'],
            ['label' => 'Manage Rides', 'route' => 'admin.rides.index'],
            ['label' => 'Manage Customers', 'route' => 'admin.customers.index'],
            ['label' => 'Manage Riders', 'route' => 'admin.riders.index'],
        ],
        'rider' => [
            ['label' => 'Dashboard', 'route' => 'rider.dashboard'],
        ],
        default => [
            ['label' => 'Dashboard', 'route' => 'customer.dashboard'],
            ['label' => 'Request Ride', 'route' => 'rides.create'],
            ['label' => 'Ride History', 'route' => 'rides.index'],
        ],
    };
@endphp

<aside class="glass-panel h-fit rounded-xl p-4">
    <div class="mb-4 flex items-center gap-2 rounded-lg bg-white/60 p-2">
        <x-logo-mark class="h-9 w-9" />
        <div>
            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ ucfirst($role) }} Menu</p>
        </div>
    </div>
    <nav class="space-y-2">
        @foreach($links as $link)
            <a
                href="{{ route($link['route']) }}"
                class="block rounded-lg px-3 py-2 text-sm font-medium transition {{ request()->routeIs($link['route']) ? 'bg-primary text-white' : 'text-slate-700 hover:bg-slate-100' }}"
            >
                {{ $link['label'] }}
            </a>
        @endforeach
    </nav>
</aside>
