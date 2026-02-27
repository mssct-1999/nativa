<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-slate-900 leading-tight">
                {{ __('Inventory List') }}
            </h2>
            <a href="{{ route('inventory.create') }}" class="inline-flex items-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500">
                Add new record
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-4">
        @if (session('status'))
            <div class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 text-left">Product</th>
                        <th class="px-4 py-3 text-left">Warehouse</th>
                        <th class="px-4 py-3 text-left">Quantity</th>
                        <th class="px-4 py-3 text-left">Reserved</th>
                        <th class="px-4 py-3 text-left">Reorder point</th>
                        <th class="px-4 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($inventories as $inventory)
                        <tr>
                            <td class="px-4 py-3">{{ $inventory->product?->name ?: '-' }}</td>
                            <td class="px-4 py-3">{{ $inventory->warehouse?->name ?: '-' }}</td>
                            <td class="px-4 py-3">{{ number_format((float) $inventory->quantity, 2) }}</td>
                            <td class="px-4 py-3">{{ number_format((float) $inventory->reserved, 2) }}</td>
                            <td class="px-4 py-3">{{ number_format((float) $inventory->reorder_point, 2) }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('inventory.edit', $inventory) }}" class="text-amber-600 hover:text-amber-700 font-medium">Edit</a>
                                    <form method="POST" action="{{ route('inventory.destroy', $inventory) }}" onsubmit="return confirm('Delete this inventory record?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-700 font-medium">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-slate-500">No inventory records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>
            {{ $inventories->links() }}
        </div>
    </div>
</x-app-layout>
