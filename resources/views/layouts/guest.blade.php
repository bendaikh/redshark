<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
	<body class="font-sans text-gray-900 antialiased">
		<div class="min-h-screen grid md:grid-cols-2 bg-gray-100 dark:bg-gray-900">
			<div class="hidden md:flex items-center justify-center p-8 bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500">
				<div class="max-w-md text-white">
					<a href="/" class="inline-block">
						<x-application-logo class="w-16 h-16 mb-6 fill-current text-white" />
					</a>
					<h1 class="text-3xl font-bold leading-tight">{{ config('app.name', 'Laravel') }}</h1>
					<p class="mt-2 text-white/90">Sign in to manage your workspace and analytics.</p>
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
