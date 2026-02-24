@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center rounded-lg px-3 py-2 text-sm font-semibold text-emerald-900 bg-emerald-100 border border-emerald-200 transition'
            : 'flex items-center rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-transparent transition';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
