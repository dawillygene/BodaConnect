<x-app-layout :title="$title ?? 'Admin Dashboard'">
    <div class="grid gap-6 lg:grid-cols-[260px,1fr]">
        <x-sidebar role="admin" />
        <main class="space-y-6">{{ $slot }}</main>
    </div>
</x-app-layout>
