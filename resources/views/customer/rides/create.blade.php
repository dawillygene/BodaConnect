<x-customer-layout title="Request Ride">
    <div class="mb-1 flex items-center gap-3">
        <x-logo-mark class="h-10 w-10" />
        <h1 class="text-2xl font-bold text-slate-900">Request Ride</h1>
    </div>
    <p class="mb-6 text-sm text-slate-600">Fill pickup and destination details to create a ride request.</p>

    @if ($errors->any())
        <x-alert-message type="error" class="mb-4">{{ $errors->first() }}</x-alert-message>
    @endif

    <form method="POST" action="{{ route('rides.store') }}" class="glass-panel space-y-4 rounded-xl p-5">
        @csrf
        <input type="hidden" name="_response" value="web">

        <x-input-field label="Pickup Location" name="pickup_location" required />
        <x-input-field label="Destination Location" name="destination_location" required />

        <div>
            <x-form-label for="notes">Optional Notes</x-form-label>
            <textarea id="notes" name="notes" rows="4" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none ring-primary focus:border-primary focus:ring-2">{{ old('notes') }}</textarea>
            @error('notes')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
        </div>

        <x-primary-button>Submit Ride Request</x-primary-button>
    </form>
</x-customer-layout>
