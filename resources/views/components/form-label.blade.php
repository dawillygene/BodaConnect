@props(['for' => null])
<label @if($for) for="{{ $for }}" @endif class="mb-1 block text-sm font-medium text-slate-700">{{ $slot }}</label>
