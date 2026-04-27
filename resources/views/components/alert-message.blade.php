@props(['type' => 'success'])
@php
    $styles = [
        'success' => 'border-emerald-200 bg-emerald-50/80 text-emerald-800',
        'error' => 'border-red-200 bg-red-50/80 text-red-800',
        'warning' => 'border-amber-200 bg-amber-50/80 text-amber-800',
    ];
@endphp
<div {{ $attributes->merge(['class' => 'rounded-lg border px-4 py-3 text-sm font-medium backdrop-blur-md '.$styles[$type]]) }}>
    {{ $slot }}
</div>
