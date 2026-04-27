@props(['headers' => []])
<div class="glass-panel overflow-hidden rounded-xl">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-white/70 text-slate-700">
                <tr>
                    @foreach($headers as $header)
                        <th class="px-4 py-3 text-left font-semibold">{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white/40 text-slate-800">
                {{ $slot }}
            </tbody>
        </table>
    </div>
</div>
