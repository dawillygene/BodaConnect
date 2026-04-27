<x-app-layout :title="$title ?? 'Customer Dashboard'">
    <div class="grid gap-6 lg:grid-cols-[260px,1fr]">
        <x-sidebar role="customer" />
        <main class="space-y-6">{{ $slot }}</main>
    </div>
</x-app-layout>
