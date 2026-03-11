<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-slate-900 leading-tight">
                {{ __('Orders List') }}
            </h2>
            <a href="{{ route('orders.create') }}" class="inline-flex items-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500">
                {{ __('Add new order') }}
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-4">
        @if (session('status'))
            <div class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <form method="GET" action="{{ route('orders.list') }}" class="flex flex-wrap items-center gap-3">
            <input
                type="text"
                name="q"
                value="{{ $search ?? '' }}"
                placeholder="{{ __('Search order number, client, status...') }}"
                class="w-full max-w-md rounded-md border-slate-300 text-sm"
            />
            <button type="submit" class="inline-flex items-center rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                {{ __('Search') }}
            </button>
            @if (!empty($search))
                <a href="{{ route('orders.list') }}" class="text-sm text-slate-600 hover:text-slate-900">{{ __('Clear') }}</a>
            @endif
        </form>

        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 shadow-sm">
            <h3 class="text-base font-semibold text-slate-900">{{ __('Orders Timeline History') }}</h3>
            <p class="mt-1 text-sm text-slate-600">{{ __('Grouped by day with daily totals, like an account statement.') }}</p>

            @if (collect($timeline)->isNotEmpty())
                <div class="mt-4 space-y-4">
                    @foreach ($timeline as $dayGroup)
                        <section class="rounded-lg border border-slate-200 bg-white p-3">
                            <div class="flex items-center justify-between gap-3 border-b border-slate-100 pb-2">
                                <h4 class="text-sm font-semibold text-slate-900">{{ $dayGroup['day'] }}</h4>
                                <p class="text-sm font-semibold text-emerald-700">
                                    {{ __('Day total') }}: ${{ number_format((float) $dayGroup['day_total'], 2) }}
                                </p>
                            </div>

                            <div class="mt-3 space-y-2">
                                @foreach ($dayGroup['orders'] as $timelineOrder)
                                    <div class="rounded-md border border-slate-100 bg-slate-50 px-3 py-2">
                                        <div class="flex flex-wrap items-center justify-between gap-2">
                                            <div>
                                                <p class="text-sm font-semibold text-slate-900">{{ $timelineOrder->number }}</p>
                                                <p class="text-xs text-slate-600">
                                                    {{ __('Client') }}: {{ optional($timelineOrder->client)->company_name ?? '-' }}
                                                    <span class="mx-1 text-slate-300">|</span>
                                                    {{ __('Created by') }}: {{ optional($timelineOrder->user)->name ?? '-' }}
                                                    <span class="mx-1 text-slate-300">|</span>
                                                    {{ __('Status') }}: {{ ucfirst($timelineOrder->status) }}
                                                    <span class="mx-1 text-slate-300">|</span>
                                                    {{ __('Ordered') }}: {{ optional($timelineOrder->ordered_at)->format('Y-m-d') ?: optional($timelineOrder->created_at)->format('Y-m-d') }}
                                                    <span class="mx-1 text-slate-300">|</span>
                                                    {{ __('Shipped') }}: {{ optional($timelineOrder->shipped_at)->format('Y-m-d') ?: __('Not shipped') }}
                                                </p>
                                            </div>
                                            <p class="text-sm font-semibold text-slate-900">${{ number_format((float) $timelineOrder->total, 2) }}</p>
                                        </div>

                                        <div class="mt-2 flex items-center gap-3 text-sm">
                                            <a href="{{ route('orders.edit', $timelineOrder) }}" class="text-amber-600 hover:text-amber-700 font-medium">{{ __('Edit') }}</a>
                                            @if (empty($timelineOrder->shipped_at))
                                                <form method="POST" action="{{ route('orders.mark-shipped', $timelineOrder) }}" onsubmit="return confirm('{{ __('Mark order :number as shipped?', ['number' => $timelineOrder->number]) }}');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="text-emerald-600 hover:text-emerald-700 font-medium">{{ __('Mark as shipped') }}</button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endforeach
                </div>
            @else
                <p class="mt-3 text-sm text-slate-500">{{ __('No orders found for timeline history.') }}</p>
            @endif
        </div>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 text-left">{{ __('Number') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Client') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Status') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Total') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($orders as $order)
                        <tr>
                            <td class="px-4 py-3">{{ $order->number }}</td>
                            <td class="px-4 py-3">
                                {{ optional($order->client)->company_name ?? '-' }}
                            </td>
                            <td class="px-4 py-3">{{ ucfirst($order->status) }}</td>
                            <td class="px-4 py-3">${{ number_format((float) $order->total, 2) }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('orders.edit', $order) }}" class="text-amber-600 hover:text-amber-700 font-medium">{{ __('Edit') }}</a>
                                    <form method="POST" action="{{ route('orders.destroy', $order) }}" onsubmit="return confirm('{{ __('Delete this order?') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-700 font-medium">{{ __('Delete') }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-slate-500">{{ __('No orders found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>
            {{ $orders->withQueryString()->links() }}
        </div>
    </div>
</x-app-layout>
