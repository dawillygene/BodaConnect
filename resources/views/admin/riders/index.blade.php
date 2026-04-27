<x-admin-layout title="Manage Riders">
    @if (session('success'))
        <x-alert-message class="mb-4">{{ session('success') }}</x-alert-message>
    @endif

    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <x-logo-mark class="h-10 w-10" />
            <h1 class="text-2xl font-bold text-slate-900">Manage Riders</h1>
        </div>
        <a href="{{ route('admin.riders.create') }}" class="inline-flex rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white">Add Rider</a>
    </div>

    <x-data-table :headers="['Name', 'Email', 'Phone', 'Status', 'Joined', 'Actions']">
        @forelse($riders as $rider)
            <tr>
                <td class="px-4 py-3">{{ $rider->name }}</td>
                <td class="px-4 py-3">{{ $rider->email }}</td>
                <td class="px-4 py-3">{{ $rider->phone }}</td>
                <td class="px-4 py-3">{{ ucfirst($rider->status) }}</td>
                <td class="px-4 py-3">{{ $rider->created_at->format('M d, Y') }}</td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.riders.edit', $rider) }}" class="text-sm font-semibold text-primary">Edit</a>
                        @if($rider->status === 'active')
                            <form method="POST" action="{{ route('admin.users.delete', $rider) }}">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="_response" value="web">
                                <button type="submit" class="text-sm font-semibold text-red-600">Deactivate</button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-slate-500">No riders found.</td></tr>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $riders->links() }}</div>
</x-admin-layout>
