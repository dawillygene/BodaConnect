@props(['label', 'name', 'type' => 'text', 'value' => '', 'required' => false])
<div>
    <x-form-label :for="$name">{{ $label }}</x-form-label>
    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        value="{{ old($name, $value) }}"
        @if($required) required @endif
        {{ $attributes->merge(['class' => 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 outline-none ring-primary transition focus:border-primary focus:ring-2']) }}
    >
    @error($name)
        <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
    @enderror
</div>
