<x-admin-layout title="Manage Ride Requests">
    @if (session('success'))
        <x-alert-message class="mb-4">{{ session('success') }}</x-alert-message>
    @endif

    <div class="mb-6 flex items-center gap-3">
        <x-logo-mark class="h-10 w-10" />
        <h1 class="text-2xl font-bold text-slate-900">Manage Ride Requests</h1>
    </div>

    <x-data-table :headers="['#', 'Customer', 'Pickup', 'Destination', 'Status', 'Rider', 'Assign', 'View']">
        @forelse($rides as $ride)
            <tr>
                <td class="px-4 py-3">{{ $ride->id }}</td>
                <td class="px-4 py-3">{{ optional($ride->customer)->name }}</td>
                <td class="px-4 py-3">{{ $ride->pickup_location }}</td>
                <td class="px-4 py-3">{{ $ride->destination_location }}</td>
                <td class="px-4 py-3">{{ $ride->status }}</td>
                <td class="px-4 py-3">{{ optional($ride->rider)->name ?? 'Unassigned' }}</td>
                <td class="px-4 py-3">
                    @if($ride->status === 'Pending')
                        <form method="POST" action="{{ route('admin.rides.assign', $ride) }}" class="flex items-center gap-2">
                            @csrf
                            <input type="hidden" name="_response" value="web">
                            <select name="rider_id" required class="rounded border border-slate-300 px-2 py-1 text-xs">
                                <option value="">Rider</option>
                                @foreach($riders as $rider)
                                    <option value="{{ $rider->id }}">{{ $rider->name }}</option>
                                @endforeach
                            </select>
                            <x-primary-button type="submit" class="px-3 py-1 text-xs">Assign</x-primary-button>
                        </form>
                    @else
                        <span class="text-xs text-slate-500">Locked</span>
                    @endif
                </td>
                <td class="px-4 py-3"><a class="text-sm font-semibold text-primary" href="{{ route('admin.rides.show', $ride) }}">Details</a></td>
            </tr>
        @empty
            <tr><td colspan="8" class="px-4 py-8 text-center text-slate-500">No ride requests yet.</td></tr>
        @endforelse
    </x-data-table>
</x-admin-layout>
