@props(['type' => 'button'])
<button {{ $attributes->merge(['type' => $type, 'class' => 'inline-flex items-center justify-center rounded-lg bg-secondary px-4 py-2 text-sm font-semibold text-slate-900 shadow transition hover:opacity-90']) }}>
    {{ $slot }}
</button>
