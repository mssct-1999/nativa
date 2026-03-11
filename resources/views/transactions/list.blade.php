<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-slate-900 leading-tight">
                {{ __('Transactions History') }}
            </h2>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-4">
        @if (session('status'))
            <div class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <form method="GET" action="{{ route('transactions.list') }}" class="flex flex-wrap items-center gap-3">
            <input
                type="text"
                name="q"
                value="{{ $search ?? '' }}"
                placeholder="{{ __('Search transaction, account...') }}"
                class="w-full max-w-md rounded-md border-slate-300 text-sm"
            />
            <button type="submit" class="inline-flex items-center rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                {{ __('Search') }}
            </button>
            @if (!empty($search))
                <a href="{{ route('transactions.list') }}" class="text-sm text-slate-600 hover:text-slate-900">{{ __('Clear') }}</a>
            @endif
        </form>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 text-left">{{ __('Date') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Type') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Amount') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Account') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Description') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Reference') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($transactions as $transaction)
                        <tr>
                            <td class="px-4 py-3">{{ optional($transaction->date)->format('Y-m-d') ?: $transaction->created_at->format('Y-m-d') }}</td>
                            <td class="px-4 py-3 capitalize">{{ $transaction->type }}</td>
                            <td class="px-4 py-3">${{ number_format((float) $transaction->amount, 2) }}</td>
                            <td class="px-4 py-3">{{ $transaction->account->name ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $transaction->description ?: '-' }}</td>
                            <td class="px-4 py-3">
                                {{ $transaction->reference_type }} #{{ $transaction->reference_id }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-slate-500">{{ __('No transactions found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>
            {{ $transactions->withQueryString()->links() }}
        </div>
    </div>
</x-app-layout>
