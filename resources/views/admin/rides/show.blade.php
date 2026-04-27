<x-admin-layout title="Ride Details">
    <div class="mb-6 flex items-center gap-3">
        <x-logo-mark class="h-10 w-10" />
        <h1 class="text-2xl font-bold text-slate-900">Ride #{{ $ride->id }}</h1>
    </div>

    <div class="glass-panel rounded-xl p-6">
        <dl class="grid gap-4 sm:grid-cols-2">
            <div><dt class="text-xs uppercase text-slate-500">Customer</dt><dd class="mt-1 font-medium">{{ optional($ride->customer)->name }}</dd></div>
            <div><dt class="text-xs uppercase text-slate-500">Rider</dt><dd class="mt-1 font-medium">{{ optional($ride->rider)->name ?? 'Not assigned' }}</dd></div>
            <div><dt class="text-xs uppercase text-slate-500">Pickup</dt><dd class="mt-1 font-medium">{{ $ride->pickup_location }}</dd></div>
            <div><dt class="text-xs uppercase text-slate-500">Destination</dt><dd class="mt-1 font-medium">{{ $ride->destination_location }}</dd></div>
            <div><dt class="text-xs uppercase text-slate-500">Status</dt><dd class="mt-1 font-medium">{{ $ride->status }}</dd></div>
            <div><dt class="text-xs uppercase text-slate-500">Created</dt><dd class="mt-1 font-medium">{{ $ride->created_at->format('M d, Y H:i') }}</dd></div>
            <div class="sm:col-span-2"><dt class="text-xs uppercase text-slate-500">Notes</dt><dd class="mt-1 font-medium">{{ $ride->notes ?: 'None' }}</dd></div>
        </dl>
    </div>
</x-admin-layout>
