<x-guest-layout title="Register - BodaConnect">
    <h1 class="text-2xl font-bold text-slate-900">Customer Registration</h1>
    <p class="mt-1 text-sm text-slate-600">Create your account to request rides.</p>

    @if ($errors->any())
        <x-alert-message type="error" class="mt-4">{{ $errors->first() }}</x-alert-message>
    @endif

    <form method="POST" action="{{ route('register.store') }}" class="mt-6 space-y-4">
        @csrf
        <x-input-field label="Full Name" name="name" required />
        <x-input-field label="Email" name="email" type="email" required />
        <x-input-field label="Phone Number" name="phone" required />
        <x-input-field label="Password" name="password" type="password" required />
        <x-input-field label="Confirm Password" name="password_confirmation" type="password" required />

        <x-primary-button class="w-full">Create Account</x-primary-button>
    </form>

    <p class="mt-5 text-sm text-slate-600">
        Already registered? <a class="font-semibold text-primary" href="{{ route('login') }}">Login</a>
    </p>
</x-guest-layout>
