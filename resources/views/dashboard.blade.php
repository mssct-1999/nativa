<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto">
        <x-index-overview :description="$pageDescription" :metrics="$metrics" :chart="$chart" />

        <div class="mt-6 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-slate-900">Last 5 Payments</h3>
                <span class="text-xs text-slate-500">Most recent by payment date</span>
            </div>

            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-3 text-left">Date</th>
                            <th class="px-4 py-3 text-left">Invoice</th>
                            <th class="px-4 py-3 text-left">Method</th>
                            <th class="px-4 py-3 text-left">Reference</th>
                            <th class="px-4 py-3 text-left">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($latestPayments as $payment)
                            <tr>
                                <td class="px-4 py-3">{{ optional($payment->paid_at)->format('Y-m-d') ?: '-' }}</td>
                                <td class="px-4 py-3">{{ optional($payment->invoice)->number ?: '-' }}</td>
                                <td class="px-4 py-3">{{ $payment->method ?: '-' }}</td>
                                <td class="px-4 py-3">{{ $payment->reference ?: '-' }}</td>
                                <td class="px-4 py-3">${{ number_format((float) $payment->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-slate-500">No payments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
