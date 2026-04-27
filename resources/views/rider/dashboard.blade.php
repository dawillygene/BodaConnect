<x-rider-layout title="Rider Dashboard">
    @if (session('success'))
        <x-alert-message class="mb-4">{{ session('success') }}</x-alert-message>
    @endif

    <div class="mb-6 flex items-center gap-3">
        <x-logo-mark class="h-10 w-10" />
        <h1 class="text-2xl font-bold text-slate-900">Rider Dashboard</h1>
    </div>

    <section>
        <h2 class="mb-3 text-lg font-semibold text-slate-900">Assigned Trips</h2>
        <x-data-table :headers="['#', 'Pickup', 'Destination', 'Status', 'Action']">
            @forelse($assignedTrips as $trip)
                <tr>
                    <td class="px-4 py-3">{{ $trip->id }}</td>
                    <td class="px-4 py-3">{{ $trip->pickup_location }}</td>
                    <td class="px-4 py-3">{{ $trip->destination_location }}</td>
                    <td class="px-4 py-3">{{ $trip->status }}</td>
                    <td class="px-4 py-3">
                        @if ($trip->status === 'Assigned')
                            <form method="POST" action="{{ route('rider.rides.accept', $trip) }}">
                                @csrf
                                <input type="hidden" name="_response" value="web">
                                <x-secondary-button type="submit" class="px-3 py-1.5 text-xs">Accept</x-secondary-button>
                            </form>
                        @elseif ($trip->status === 'Accepted')
                            <form method="POST" action="{{ route('rider.rides.start', $trip) }}">
                                @csrf
                                <input type="hidden" name="_response" value="web">
                                <x-primary-button type="submit" class="px-3 py-1.5 text-xs">Start Trip</x-primary-button>
                            </form>
                        @elseif ($trip->status === 'In Progress')
                            <form method="POST" action="{{ route('rider.rides.complete', $trip) }}">
                                @csrf
                                <input type="hidden" name="_response" value="web">
                                <x-tertiary-button type="submit" class="px-3 py-1.5 text-xs">Complete</x-tertiary-button>
                            </form>
                        @else
                            <span class="text-xs text-slate-500">No action</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">No active assigned trips.</td></tr>
            @endforelse
        </x-data-table>
    </section>

    <section class="mt-8">
        <h2 class="mb-3 text-lg font-semibold text-slate-900">Completed Trips</h2>
        <x-data-table :headers="['#', 'Pickup', 'Destination', 'Completed At']">
            @forelse($completedTrips as $trip)
                <tr>
                    <td class="px-4 py-3">{{ $trip->id }}</td>
                    <td class="px-4 py-3">{{ $trip->pickup_location }}</td>
                    <td class="px-4 py-3">{{ $trip->destination_location }}</td>
                    <td class="px-4 py-3">{{ $trip->updated_at->format('M d, Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">No completed trips yet.</td></tr>
            @endforelse
        </x-data-table>
    </section>
</x-rider-layout>
