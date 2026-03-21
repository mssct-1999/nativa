<x-shop-layout>
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <div>
                <p class="text-sm uppercase tracking-[0.3em] text-emerald-300/70">E-Commerce</p>
                <h1 class="mt-3 text-3xl sm:text-4xl font-semibold text-white">Choose your shop</h1>
                <p class="mt-3 max-w-xl text-slate-300">
                    Each storefront is connected to real-time inventory. Prices and availability are synced with the ERP.
                </p>
            </div>
        </div>

        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($shops as $shop)
                <a href="{{ route('shop.show', $shop) }}" class="group rounded-3xl border border-slate-800 bg-gradient-to-br from-slate-900 to-slate-950 p-6 transition hover:border-emerald-400/50">
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-white group-hover:text-emerald-200">{{ $shop->name }}</h2>
                            <p class="mt-2 text-sm text-slate-400 line-clamp-3">{{ $shop->description ?: 'Inventory-led storefront for curated products.' }}</p>
                        </div>
                        <span class="rounded-full bg-emerald-500/10 px-3 py-1 text-xs text-emerald-200">Open</span>
                    </div>
                    <div class="mt-6 text-sm text-emerald-300">Enter shop →</div>
                </a>
            @empty
                <div class="rounded-3xl border border-slate-800 bg-slate-900/40 p-8 text-slate-400">
                    No active shops yet. Ask an admin to publish the first storefront.
                </div>
            @endforelse
        </div>
    </div>
</x-shop-layout>
