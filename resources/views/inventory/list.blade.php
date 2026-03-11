<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-slate-900 leading-tight">
                {{ __('Inventory List') }}
            </h2>
            <a href="{{ route('inventory.create') }}" class="inline-flex items-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500">
                {{ __('Add new record') }}
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-4">
        @if (session('status'))
            <div class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <form method="GET" action="{{ route('inventory.list') }}" class="flex flex-wrap items-center gap-3">
            <input
                type="text"
                name="q"
                value="{{ $search ?? '' }}"
                placeholder="{{ __('Search product or warehouse...') }}"
                class="w-full max-w-md rounded-md border-slate-300 text-sm"
            />
            <button type="submit" class="inline-flex items-center rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                {{ __('Search') }}
            </button>
            @if (!empty($search))
                <a href="{{ route('inventory.list') }}" class="text-sm text-slate-600 hover:text-slate-900">{{ __('Clear') }}</a>
            @endif
        </form>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 text-left">{{ __('Product') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Warehouse') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Quantity') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Reserved') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Reorder point') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($inventories as $inventory)
                        <tr>
<td class="px-4 py-3">
    {{ optional($inventory->product)->name ?? '-' }}
</td>
<td class="px-4 py-3">
    {{ optional($inventory->warehouse)->name ?? '-' }}
</td>                            <td class="px-4 py-3">{{ number_format((float) $inventory->quantity, 2) }}</td>
                            <td class="px-4 py-3">{{ number_format((float) $inventory->reserved, 2) }}</td>
                            <td class="px-4 py-3">{{ number_format((float) $inventory->reorder_point, 2) }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('inventory.edit', $inventory) }}" class="text-amber-600 hover:text-amber-700 font-medium">{{ __('Edit') }}</a>
                                    <form method="POST" action="{{ route('inventory.destroy', $inventory) }}" onsubmit="return confirm('{{ __('Delete this inventory record?') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-700 font-medium">{{ __('Delete') }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-slate-500">{{ __('No inventory records found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>
            {{ $inventories->withQueryString()->links() }}
        </div>
    </div>
</x-app-layout>
