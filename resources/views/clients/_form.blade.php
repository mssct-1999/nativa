@csrf

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label for="company_name" class="block text-sm font-medium text-slate-700">Company name</label>
        <input id="company_name" name="company_name" type="text" required value="{{ old('company_name', $client->company_name ?? '') }}" class="mt-1 w-full rounded-md border-slate-300" />
        @error('company_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="contact_name" class="block text-sm font-medium text-slate-700">Contact name</label>
        <input id="contact_name" name="contact_name" type="text" value="{{ old('contact_name', $client->contact_name ?? '') }}" class="mt-1 w-full rounded-md border-slate-300" />
        @error('contact_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email', $client->email ?? '') }}" class="mt-1 w-full rounded-md border-slate-300" />
        @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="phone" class="block text-sm font-medium text-slate-700">Phone</label>
        <input id="phone" name="phone" type="text" value="{{ old('phone', $client->phone ?? '') }}" class="mt-1 w-full rounded-md border-slate-300" />
        @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="address" class="block text-sm font-medium text-slate-700">Address</label>
        <input id="address" name="address" type="text" value="{{ old('address', $client->address ?? '') }}" class="mt-1 w-full rounded-md border-slate-300" />
        @error('address') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="city" class="block text-sm font-medium text-slate-700">City</label>
        <input id="city" name="city" type="text" value="{{ old('city', $client->city ?? '') }}" class="mt-1 w-full rounded-md border-slate-300" />
        @error('city') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="zipcode" class="block text-sm font-medium text-slate-700">Zipcode</label>
        <input id="zipcode" name="zipcode" type="text" value="{{ old('zipcode', $client->zipcode ?? '') }}" class="mt-1 w-full rounded-md border-slate-300" />
        @error('zipcode') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="country" class="block text-sm font-medium text-slate-700">Country</label>
        <input id="country" name="country" type="text" value="{{ old('country', $client->country ?? '') }}" class="mt-1 w-full rounded-md border-slate-300" />
        @error('country') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
</div>

<div class="mt-4">
    <label for="notes" class="block text-sm font-medium text-slate-700">Notes</label>
    <textarea id="notes" name="notes" rows="4" class="mt-1 w-full rounded-md border-slate-300">{{ old('notes', $client->notes ?? '') }}</textarea>
    @error('notes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>

<div class="mt-6 flex items-center gap-3">
    <button type="submit" class="inline-flex items-center rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
        Save
    </button>
    <a href="{{ route('clients.list') }}" class="text-sm text-slate-600 hover:text-slate-900">Cancel</a>
</div>
