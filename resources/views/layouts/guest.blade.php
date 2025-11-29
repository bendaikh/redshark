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
            [dir="rtl"] .text-left { text-align: right; }
            [dir="rtl"] .text-right { text-align: left; }
        </style>
        @endif
    </head>
	<body class="{{ ($isRtl ?? false) ? 'font-arabic' : 'font-sans' }} text-gray-900 antialiased">
		<!-- Language Switcher -->
		<div class="absolute top-4 {{ ($isRtl ?? false) ? 'left-4' : 'right-4' }} z-50" x-data="{ langOpen: false }" @click.outside="langOpen = false">
			<button @click="langOpen = !langOpen" class="flex items-center gap-2 px-3 py-2 rounded-lg bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm text-sm text-gray-700 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-700 shadow-sm border border-gray-200 dark:border-gray-700 transition-all">
				<span class="text-lg">{{ $supportedLocales[$currentLocale ?? 'en']['flag'] ?? '🌐' }}</span>
				<span>{{ $supportedLocales[$currentLocale ?? 'en']['native'] ?? 'English' }}</span>
				<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
				</svg>
			</button>
			<div x-show="langOpen" x-transition class="absolute {{ ($isRtl ?? false) ? 'left-0' : 'right-0' }} mt-2 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 min-w-[150px]">
				@foreach($supportedLocales ?? [] as $code => $locale)
					<a href="{{ request()->fullUrlWithQuery(['lang' => $code]) }}" class="flex items-center gap-3 px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 {{ ($currentLocale ?? 'en') === $code ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400' : 'text-gray-700 dark:text-gray-300' }}">
						<span class="text-lg">{{ $locale['flag'] }}</span>
						<span>{{ $locale['native'] }}</span>
					</a>
				@endforeach
			</div>
		</div>

		<div class="min-h-screen grid md:grid-cols-2 bg-gray-100 dark:bg-gray-900">
			<div class="hidden md:flex items-center justify-center p-8 bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500">
				<div class="max-w-md text-white">
					<a href="/" class="inline-block">
						<x-application-logo class="w-16 h-16 mb-6 fill-current text-white" />
					</a>
					<h1 class="text-3xl font-bold leading-tight">{{ config('app.name', 'Laravel') }}</h1>
					<p class="mt-2 text-white/90">{{ __('Welcome') }} - {{ __('Login') }}</p>
				</div>
			</div>
			<div class="flex items-center justify-center p-6">
				<div class="w-full max-w-md bg-white dark:bg-gray-800 shadow-md rounded-xl p-6">
					{{ $slot }}
				</div>
			</div>
		</div>
	</body>
</html>
