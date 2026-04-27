<x-customer-layout title="Ride History">
    @if (session('success'))
        <x-alert-message class="mb-4">{{ session('success') }}</x-alert-message>
    @endif

    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <x-logo-mark class="h-10 w-10" />
            <h1 class="text-2xl font-bold text-slate-900">Ride History</h1>
        </div>
        <a href="{{ route('rides.create') }}" class="inline-flex rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white">Request Ride</a>
    </div>

    <x-data-table :headers="['#', 'Pickup', 'Destination', 'Rider', 'Status', 'Requested At', 'Actions']">
        @forelse($rides as $ride)
            <tr>
                <td class="px-4 py-3">{{ $ride->id }}</td>
                <td class="px-4 py-3">{{ $ride->pickup_location }}</td>
                <td class="px-4 py-3">{{ $ride->destination_location }}</td>
                <td class="px-4 py-3">{{ optional($ride->rider)->name ?? 'Not assigned' }}</td>
                <td class="px-4 py-3">{{ $ride->status }}</td>
                <td class="px-4 py-3">{{ $ride->created_at->format('M d, Y H:i') }}</td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('rides.show', $ride) }}" class="text-sm font-semibold text-primary">View</a>
                        @if ($ride->status === 'Pending')
                            <form method="POST" action="{{ route('rides.cancel', $ride) }}">
                                @csrf
                                <input type="hidden" name="_response" value="web">
                                <button type="submit" class="text-sm font-semibold text-red-600">Cancel</button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="px-4 py-8 text-center text-slate-500">No ride requests found.</td></tr>
        @endforelse
    </x-data-table>
</x-customer-layout>
