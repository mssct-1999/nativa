<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 leading-tight">{{ __('Edit Customer') }}</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <form method="POST" action="{{ route('shop-customers.update', $customer) }}" class="space-y-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @method('PATCH')

            <div>
                <x-label :value="__('Customer')" />
                <div class="text-sm text-slate-600">{{ $customer->user?->name }} · {{ $customer->user?->email }}</div>
            </div>

            <div>
                <x-label for="fidelity_score" :value="__('Fidelity Score')" />
                <x-input id="fidelity_score" name="fidelity_score" type="number" min="0" class="mt-1 block w-full" value="{{ old('fidelity_score', $customer->fidelity_score) }}" required />
            </div>

            <div>
                <x-label for="discount_rate" :value="__('Discount Rate (%)')" />
                <x-input id="discount_rate" name="discount_rate" type="number" min="0" max="50" step="1" class="mt-1 block w-full" value="{{ old('discount_rate', $customer->discount_rate) }}" required />
            </div>

            <div class="flex justify-end">
                <x-button>{{ __('Save Changes') }}</x-button>
            </div>
        </form>
    </div>
</x-app-layout>
