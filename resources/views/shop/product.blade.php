<x-shop-layout>
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 py-10">
        <a href="{{ route('shop.show', $shop) }}" class="text-sm text-emerald-300">? Back to {{ $shop->name }}</a>

        <div class="mt-6 grid gap-10 lg:grid-cols-[1.2fr_0.8fr]">
            <div class="rounded-3xl border border-slate-800 bg-slate-900/60 p-8">
                <h1 class="text-3xl font-semibold text-white">{{ $product->name }}</h1>
                <p class="mt-4 text-slate-300">{{ $product->description ?: 'Product details coming soon.' }}</p>
                <div class="mt-6 flex flex-wrap items-center gap-4 text-sm text-slate-400">
                    <span>SKU: {{ $product->sku ?: 'n/a' }}</span>
                    <span>Barcode: {{ $product->barcode ?: 'n/a' }}</span>
                </div>
            </div>

            <div class="rounded-3xl border border-emerald-400/30 bg-emerald-500/5 p-8">
                <div class="text-sm uppercase tracking-[0.2em] text-emerald-200/80">Price</div>
                <div class="mt-3 text-3xl font-semibold text-emerald-200">${{ number_format($shopProduct->effectivePrice(), 2) }}</div>
                <div class="mt-4 text-sm text-slate-300">
                    @if ($inventory)
                        Available: {{ (float) $inventory->quantity - (float) $inventory->reserved }} units
                    @else
                        Stock on request
                    @endif
                </div>

                <form method="POST" action="{{ route('shop.cart.store', $shop) }}" class="mt-6 space-y-3">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <label class="text-xs uppercase tracking-widest text-slate-400">Quantity</label>
                    <input type="number" name="quantity" value="1" min="1" step="1" class="w-full rounded-xl border border-slate-700 bg-slate-900 px-4 py-2 text-slate-100">
                    <button class="w-full rounded-full bg-emerald-500 px-4 py-2 text-sm font-semibold text-slate-900 hover:bg-emerald-400">
                        Add to cart
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-shop-layout>
