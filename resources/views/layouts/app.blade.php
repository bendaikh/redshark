<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $textDir ?? 'ltr' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        @if(($currentLocale ?? 'en') === 'ar')
            <link href="https://fonts.bunny.net/css?family=cairo:400,500,600,700&display=swap" rel="stylesheet" />
        @else
            <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @endif

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- RTL Styles -->
        @if($isRtl ?? false)
        <style>
            body { font-family: 'Cairo', sans-serif !important; }
            .rtl-flip { transform: scaleX(-1); }
            [dir="rtl"] .space-x-8 > :not([hidden]) ~ :not([hidden]) { margin-left: 2rem; margin-right: 0; }
            [dir="rtl"] .ms-1 { margin-right: 0.25rem; margin-left: 0; }
            [dir="rtl"] .ms-6 { margin-right: 1.5rem; margin-left: 0; }
            [dir="rtl"] .ms-10 { margin-right: 2.5rem; margin-left: 0; }
            [dir="rtl"] .me-2 { margin-left: 0.5rem; margin-right: 0; }
            [dir="rtl"] .text-left { text-align: right; }
            [dir="rtl"] .text-right { text-align: left; }
        </style>
        @endif
    </head>
    <body class="{{ ($isRtl ?? false) ? 'font-arabic' : 'font-sans' }} antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
        
        @stack('scripts')
    </body>
</html>
