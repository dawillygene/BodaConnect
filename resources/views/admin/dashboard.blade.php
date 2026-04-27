<x-admin-layout title="Admin Dashboard">
    <div class="mb-6 flex items-center gap-3">
        <x-logo-mark class="h-10 w-10" />
        <h1 class="text-2xl font-bold text-slate-900">Admin Dashboard</h1>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <x-dashboard-card title="Total Customers" :value="$stats['customers']" />
        <x-dashboard-card title="Total Riders" :value="$stats['riders']" />
        <x-dashboard-card title="Total Ride Requests" :value="$stats['total_rides']" />
        <x-dashboard-card title="Pending Rides" :value="$stats['pending']" />
        <x-dashboard-card title="Completed Rides" :value="$stats['completed']" />
        <x-dashboard-card title="Cancelled Rides" :value="$stats['cancelled']" />
    </div>
</x-admin-layout>
