<x-admin-layout title="Manage Customers">
    @if (session('success'))
        <x-alert-message class="mb-4">{{ session('success') }}</x-alert-message>
    @endif

    <div class="mb-6 flex items-center gap-3">
        <x-logo-mark class="h-10 w-10" />
        <h1 class="text-2xl font-bold text-slate-900">Manage Customers</h1>
    </div>

    <x-data-table :headers="['Name', 'Email', 'Phone', 'Status', 'Joined', 'Action']">
        @forelse($customers as $customer)
            <tr>
                <td class="px-4 py-3">{{ $customer->name }}</td>
                <td class="px-4 py-3">{{ $customer->email }}</td>
                <td class="px-4 py-3">{{ $customer->phone }}</td>
                <td class="px-4 py-3">{{ ucfirst($customer->status) }}</td>
                <td class="px-4 py-3">{{ $customer->created_at->format('M d, Y') }}</td>
                <td class="px-4 py-3">
                    @if($customer->status === 'active')
                        <form method="POST" action="{{ route('admin.users.delete', $customer) }}">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="_response" value="web">
                            <button type="submit" class="text-sm font-semibold text-red-600">Deactivate</button>
                        </form>
                    @else
                        <span class="text-xs text-slate-500">Inactive</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-slate-500">No customers found.</td></tr>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $customers->links() }}</div>
</x-admin-layout>
