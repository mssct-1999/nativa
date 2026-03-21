<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-slate-900 leading-tight">{{ __('Shop Customers') }}</h2>
            <p class="text-sm text-slate-500">Track loyalty scores and discounts.</p>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <input type="text" name="q" value="{{ $search }}" placeholder="Search by name or email" class="w-64 rounded-lg border-slate-300">
            <x-button>Search</x-button>
        </form>

        <div class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Customer</th>
                        <th class="px-4 py-3">Score</th>
                        <th class="px-4 py-3">Discount</th>
                        <th class="px-4 py-3">Total spent</th>
                        <th class="px-4 py-3">Orders</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach ($customers as $customer)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-900">
                                    {{ optional($customer->user)->name ?? 'Unknown' }}
                                </div>

                                <div class="text-xs text-slate-500">
                                    {{ optional($customer->user)->email }}
                                </div>
                            </td>
                            <td class="px-4 py-3">{{ $customer->fidelity_score }}</td>
                            <td class="px-4 py-3">{{ number_format($customer->discount_rate, 0) }}%</td>
                            <td class="px-4 py-3">${{ number_format($customer->total_spent, 2) }}</td>
                            <td class="px-4 py-3">{{ $customer->orders_count }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('shop-customers.show', $customer) }}" class="text-sm text-emerald-700 hover:text-emerald-900">Details</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $customers->links() }}
        </div>
    </div>
</x-app-layout>
