<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto">
        <x-index-overview :description="$pageDescription" :metrics="$metrics" :chart="$chart" />
    </div>
</x-app-layout>
