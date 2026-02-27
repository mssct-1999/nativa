@csrf

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label for="number" class="block text-sm font-medium text-slate-700">Invoice number</label>
        <input id="number" name="number" type="text" required value="{{ old('number', $invoice->number ?? '') }}" class="mt-1 w-full rounded-md border-slate-300" />
        @error('number') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="client_id" class="block text-sm font-medium text-slate-700">Client</label>
        <select id="client_id" name="client_id" class="mt-1 w-full rounded-md border-slate-300">
            <option value="">None</option>
            @foreach ($clients as $client)
                <option value="{{ $client->id }}" @selected((string) old('client_id', $invoice->client_id ?? '') === (string) $client->id)>{{ $client->company_name }}</option>
            @endforeach
        </select>
        @error('client_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="quote_id" class="block text-sm font-medium text-slate-700">Quote</label>
        <select id="quote_id" name="quote_id" class="mt-1 w-full rounded-md border-slate-300">
            <option value="">None</option>
            @foreach ($quotes as $quote)
                <option value="{{ $quote->id }}" @selected((string) old('quote_id', $invoice->quote_id ?? '') === (string) $quote->id)>{{ $quote->number }}</option>
            @endforeach
        </select>
        @error('quote_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="created_by" class="block text-sm font-medium text-slate-700">Created by</label>
        <select id="created_by" name="created_by" class="mt-1 w-full rounded-md border-slate-300">
            <option value="">None</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}" @selected((string) old('created_by', $invoice->created_by ?? auth()->id()) === (string) $user->id)>{{ $user->name }}</option>
            @endforeach
        </select>
        @error('created_by') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="status" class="block text-sm font-medium text-slate-700">Status</label>
        <select id="status" name="status" required class="mt-1 w-full rounded-md border-slate-300">
            @foreach ($statuses as $status)
                <option value="{{ $status }}" @selected(old('status', $invoice->status ?? 'draft') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="sub_total" class="block text-sm font-medium text-slate-700">Subtotal</label>
        <input id="sub_total" name="sub_total" type="number" min="0" step="0.01" required value="{{ old('sub_total', $invoice->sub_total ?? '0') }}" class="mt-1 w-full rounded-md border-slate-300" />
        @error('sub_total') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="tax" class="block text-sm font-medium text-slate-700">Tax</label>
        <input id="tax" name="tax" type="number" min="0" step="0.01" required value="{{ old('tax', $invoice->tax ?? '0') }}" class="mt-1 w-full rounded-md border-slate-300" />
        @error('tax') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="discount" class="block text-sm font-medium text-slate-700">Discount</label>
        <input id="discount" name="discount" type="number" min="0" step="0.01" required value="{{ old('discount', $invoice->discount ?? '0') }}" class="mt-1 w-full rounded-md border-slate-300" />
        @error('discount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="total" class="block text-sm font-medium text-slate-700">Total</label>
        <input id="total" name="total" type="number" min="0" step="0.01" required value="{{ old('total', $invoice->total ?? '0') }}" class="mt-1 w-full rounded-md border-slate-300" />
        @error('total') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="issued_at" class="block text-sm font-medium text-slate-700">Issued at</label>
        <input id="issued_at" name="issued_at" type="date" value="{{ old('issued_at', isset($invoice->issued_at) ? optional($invoice->issued_at)->format('Y-m-d') : '') }}" class="mt-1 w-full rounded-md border-slate-300" />
        @error('issued_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="due_at" class="block text-sm font-medium text-slate-700">Due at</label>
        <input id="due_at" name="due_at" type="date" value="{{ old('due_at', isset($invoice->due_at) ? optional($invoice->due_at)->format('Y-m-d') : '') }}" class="mt-1 w-full rounded-md border-slate-300" />
        @error('due_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="paid_at" class="block text-sm font-medium text-slate-700">Paid at</label>
        <input id="paid_at" name="paid_at" type="date" value="{{ old('paid_at', isset($invoice->paid_at) ? optional($invoice->paid_at)->format('Y-m-d') : '') }}" class="mt-1 w-full rounded-md border-slate-300" />
        @error('paid_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
</div>

<div class="mt-4">
    <label for="notes" class="block text-sm font-medium text-slate-700">Notes</label>
    <textarea id="notes" name="notes" rows="4" class="mt-1 w-full rounded-md border-slate-300">{{ old('notes', $invoice->notes ?? '') }}</textarea>
    @error('notes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>

<div class="mt-6 flex items-center gap-3">
    <button type="submit" class="inline-flex items-center rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
        Save
    </button>
    <a href="{{ route('invoices.list') }}" class="text-sm text-slate-600 hover:text-slate-900">Cancel</a>
</div>
