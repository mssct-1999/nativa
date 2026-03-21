<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-slate-900 leading-tight">{{ __('Customer Profile') }}</h2>
            <p class="text-sm text-slate-500">{{ $customer->user?->name ?? 'Unknown' }}</p>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <a href="{{ route('shop-customers.index') }}" class="text-sm text-slate-600 hover:text-slate-900">? Back to customers</a>
            <a href="{{ route('shop-customers.edit', $customer) }}" class="text-sm text-emerald-600 hover:text-emerald-700">Edit</a>
        </div>

        <div class="grid gap-6 md:grid-cols-3">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="text-xs uppercase text-slate-400">Fidelity score</div>
                <div class="mt-2 text-2xl font-semibold text-slate-900">{{ $customer->fidelity_score }}</div>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="text-xs uppercase text-slate-400">Discount rate</div>
                <div class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($customer->discount_rate, 0) }}%</div>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="text-xs uppercase text-slate-400">Total spent</div>
                <div class="mt-2 text-2xl font-semibold text-slate-900">${{ number_format($customer->total_spent, 2) }}</div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-base font-semibold text-slate-900">Recent Orders</h3>
            <div class="mt-4 space-y-3">
                @forelse ($customer->orders as $order)
                    <div class="flex items-center justify-between text-sm text-slate-600">
                        <span>{{ $order->number }} · {{ $order->created_at?->format('Y-m-d') }}</span>
                        <span>${{ number_format($order->total, 2) }}</span>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No orders yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
