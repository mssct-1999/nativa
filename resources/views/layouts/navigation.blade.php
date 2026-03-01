@php
    $menuItems = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'active' => 'dashboard'],
        ['label' => 'Clients', 'route' => 'clients.index', 'active' => 'clients.*'],
        ['label' => 'Products', 'route' => 'products.index', 'active' => 'products.*'],
        ['label' => 'Orders', 'route' => 'orders.index', 'active' => 'orders.*'],
        ['label' => 'Invoices', 'route' => 'invoices.index', 'active' => 'invoices.*'],
        ['label' => 'Inventory', 'route' => 'inventory.index', 'active' => 'inventory.*'],
        ['label' => 'Employees', 'route' => 'employees.index', 'active' => 'employees.*'],
        ['label' => 'Payrolls', 'route' => 'payrolls.index', 'active' => 'payrolls.*'],
        ['label' => 'Warehouses', 'route' => 'warehouses.index', 'active' => 'warehouses.*'],
    ];
@endphp

<aside class="hidden lg:flex lg:w-72 lg:flex-col lg:bg-white lg:border-r lg:border-slate-200 lg:min-h-screen">
    <div class="sticky top-0 flex h-screen flex-col">
        <div class="px-5 py-6 border-b border-slate-200">
            <a href="{{ route('dashboard') }}" class="flex items-center">
                <x-application-logo class="block h-10 w-auto fill-current text-emerald-600" />
                <span class="ml-3 text-lg font-bold tracking-tight text-slate-900">Nativa</span>
            </a>
            <p class="mt-2 text-xs font-medium uppercase tracking-wide text-slate-400">Operations Center</p>
        </div>

        <div class="flex-1 overflow-y-auto px-3 py-5 space-y-1">
            @foreach ($menuItems as $item)
                <x-nav-link :href="route($item['route'])" :active="request()->routeIs($item['active'])">
                    {{ __($item['label']) }}
                </x-nav-link>
            @endforeach
        </div>

        <div class="px-4 py-4 border-t border-slate-200">
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button class="flex items-center w-full rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">
                        <div class="text-left flex-1 truncate">{{ Auth::user()->name }}</div>

                        <div class="ml-2">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>
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

        <div class="pt-4 pb-1 border-t border-slate-200">
            <div class="px-4">
                <div class="font-medium text-base text-slate-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-slate-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</div>
