@php
    $menuItems = [
        ['label' => __('Dashboard'), 'route' => 'dashboard', 'active' => 'dashboard'],
        ['label' => __('Clients'), 'route' => 'clients.index', 'active' => 'clients.*'],
        ['label' => __('Products'), 'route' => 'products.index', 'active' => 'products.*'],
        ['label' => __('Orders'), 'route' => 'orders.index', 'active' => 'orders.*'],
        ['label' => __('POS'), 'route' => 'pos.index', 'active' => 'pos.*'],
        ['label' => __('Invoices'), 'route' => 'invoices.index', 'active' => 'invoices.*'],
        ['label' => __('Inventory'), 'route' => 'inventory.index', 'active' => 'inventory.*'],
        ['label' => __('Transactions'), 'route' => 'transactions.list', 'active' => 'transactions.*'],
        ['label' => __('Employees'), 'route' => 'employees.index', 'active' => 'employees.*'],
        ['label' => __('Payrolls'), 'route' => 'payrolls.index', 'active' => 'payrolls.*'],
        ['label' => __('Warehouses'), 'route' => 'warehouses.index', 'active' => 'warehouses.*'],
    ];
@endphp

<aside class="hidden lg:flex lg:w-72 lg:flex-col lg:bg-white lg:border-r lg:border-slate-200 lg:min-h-screen">
    <div class="sticky top-0 flex h-screen flex-col">
        <div class="px-5 py-6 border-b border-slate-200">
            <a href="{{ route('dashboard') }}" class="flex items-center">
                <x-application-logo class="block h-20 w-auto fill-current text-emerald-600" />
            </a>
            <p class="mt-2 text-xs font-medium uppercase tracking-wide text-slate-400">{{ __('Operations Center') }}</p>
        </div>

        <div class="flex-1 overflow-y-auto px-3 py-5 space-y-1">
            @foreach ($menuItems as $item)
                <x-nav-link :href="route($item['route'])" :active="request()->routeIs($item['active'])">
                    {{ __($item['label']) }}
                </x-nav-link>
            @endforeach
        </div>

        <div class="px-4 py-4 border-t border-slate-200"></div>
    </div>
</aside>

<!-- Mobile top nav (visible on small screens) -->
<div x-data="{ open: false }" class="lg:hidden bg-white border-b border-slate-200 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}" class="flex items-center">
                    <x-application-logo class="block h-8 w-auto fill-current text-emerald-600" />
                    <span class="ml-2 font-semibold text-slate-900">{{ config('app.name', 'App') }}</span>
                </a>
            </div>

            <div class="flex items-center">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-slate-500 hover:text-slate-700 hover:bg-slate-100 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div x-show="open" x-cloak class="pt-2 pb-3 space-y-1">
        @foreach ($menuItems as $item)
            <x-responsive-nav-link :href="route($item['route'])" :active="request()->routeIs($item['active'])">
                {{ __($item['label']) }}
            </x-responsive-nav-link>
        @endforeach

        <div class="pt-4 pb-1 border-t border-slate-200"></div>
    </div>
</div>
