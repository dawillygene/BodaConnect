<x-admin-layout title="Create Rider">
    <div class="mb-6 flex items-center gap-3">
        <x-logo-mark class="h-10 w-10" />
        <h1 class="text-2xl font-bold text-slate-900">Add Rider</h1>
    </div>

    @if ($errors->any())
        <x-alert-message type="error" class="mb-4">{{ $errors->first() }}</x-alert-message>
    @endif

    <form method="POST" action="{{ route('admin.riders.store') }}" class="glass-panel space-y-4 rounded-xl p-5">
        @csrf
        <input type="hidden" name="_response" value="web">

        <x-input-field label="Full Name" name="name" required />
        <x-input-field label="Email" name="email" type="email" required />
        <x-input-field label="Phone Number" name="phone" required />
        <x-input-field label="Password" name="password" type="password" required />
        <x-input-field label="Confirm Password" name="password_confirmation" type="password" required />

        <div>
            <x-form-label for="status">Status</x-form-label>
            <select id="status" name="status" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" required>
                <option value="active" @selected(old('status') === 'active')>Active</option>
                <option value="inactive" @selected(old('status') === 'inactive')>Inactive</option>
            </select>
        </div>

        <x-primary-button>Create Rider</x-primary-button>
    </form>
</x-admin-layout>
