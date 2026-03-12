<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Nativa</title>
        <link rel="icon" href="{{ asset('img/favicon_io/favicon.ico') }}">

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Scripts -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <script defer src="{{ asset('js/app.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    </head>
    <body class="font-sans antialiased bg-slate-100 text-slate-900">
        <div class="min-h-screen lg:flex">
            @include('layouts.navigation')

            <div class="flex-1 min-w-0">
                <!-- Page Heading -->
                <header class="bg-white/90 backdrop-blur shadow-sm border-b border-slate-200">
                    <div class="px-4 py-4 sm:px-6 lg:px-8">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div class="min-w-0">
                                {{ $header }}
                            </div>

                            <div class="flex items-center gap-3">
                                @php
                                    $locale = app()->getLocale();
                                    $languageOptions = [
                                        'en' => ['label' => 'English', 'flag' => asset('img/en.png')],
                                        'fr' => ['label' => 'Francais', 'flag' => asset('img/fr.png')],
                                        'pt' => ['label' => 'Portugues', 'flag' => asset('img/br.png')],
                                    ];
                                    $currentLanguage = $languageOptions[$locale] ?? $languageOptions['en'];
                                @endphp

                                <x-dropdown align="right" width="48">
                                    <x-slot name="trigger">
                                        <button class="flex items-center gap-2 rounded-full border border-slate-200 bg-white px-2 py-1 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">
                                            <img src="{{ $currentLanguage['flag'] }}" alt="{{ $currentLanguage['label'] }}" class="h-5 w-5 rounded-full object-cover" />
                                            <span class="hidden sm:inline">{{ strtoupper($locale) }}</span>
                                        </button>
                                    </x-slot>

                                    <x-slot name="content">
                                        @foreach ($languageOptions as $code => $option)
                                            <form method="POST" action="{{ route('settings.locale') }}">
                                                @csrf
                                                <input type="hidden" name="locale" value="{{ $code }}" />
                                                <button type="submit" class="flex w-full items-center gap-2 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">
                                                    <img src="{{ $option['flag'] }}" alt="{{ $option['label'] }}" class="h-5 w-5 rounded-full object-cover" />
                                                    <span>{{ $option['label'] }}</span>
                                                </button>
                                            </form>
                                        @endforeach
                                    </x-slot>
                                </x-dropdown>

                                <x-dropdown align="right" width="48">
                                    <x-slot name="trigger">
                                        <button class="flex items-center gap-2 rounded-full border border-slate-200 bg-white px-2 py-1 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">
                                            <img src="{{ Auth::user()->profilePhotoUrl() }}" alt="{{ Auth::user()->name }}" class="h-8 w-8 rounded-full object-cover" />
                                            <span class="hidden sm:inline">{{ Auth::user()->name }}</span>
                                        </button>
                                    </x-slot>

                                    <x-slot name="content">
                                        <x-dropdown-link :href="route('profile.edit')">
                                            {{ __('Profile Settings') }}
                                        </x-dropdown-link>
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
                    </div>
                </header>

                <!-- Page Content -->
                <main class="p-4 sm:p-6 lg:p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
