<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 leading-tight">
            {{ __('Edit Inventory Record') }}
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <form method="POST" action="{{ route('inventory.update', $inventory) }}">
                @method('PUT')
                @include('inventory._form')
            </form>
        </div>
    </div>
</x-app-layout>
