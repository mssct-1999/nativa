<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Shop') }}</title>
        <link rel="icon" href="{{ asset('img/favicon_io/favicon.ico') }}">

        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap">
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <script defer src="{{ asset('js/app.js') }}"></script>
    </head>
    <body class="font-[Manrope] bg-slate-950 text-slate-100">
        <div class="min-h-screen flex flex-col">
            <header class="border-b border-slate-800 bg-slate-950/80 backdrop-blur">
                <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-wrap items-center justify-between gap-4 py-6">
                        <a href="{{ route('shop.index') }}" class="text-lg font-semibold tracking-wide text-emerald-400">
                            Nativa Shops
                        </a>

                        <div class="flex items-center gap-4 text-sm">
                            @auth
                                <span class="text-slate-300">Hi, {{ Auth::user()->name }}</span>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="rounded-full border border-emerald-400/40 px-4 py-2 text-emerald-200 hover:bg-emerald-500/10">
                                        Log out
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="rounded-full border border-slate-700 px-4 py-2 text-slate-200 hover:border-emerald-400/50 hover:text-emerald-200">Log in</a>
                                <a href="{{ route('register') }}" class="rounded-full bg-emerald-500 px-4 py-2 text-slate-900 font-semibold hover:bg-emerald-400">Create account</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1">
                {{ $slot }}
            </main>

            <footer class="border-t border-slate-800 bg-slate-950/80">
                <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 py-6 text-xs text-slate-400">
                    Powered by Nativa ERP · Inventory-led commerce
                </div>
            </footer>
        </div>
    </body>
</html>
