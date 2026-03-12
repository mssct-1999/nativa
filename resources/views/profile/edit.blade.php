<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-slate-900 leading-tight">
                {{ __('Profile Settings') }}
            </h2>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-4">
        @if (session('status'))
            <div class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="flex items-center gap-4">
                    <img src="{{ $user->profilePhotoUrl() }}" alt="{{ $user->name }}" class="h-16 w-16 rounded-full object-cover" />
                    <div>
                        <label class="block text-sm font-medium text-slate-700">{{ __('Profile photo') }}</label>
                        <input type="file" name="profile_photo" accept="image/*" class="mt-1 text-sm text-slate-600" />
                        @error('profile_photo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700">{{ __('Name') }}</label>
                        <input id="name" name="name" type="text" required value="{{ old('name', $user->name) }}" class="mt-1 w-full rounded-md border-slate-300" />
                        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700">{{ __('Email') }}</label>
                        <input id="email" name="email" type="email" required value="{{ old('email', $user->email) }}" class="mt-1 w-full rounded-md border-slate-300" />
                        @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="inline-flex items-center rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                        {{ __('Save changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
