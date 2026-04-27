<x-customer-layout title="Ride Details">
    <div class="mb-6 flex items-center gap-3">
        <x-logo-mark class="h-10 w-10" />
        <h1 class="text-2xl font-bold text-slate-900">Ride #{{ $ride->id }} Details</h1>
    </div>

    <div class="glass-panel rounded-xl p-6">
        <dl class="grid gap-4 sm:grid-cols-2">
            <div><dt class="text-xs uppercase text-slate-500">Pickup</dt><dd class="mt-1 font-medium">{{ $ride->pickup_location }}</dd></div>
            <div><dt class="text-xs uppercase text-slate-500">Destination</dt><dd class="mt-1 font-medium">{{ $ride->destination_location }}</dd></div>
            <div><dt class="text-xs uppercase text-slate-500">Status</dt><dd class="mt-1 font-medium">{{ $ride->status }}</dd></div>
            <div><dt class="text-xs uppercase text-slate-500">Assigned Rider</dt><dd class="mt-1 font-medium">{{ optional($ride->rider)->name ?? 'Not assigned yet' }}</dd></div>
            <div><dt class="text-xs uppercase text-slate-500">Requested At</dt><dd class="mt-1 font-medium">{{ $ride->created_at->format('M d, Y H:i') }}</dd></div>
            <div><dt class="text-xs uppercase text-slate-500">Notes</dt><dd class="mt-1 font-medium">{{ $ride->notes ?: 'None' }}</dd></div>
        </dl>

        <div class="mt-6 flex items-center gap-3">
            <a href="{{ route('rides.index') }}" class="inline-flex rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700">Back to History</a>
            @if ($ride->status === 'Pending')
                <form method="POST" action="{{ route('rides.cancel', $ride) }}">
                    @csrf
                    <input type="hidden" name="_response" value="web">
                    <x-tertiary-button type="submit">Cancel Ride</x-tertiary-button>
                </form>
            @endif
        </div>
    </div>
</x-customer-layout>
