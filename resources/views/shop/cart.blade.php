<x-shop-layout>
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex items-start justify-between gap-4">
            <div>
                <a href="{{ route('shop.show', $shop) }}" class="text-sm text-emerald-300">? Continue shopping</a>
                <h1 class="mt-3 text-3xl font-semibold text-white">Your cart</h1>
            </div>
            <a href="{{ route('shop.checkout', $shop) }}" class="rounded-full bg-emerald-500 px-4 py-2 text-sm font-semibold text-slate-900 hover:bg-emerald-400">Checkout</a>
        </div>

        @if (session('status'))
            <div class="mt-4 rounded-2xl border border-emerald-400/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        <form id="update-cart" method="POST" action="{{ route('shop.cart.update', $shop) }}" class="mt-8 space-y-4">
            @csrf
            @method('PATCH')

            <div class="rounded-3xl border border-slate-800 bg-slate-900/60 p-6">
                @forelse ($items as $item)
                    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-800 py-4 last:border-b-0">
                        <div>
                            <div class="text-lg font-semibold text-white">{{ $item['name'] }}</div>
                            <div class="text-sm text-slate-400">${{ number_format($item['price'], 2) }} each</div>
                        </div>
                        <div class="flex items-center gap-3">
                            <input type="number" name="items[{{ $item['product_id'] }}][quantity]" value="{{ $item['quantity'] }}" min="0" step="1" class="w-24 rounded-xl border border-slate-700 bg-slate-900 px-3 py-2 text-slate-100">
                            <button form="remove-{{ $item['product_id'] }}" class="rounded-full border border-slate-700 px-4 py-2 text-sm text-slate-200 hover:border-rose-400/60 hover:text-rose-200">Remove</button>
                        </div>
                    </div>
                @empty
                    <div class="text-slate-400">Your cart is empty.</div>
                @endforelse
            </div>

            <div class="flex flex-wrap items-center justify-between gap-4">
                <button class="rounded-full border border-slate-700 px-4 py-2 text-sm text-slate-200 hover:border-emerald-400/50">Update cart</button>
                <div class="text-lg text-slate-200">
                    Subtotal: <span class="font-semibold text-white">${{ number_format($summary['subtotal'], 2) }}</span>
                </div>
            </div>
        </form>

        @foreach ($items as $item)
            <form id="remove-{{ $item['product_id'] }}" method="POST" action="{{ route('shop.cart.destroy', [$shop, $item['product_id']]) }}" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        @endforeach
    </div>
</x-shop-layout>
