<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-slate-900 leading-tight">{{ __('Shops') }}</h2>
            <p class="text-sm text-slate-500">Manage storefronts connected to inventory.</p>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between gap-4">
            <div class="text-sm text-slate-500">{{ $shops->total() }} shops</div>
            <a href="{{ route('shops.create') }}" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500">New shop</a>
        </div>

        @if (session('status'))
            <div class="mt-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Slug</th>
                        <th class="px-4 py-3">Owner</th>
                        <th class="px-4 py-3">Warehouse</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach ($shops as $shop)
                        <tr>
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $shop->name }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $shop->slug }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ optional($shop->owner)->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ optional($shop->warehouse)->name ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2 py-1 text-xs {{ $shop->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $shop->is_active ? 'Active' : 'Paused' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <a href="{{ route('shops.catalog', $shop) }}" class="text-sm text-emerald-700 hover:text-emerald-900">Catalog</a>
                                <a href="{{ route('shops.edit', $shop) }}" class="text-sm text-slate-700 hover:text-slate-900">Edit</a>
                                <form method="POST" action="{{ route('shops.destroy', $shop) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-sm text-rose-600 hover:text-rose-800">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $shops->links() }}
        </div>
    </div>
</x-app-layout>
