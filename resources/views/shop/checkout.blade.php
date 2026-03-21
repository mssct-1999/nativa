<x-shop-layout>
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 py-10">
        <a href="{{ route('shop.cart', $shop) }}" class="text-sm text-emerald-300">? Back to cart</a>
        <h1 class="mt-3 text-3xl font-semibold text-white">Checkout</h1>

        @if ($errors->any())
            <div class="mt-4 rounded-2xl border border-rose-400/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="mt-8 grid gap-8 lg:grid-cols-[1.4fr_0.6fr]">
            <form method="POST" action="{{ route('shop.checkout.store', $shop) }}" class="rounded-3xl border border-slate-800 bg-slate-900/60 p-6 space-y-4">
                @csrf
                <div>
                    <label class="text-xs uppercase tracking-widest text-slate-400">Payment method</label>
                    <input type="text" name="payment_method" value="Card" class="mt-2 w-full rounded-xl border border-slate-700 bg-slate-900 px-4 py-2 text-slate-100" required>
                </div>
                <div>
                    <label class="text-xs uppercase tracking-widest text-slate-400">Order notes</label>
                    <textarea name="notes" rows="3" class="mt-2 w-full rounded-xl border border-slate-700 bg-slate-900 px-4 py-2 text-slate-100"></textarea>
                </div>
                <button class="w-full rounded-full bg-emerald-500 px-4 py-2 text-sm font-semibold text-slate-900 hover:bg-emerald-400">
                    Pay and place order
                </button>
            </form>

            <div class="rounded-3xl border border-emerald-400/30 bg-emerald-500/5 p-6">
                <div class="text-sm uppercase tracking-widest text-emerald-200/80">Summary</div>
                <div class="mt-4 space-y-2 text-sm text-slate-200">
                    <div class="flex items-center justify-between">
                        <span>Subtotal</span>
                        <span>${{ number_format($summary['subtotal'], 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Loyalty discount ({{ number_format($summary['discount_rate'], 0) }}%)</span>
                        <span>-${{ number_format($summary['discount'], 2) }}</span>
                    </div>
                    <div class="border-t border-emerald-400/20 pt-3 flex items-center justify-between text-base font-semibold text-emerald-200">
                        <span>Total</span>
                        <span>${{ number_format($summary['total'], 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 rounded-3xl border border-slate-800 bg-slate-900/60 p-6">
            <h2 class="text-lg font-semibold text-white">Items</h2>
            <div class="mt-4 space-y-3">
                @foreach ($items as $item)
                    <div class="flex items-center justify-between text-sm text-slate-300">
                        <span>{{ $item['name'] }} × {{ $item['quantity'] }}</span>
                        <span>${{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-shop-layout>
