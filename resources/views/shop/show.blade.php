<x-shop-layout>
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex flex-wrap items-start justify-between gap-6">
            <div>
                <a href="{{ route('shop.index') }}" class="text-sm text-emerald-300">? Back to shops</a>
                <h1 class="mt-3 text-3xl sm:text-4xl font-semibold text-white">{{ $shop->name }}</h1>
                <p class="mt-3 max-w-2xl text-slate-300">{{ $shop->description ?: 'Curated inventory from the ERP catalog.' }}</p>
            </div>
            <a href="{{ route('shop.cart', $shop) }}" class="rounded-full border border-emerald-400/40 px-4 py-2 text-sm text-emerald-200 hover:bg-emerald-500/10">
                View cart
            </a>
        </div>

        @if (session('status'))
            <div class="mt-4 rounded-2xl border border-emerald-400/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($shopProducts as $shopProduct)
                @php
                    $product = $shopProduct->product;
                    $inventory = $inventoryMap->get($product->id);
                    $available = $inventory ? (float) $inventory->quantity - (float) $inventory->reserved : null;
                    $price = $shopProduct->effectivePrice();
                @endphp
                <div class="rounded-3xl border border-slate-800 bg-slate-900/60 p-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-white">{{ $product->name }}</h2>
                        <span class="text-lg font-semibold text-emerald-300">${{ number_format($price, 2) }}</span>
                    </div>
                    <p class="mt-2 text-sm text-slate-400 line-clamp-3">{{ $product->description }}</p>
                    <div class="mt-4 flex items-center justify-between text-xs text-slate-400">
                        <span>{{ $product->sku ?: 'SKU n/a' }}</span>
                        <span>
                            @if ($available === null)
                                Stock on request
                            @else
                                {{ $available }} in stock
                            @endif
                        </span>
                    </div>
                    <div class="mt-4 flex items-center gap-3">
                        <form method="POST" action="{{ route('shop.cart.store', $shop) }}" class="flex-1">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" value="1">
                            <button class="w-full rounded-full bg-emerald-500 px-4 py-2 text-sm font-semibold text-slate-900 hover:bg-emerald-400">
                                Add to cart
                            </button>
                        </form>
                        <a href="{{ route('shop.product', [$shop, $product]) }}" class="rounded-full border border-slate-700 px-4 py-2 text-sm text-slate-200 hover:border-emerald-400/50">
                            Details
                        </a>
                    </div>
                </div>
            @empty
                <div class="rounded-3xl border border-slate-800 bg-slate-900/40 p-8 text-slate-400">
                    No products available in this shop yet.
                </div>
            @endforelse
        </div>
    </div>
</x-shop-layout>
