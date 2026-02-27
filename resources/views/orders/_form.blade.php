@csrf

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label for="number" class="block text-sm font-medium text-slate-700">Order number</label>
        <input id="number" name="number" type="text" required value="{{ old('number', $order->number ?? '') }}" class="mt-1 w-full rounded-md border-slate-300" />
        @error('number') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="client_id" class="block text-sm font-medium text-slate-700">Client</label>
        <select id="client_id" name="client_id" class="mt-1 w-full rounded-md border-slate-300">
            <option value="">None</option>
            @foreach ($clients as $client)
                <option value="{{ $client->id }}" @selected((string) old('client_id', $order->client_id ?? '') === (string) $client->id)>{{ $client->company_name }}</option>
            @endforeach
        </select>
        @error('client_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="created_by" class="block text-sm font-medium text-slate-700">Created by</label>
        <select id="created_by" name="created_by" class="mt-1 w-full rounded-md border-slate-300">
            <option value="">None</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}" @selected((string) old('created_by', $order->created_by ?? auth()->id()) === (string) $user->id)>{{ $user->name }}</option>
            @endforeach
        </select>
        @error('created_by') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="status" class="block text-sm font-medium text-slate-700">Status</label>
        <select id="status" name="status" required class="mt-1 w-full rounded-md border-slate-300">
            @foreach ($statuses as $status)
                <option value="{{ $status }}" @selected(old('status', $order->status ?? 'pending') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="total" class="block text-sm font-medium text-slate-700">Total</label>
        <input id="total" name="total" type="number" min="0" step="0.01" required value="{{ old('total', $order->total ?? '0') }}" class="mt-1 w-full rounded-md border-slate-300" />
        @error('total') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="ordered_at" class="block text-sm font-medium text-slate-700">Ordered at</label>
        <input id="ordered_at" name="ordered_at" type="date" value="{{ old('ordered_at', isset($order->ordered_at) ? optional($order->ordered_at)->format('Y-m-d') : '') }}" class="mt-1 w-full rounded-md border-slate-300" />
        @error('ordered_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="shipped_at" class="block text-sm font-medium text-slate-700">Shipped at</label>
        <input id="shipped_at" name="shipped_at" type="date" value="{{ old('shipped_at', isset($order->shipped_at) ? optional($order->shipped_at)->format('Y-m-d') : '') }}" class="mt-1 w-full rounded-md border-slate-300" />
        @error('shipped_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
</div>

<div class="mt-4">
    <label for="notes" class="block text-sm font-medium text-slate-700">Notes</label>
    <textarea id="notes" name="notes" rows="4" class="mt-1 w-full rounded-md border-slate-300">{{ old('notes', $order->notes ?? '') }}</textarea>
    @error('notes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>

<div class="mt-6 flex items-center gap-3">
    <button type="submit" class="inline-flex items-center rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
        Save
    </button>
    <a href="{{ route('orders.list') }}" class="text-sm text-slate-600 hover:text-slate-900">Cancel</a>
</div>
