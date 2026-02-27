<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="font-sans antialiased text-slate-900 selection:bg-blue-100 selection:text-blue-900">
        <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50/20 to-slate-200/50">
            
            @include('layouts.navigation')

            @if (isset($header))
                <header class="bg-white/40 backdrop-blur-md border-b border-slate-200/60 sticky top-0 z-10">
                    <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                        <div class="flex items-center space-x-2">
                            <div class="w-1 h-6 bg-blue-500 rounded-full"></div>
                            <div class="text-slate-800">
                                {{ $header }}
                            </div>
                        </div>
                    </div>
                </header>
            @endif

            <main class="animate-fade-in">
                {{ $slot }}
            </main>

            <footer class="py-10 text-center text-sm text-slate-400">
                &copy; {{ date('Y') }} {{ config('app.name') }} &bull; Internal Management System
            </footer>
        </div>
    </body>
</html>