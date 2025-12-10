<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $textDir ?? 'ltr' }}">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="csrf-token" content="{{ csrf_token() }}">

		<title>{{ config('app.name', 'Laravel') }} — {{ __('Admin') }}</title>

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
		<!-- Mobile-first responsive styles -->
		<style>
			/* Touch-friendly utilities */
			.touch-manipulation { touch-action: manipulation; }
			.safe-area-top { padding-top: env(safe-area-inset-top); }
			.safe-area-bottom { padding-bottom: env(safe-area-inset-bottom); }
			
			/* Smooth scrolling for mobile */
			@media (max-width: 768px) {
				html { scroll-behavior: smooth; }
				body { -webkit-overflow-scrolling: touch; }
				
				/* Prevent horizontal scroll */
				body, html { overflow-x: hidden; max-width: 100vw; }
				
				/* Better tap highlighting */
				* { -webkit-tap-highlight-color: rgba(99, 102, 241, 0.1); }
				
				/* Larger touch targets */
				button, a, select, input[type="checkbox"] { min-height: 44px; min-width: 44px; }
				
				/* Better form inputs on mobile */
				input, select, textarea { font-size: 16px !important; /* Prevents zoom on iOS */ }
				
				/* Cards spacing on mobile */
				.mobile-card { margin-bottom: 1rem; }
				
				/* Text sizes for mobile readability */
				.text-3xl { font-size: 1.5rem; line-height: 2rem; }
			}
			
			/* Smooth sidebar animation */
			.sidebar-transition {
				transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
			}
		</style>
		
		@if($isRtl ?? false)
		<style>
			body { font-family: 'Cairo', sans-serif !important; }
			.rtl-flip { transform: scaleX(-1); }
			/* RTL specific adjustments */
			[dir="rtl"] .space-x-8 > :not([hidden]) ~ :not([hidden]) { margin-left: 2rem; margin-right: 0; }
			[dir="rtl"] .ms-1 { margin-right: 0.25rem; margin-left: 0; }
			[dir="rtl"] .ms-6 { margin-right: 1.5rem; margin-left: 0; }
			[dir="rtl"] .ms-10 { margin-right: 2.5rem; margin-left: 0; }
			[dir="rtl"] .me-2 { margin-left: 0.5rem; margin-right: 0; }
			[dir="rtl"] .ml-4 { margin-right: 1rem; margin-left: 0; }
			[dir="rtl"] .pl-4 { padding-right: 1rem; padding-left: 0; }
			[dir="rtl"] .border-l-2 { border-left-width: 0; border-right-width: 2px; }
			[dir="rtl"] .left-0 { left: auto; right: 0; }
			[dir="rtl"] .right-3 { right: auto; left: 0.75rem; }
			[dir="rtl"] .-translate-x-full { transform: translateX(100%); }
			[dir="rtl"] .text-left { text-align: right; }
			[dir="rtl"] .text-right { text-align: left; }
		</style>
		@endif
	</head>
	<body class="{{ ($isRtl ?? false) ? 'font-arabic' : 'font-sans' }} antialiased" x-data="{ 
		sidebarOpen: false, 
		sidebarCollapsed: false, 
		darkMode: false,
		init() {
			// Load sidebar state from localStorage
			const saved = localStorage.getItem('adminSidebarCollapsed');
			if (saved !== null) {
				this.sidebarCollapsed = saved === 'true';
			}
			// On desktop, sidebar should be open by default
			if (window.innerWidth >= 768) {
				this.sidebarOpen = true;
			}
			// Load dark mode preference
			const savedTheme = localStorage.getItem('theme');
			if (savedTheme === 'dark') {
				this.darkMode = true;
				document.documentElement.classList.add('dark');
			} else if (savedTheme === 'light') {
				this.darkMode = false;
				document.documentElement.classList.remove('dark');
			} else {
				// Default to system preference
				this.darkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
				if (this.darkMode) {
					document.documentElement.classList.add('dark');
				}
			}
		},
		toggleSidebar() {
			this.sidebarCollapsed = !this.sidebarCollapsed;
			localStorage.setItem('adminSidebarCollapsed', this.sidebarCollapsed);
		},
		toggleTheme() {
			this.darkMode = !this.darkMode;
			if (this.darkMode) {
				document.documentElement.classList.add('dark');
				localStorage.setItem('theme', 'dark');
			} else {
				document.documentElement.classList.remove('dark');
				localStorage.setItem('theme', 'light');
			}
		}
	}">
		<div class="min-h-screen bg-gray-100 dark:bg-gray-900">
			<!-- Mobile top bar -->
			<div class="md:hidden bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 safe-area-top">
				<!-- Main row: hamburger, logo, quick actions -->
				<div class="flex items-center justify-between px-3 py-2.5">
					<div class="flex items-center gap-2">
						<button @click="sidebarOpen = true" class="p-2.5 -ml-1 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 active:bg-gray-200 dark:active:bg-gray-600 touch-manipulation">
							<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
							</svg>
						</button>
						<div class="flex items-center gap-2">
							<svg class="h-7 w-7 text-emerald-600 dark:text-emerald-400" viewBox="0 0 24 24" fill="currentColor">
								<path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4a2 2 0 0 0 1-1.73zM12 3.84 18.74 8 12 12.16 5.26 8zm-7 6.32 6 3.6v6.4l-6-3.43zm8 10v-6.4l6-3.6v6.57z"/>
							</svg>
							<span class="text-base font-bold text-gray-900 dark:text-white">{{ __('Admin') }}</span>
						</div>
					</div>
					<div class="flex items-center gap-1">
						<!-- Theme toggle -->
						<button onclick="toggleDarkMode()" class="p-2.5 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 active:bg-gray-200 dark:active:bg-gray-600 touch-manipulation" title="Toggle Theme" id="mobileThemeBtn">
							<svg id="mobileThemeIcon" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
							</svg>
						</button>
						<!-- More options dropdown -->
						<div x-data="{ mobileMenuOpen: false }" @click.outside="mobileMenuOpen = false" class="relative">
							<button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2.5 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 active:bg-gray-200 dark:active:bg-gray-600 touch-manipulation">
								<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
								</svg>
							</button>
							<div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute {{ ($isRtl ?? false) ? 'left-0' : 'right-0' }} mt-2 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 py-2 z-50">
								<!-- Language section -->
								<div class="px-3 py-2 border-b border-gray-100 dark:border-gray-700">
									<p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">{{ __('Language') }}</p>
									<div class="space-y-1">
										@foreach($supportedLocales ?? [] as $code => $locale)
											<a href="{{ request()->fullUrlWithQuery(['lang' => $code]) }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ ($currentLocale ?? 'en') === $code ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
												<span class="text-lg">{{ $locale['flag'] }}</span>
												<span>{{ $locale['native'] }}</span>
												@if(($currentLocale ?? 'en') === $code)
													<svg class="ml-auto h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
												@endif
											</a>
										@endforeach
									</div>
								</div>
								<!-- Quick links -->
								<div class="px-3 py-2">
									<a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
										<svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
										<span>{{ __('User Dashboard') }}</span>
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- Secondary row: Country selector -->
				<div class="px-3 pb-2.5">
					<select onchange="window.location.href='?country=' + this.value" class="w-full py-2.5 px-3 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-sm font-medium text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
						@php
							$countries = \App\Models\Country::where('name', '!=', 'Global')->orderBy('name')->get();
							$currentCountryId = session('current_country_id');
						@endphp
						<option value="0" {{ $currentCountryId == 0 || !$currentCountryId ? 'selected' : '' }}>
							🌍 {{ __('Global - All Countries') }}
						</option>
						@foreach($countries as $country)
							<option value="{{ $country->id }}" {{ $currentCountryId == $country->id ? 'selected' : '' }}>
								📍 {{ $country->name }}
							</option>
						@endforeach
					</select>
				</div>
			</div>

			<div class="flex h-[calc(100vh-var(--mobile-header-height,0px))] md:h-screen">
				<!-- Sidebar -->
				<aside
					class="fixed inset-y-0 {{ ($isRtl ?? false) ? 'right-0 border-l' : 'left-0 border-r' }} z-30 w-72 transform transition-all duration-300 md:sticky md:top-0 md:h-screen bg-gradient-to-br from-emerald-800 via-teal-800 to-emerald-900 dark:from-emerald-950 dark:via-teal-950 dark:to-emerald-900 border-emerald-700/50 dark:border-emerald-800/50 overflow-y-auto shadow-2xl flex-shrink-0"
					:class="{ 
						'{{ ($isRtl ?? false) ? 'translate-x-full' : '-translate-x-full' }}': !sidebarOpen,
						'md:w-72': !sidebarCollapsed,
						'md:w-20': sidebarCollapsed
					}"
					x-cloak
				>
					<div class="flex h-full flex-col justify-between p-4">
						<div>
							<div class="hidden md:flex items-center justify-center px-2 pb-6 border-b border-emerald-700/50 dark:border-emerald-800/50" :class="{ 'justify-start': !sidebarCollapsed }">
								<a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 group" :class="{ 'justify-center': sidebarCollapsed }">
									<svg class="h-7 w-7 text-emerald-300 shrink-0 group-hover:text-emerald-200 transition-colors" viewBox="0 0 24 24" fill="currentColor">
										<path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4a2 2 0 0 0 1-1.73zM12 3.84 18.74 8 12 12.16 5.26 8zm-7 6.32 6 3.6v6.4l-6-3.43zm8 10v-6.4l6-3.6v6.57z"/>
									</svg>
									<span class="text-lg font-bold text-white whitespace-nowrap group-hover:text-emerald-200 transition-colors" x-show="!sidebarCollapsed" x-transition>{{ __('Admin') }}</span>
								</a>
							</div>

							<nav class="space-y-2 pt-6">
								<a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/50' : 'text-emerald-100 hover:text-white hover:bg-emerald-700/60 dark:hover:bg-emerald-800/60' }}" :class="{ 'justify-center': sidebarCollapsed }" title="{{ __('Dashboard') }}">
									<svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M3 12l9-9 9 9h-2v8a2 2 0 0 1-2 2h-4v-6H9v6H7a2 2 0 0 1-2-2v-8z"/></svg>
									<span x-show="!sidebarCollapsed" x-transition class="whitespace-nowrap">{{ __('Dashboard') }}</span>
								</a>
								
								<!-- Products Section -->
								<div x-data="{ open: {{ request()->routeIs('products.*') || request()->routeIs('categories.*') || request()->routeIs('suppliers.*') ? 'true' : 'false' }} }">
									<button @click="open = !open" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('products.*') || request()->routeIs('categories.*') || request()->routeIs('suppliers.*') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/50' : 'text-emerald-100 hover:text-white hover:bg-emerald-700/60 dark:hover:bg-emerald-800/60' }}" :class="{ 'justify-center': sidebarCollapsed }" title="{{ __('Products') }}">
										<svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M20 6H8l-1-2H4a1 1 0 0 0 0 2h2l3.6 7.59L8.25 17A1.5 1.5 0 0 0 9.75 19h9.5a1 1 0 0 0 0-2h-9l1.1-2h7.27a2 2 0 0 0 1.86-1.25l2-5A1 1 0 0 0 20 6z"/></svg>
										<span x-show="!sidebarCollapsed" x-transition class="whitespace-nowrap">{{ __('Products') }}</span>
										<svg x-show="!sidebarCollapsed" x-transition class="h-4 w-4 shrink-0 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
										</svg>
									</button>
									<div x-show="open && !sidebarCollapsed" x-transition class="{{ ($isRtl ?? false) ? 'mr-4 pr-4 border-r-2' : 'ml-4 pl-4 border-l-2' }} mt-1 space-y-1 border-emerald-700/50 dark:border-emerald-800/50">
										<a href="{{ route('products.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('products.index') ? 'bg-emerald-600/80 text-white' : 'text-emerald-200 hover:text-white hover:bg-emerald-700/60 dark:hover:bg-emerald-800/60' }}" title="{{ __('Products') }}">
											<svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M20 6H8l-1-2H4a1 1 0 0 0 0 2h2l3.6 7.59L8.25 17A1.5 1.5 0 0 0 9.75 19h9.5a1 1 0 0 0 0-2h-9l1.1-2h7.27a2 2 0 0 0 1.86-1.25l2-5A1 1 0 0 0 20 6z"/></svg>
											<span class="whitespace-nowrap">{{ __('Products') }}</span>
										</a>
										<a href="{{ route('products.create') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('products.create') ? 'bg-emerald-600/80 text-white' : 'text-emerald-200 hover:text-white hover:bg-emerald-700/60 dark:hover:bg-emerald-800/60' }}" title="{{ __('Add Product') }}">
											<svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
											<span class="whitespace-nowrap">{{ __('Add Product') }}</span>
										</a>
										<a href="{{ route('categories.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('categories.*') ? 'bg-emerald-600/80 text-white' : 'text-emerald-200 hover:text-white hover:bg-emerald-700/60 dark:hover:bg-emerald-800/60' }}" title="{{ __('Categories') }}">
											<svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M10 4H4c-1.11 0-2 .89-2 2v12c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2h-8l-2-2z"/></svg>
											<span class="whitespace-nowrap">{{ __('Categories') }}</span>
										</a>
										<a href="{{ route('suppliers.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('suppliers.*') ? 'bg-emerald-600/80 text-white' : 'text-emerald-200 hover:text-white hover:bg-emerald-700/60 dark:hover:bg-emerald-800/60' }}" title="{{ __('Suppliers') }}">
											<svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M17 20h5v-2a3 3 0 0 0-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 0 1 5.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 0 1 9.288 0M15 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0zm6 3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM7 10a2 2 0 1 1-4 0 2 2 0 0 1 4 0z"/></svg>
											<span class="whitespace-nowrap">{{ __('Suppliers') }}</span>
										</a>
									</div>
								</div>
								<!-- Sourcing Section -->
								<div x-data="{ open: {{ request()->routeIs('sourcings.*') || request()->routeIs('shipping-methods.*') ? 'true' : 'false' }} }">
									<button @click="open = !open" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('sourcings.*') || request()->routeIs('shipping-methods.*') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/50' : 'text-emerald-100 hover:text-white hover:bg-emerald-700/60 dark:hover:bg-emerald-800/60' }}" :class="{ 'justify-center': sidebarCollapsed }" title="{{ __('Sourcing') }}">
										<svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4zM6 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm13.5-9l1.96 2.5H17V9.5h2.5zm-1.5 9c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/></svg>
										<span x-show="!sidebarCollapsed" x-transition class="whitespace-nowrap">{{ __('Sourcing') }}</span>
										<svg x-show="!sidebarCollapsed" x-transition class="h-4 w-4 shrink-0 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
										</svg>
									</button>
									<div x-show="open && !sidebarCollapsed" x-transition class="{{ ($isRtl ?? false) ? 'mr-4 pr-4 border-r-2' : 'ml-4 pl-4 border-l-2' }} mt-1 space-y-1 border-emerald-700/50 dark:border-emerald-800/50">
										<a href="{{ route('sourcings.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('sourcings.index') || request()->routeIs('sourcings.show') || request()->routeIs('sourcings.edit') ? 'bg-emerald-600/80 text-white' : 'text-emerald-200 hover:text-white hover:bg-emerald-700/60 dark:hover:bg-emerald-800/60' }}" title="{{ __('Sourcing') }}">
											<svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4zM6 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm13.5-9l1.96 2.5H17V9.5h2.5zm-1.5 9c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/></svg>
											<span class="whitespace-nowrap">{{ __('Sourcing') }}</span>
										</a>
										<a href="{{ route('sourcings.create') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('sourcings.create') ? 'bg-emerald-600/80 text-white' : 'text-emerald-200 hover:text-white hover:bg-emerald-700/60 dark:hover:bg-emerald-800/60' }}" title="{{ __('Sourcing Creation') }}">
											<svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
											<span class="whitespace-nowrap">{{ __('Sourcing Creation') }}</span>
										</a>
										<a href="{{ route('shipping-methods.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('shipping-methods.*') ? 'bg-emerald-600/80 text-white' : 'text-emerald-200 hover:text-white hover:bg-emerald-700/60 dark:hover:bg-emerald-800/60' }}" title="{{ __('Shipping Methods') }}">
											<svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4zM6 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm13.5-9l1.96 2.5H17V9.5h2.5zm-1.5 9c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/></svg>
											<span class="whitespace-nowrap">{{ __('Shipping Methods') }}</span>
										</a>
									</div>
								</div>
								<a href="{{ route('invoices.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('invoices.*') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/50' : 'text-emerald-100 hover:text-white hover:bg-emerald-700/60 dark:hover:bg-emerald-800/60' }}" :class="{ 'justify-center': sidebarCollapsed }" title="{{ __('Invoices') }}">
									<svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M8 2h8a2 2 0 0 1 2 2v18l-6-3-6 3V4a2 2 0 0 1 2-2zm2 5h4v2h-4V7zm0 4h4v2h-4v-2z"/></svg>
									<span x-show="!sidebarCollapsed" x-transition class="whitespace-nowrap">{{ __('Invoices') }}</span>
								</a>
								<!-- Accounting Section -->
								<div x-data="{ open: {{ request()->routeIs('expense-categories.*') || request()->routeIs('balances.*') || request()->routeIs('expenses.*') ? 'true' : 'false' }} }">
									<button @click="open = !open" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('expense-categories.*') || request()->routeIs('balances.*') || request()->routeIs('expenses.*') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/50' : 'text-emerald-100 hover:text-white hover:bg-emerald-700/60 dark:hover:bg-emerald-800/60' }}" :class="{ 'justify-center': sidebarCollapsed }" title="{{ __('Accounting') }}">
										<svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M7 2h10a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2zm1 4v2h8V6H8zm0 4v2h8v-2H8zm0 4v2h6v-2H8z"/></svg>
										<span x-show="!sidebarCollapsed" x-transition class="whitespace-nowrap">{{ __('Accounting') }}</span>
										<svg x-show="!sidebarCollapsed" x-transition class="h-4 w-4 shrink-0 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
										</svg>
									</button>
									<div x-show="open && !sidebarCollapsed" x-transition class="{{ ($isRtl ?? false) ? 'mr-4 pr-4 border-r-2' : 'ml-4 pl-4 border-l-2' }} mt-1 space-y-1 border-emerald-700/50 dark:border-emerald-800/50">
										<a href="{{ route('expense-categories.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('expense-categories.*') ? 'bg-emerald-600/80 text-white' : 'text-emerald-200 hover:text-white hover:bg-emerald-700/60 dark:hover:bg-emerald-800/60' }}" title="{{ __('Expense Categories') }}">
											<svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M10 4H4c-1.11 0-2 .89-2 2v12c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2h-8l-2-2z"/></svg>
											<span class="whitespace-nowrap">{{ __('Expense Categories') }}</span>
										</a>
										<a href="{{ route('balances.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('balances.*') ? 'bg-emerald-600/80 text-white' : 'text-emerald-200 hover:text-white hover:bg-emerald-700/60 dark:hover:bg-emerald-800/60' }}" title="{{ __('Balances') }}">
											<svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
											<span class="whitespace-nowrap">{{ __('Balances') }}</span>
										</a>
										<a href="{{ route('expenses.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('expenses.*') ? 'bg-emerald-600/80 text-white' : 'text-emerald-200 hover:text-white hover:bg-emerald-700/60 dark:hover:bg-emerald-800/60' }}" title="{{ __('Expenses') }}">
											<svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M7 10h10M7 14h6M7 2h10a2 2 0 0 1 2 2v3H5V4a2 2 0 0 1 2-2zM5 9h14v9a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2z"/></svg>
											<span class="whitespace-nowrap">{{ __('Expenses') }}</span>
										</a>
									</div>
								</div>
								<!-- Ads Section -->
								<div x-data="{ open: {{ request()->routeIs('ads-campaigns.*') || request()->routeIs('ads-platforms.*') ? 'true' : 'false' }} }">
									<button @click="open = !open" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('ads-campaigns.*') || request()->routeIs('ads-platforms.*') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/50' : 'text-emerald-100 hover:text-white hover:bg-emerald-700/60 dark:hover:bg-emerald-800/60' }}" :class="{ 'justify-center': sidebarCollapsed }" title="{{ __('Ads') }}">
										<svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M4 4h16v4H4zm0 6h10v4H4zm0 6h16v4H4z"/></svg>
										<span x-show="!sidebarCollapsed" x-transition class="whitespace-nowrap">{{ __('Ads') }}</span>
										<svg x-show="!sidebarCollapsed" x-transition class="h-4 w-4 shrink-0 transition-transform duration-200 {{ ($isRtl ?? false) ? 'rotate-180' : '' }}" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
										</svg>
									</button>
									<div x-show="open && !sidebarCollapsed" x-transition class="{{ ($isRtl ?? false) ? 'mr-4 pr-4 border-r-2' : 'ml-4 pl-4 border-l-2' }} mt-1 space-y-1 border-emerald-700/50 dark:border-emerald-800/50">
										<a href="{{ route('ads-campaigns.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('ads-campaigns.*') ? 'bg-emerald-600/80 text-white' : 'text-emerald-200 hover:text-white hover:bg-emerald-700/60 dark:hover:bg-emerald-800/60' }}" title="{{ __('Ads Campaigns') }}">
											<svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M4 4h16v4H4zm0 6h10v4H4zm0 6h16v4H4z"/></svg>
											<span class="whitespace-nowrap">{{ __('Ads Campaigns') }}</span>
										</a>
										<a href="{{ route('ads-platforms.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('ads-platforms.*') ? 'bg-emerald-600/80 text-white' : 'text-emerald-200 hover:text-white hover:bg-emerald-700/60 dark:hover:bg-emerald-800/60' }}" title="{{ __('Manage Platforms') }}">
											<svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
											<span class="whitespace-nowrap">{{ __('Manage Platforms') }}</span>
										</a>
									</div>
								</div>
								<!-- Testing Section -->
								<a href="{{ route('testing-products.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('testing-products.*') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/50' : 'text-emerald-100 hover:text-white hover:bg-emerald-700/60 dark:hover:bg-emerald-800/60' }}" :class="{ 'justify-center': sidebarCollapsed }" title="{{ __('Testing') }}">
									<svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM9 18H7v-5h2v5zm4 0h-2V6h2v12zm4 0h-2v-8h2v8z"/></svg>
									<span x-show="!sidebarCollapsed" x-transition class="whitespace-nowrap">{{ __('Testing') }}</span>
								</a>
								<!-- Users Management Section -->
								<a href="{{ route('users.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('users.*') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/50' : 'text-emerald-100 hover:text-white hover:bg-emerald-700/60 dark:hover:bg-emerald-800/60' }}" :class="{ 'justify-center': sidebarCollapsed }" title="{{ __('Users Management') }}">
									<svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
									<span x-show="!sidebarCollapsed" x-transition class="whitespace-nowrap">{{ __('Users Management') }}</span>
								</a>
								<a href="{{ route('admin.settings') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.settings') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/50' : 'text-emerald-100 hover:text-white hover:bg-emerald-700/60 dark:hover:bg-emerald-800/60' }}" :class="{ 'justify-center': sidebarCollapsed }" title="{{ __('Settings') }}">
									<svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.07.62-.07.94s.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"/></svg>
									<span x-show="!sidebarCollapsed" x-transition class="whitespace-nowrap">{{ __('Settings') }}</span>
								</a>
							</nav>
						</div>

						<div class="pt-4 mt-6 border-t border-emerald-700/50 dark:border-emerald-800/50 space-y-2">
							<a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-emerald-100 hover:text-white hover:bg-emerald-700/60 dark:hover:bg-emerald-800/60 transition-all duration-200" :class="{ 'justify-center': sidebarCollapsed }" title="{{ __('Profile') }}">
								<svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5zm0 2c-4 0-8 2-8 4v2h16v-2c0-2-4-4-8-4z"/></svg>
								<span x-show="!sidebarCollapsed" x-transition class="whitespace-nowrap">{{ __('Profile') }}</span>
							</a>
							<form method="POST" action="{{ route('logout') }}">
								@csrf
								<button type="submit" class="w-full {{ ($isRtl ?? false) ? 'text-right' : 'text-left' }} flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-300 hover:text-white hover:bg-red-500/20 transition-all duration-200" :class="{ 'justify-center': sidebarCollapsed }" title="{{ __('Log Out') }}">
									<svg class="h-5 w-5 shrink-0 {{ ($isRtl ?? false) ? 'rtl-flip' : '' }}" viewBox="0 0 24 24" fill="currentColor"><path d="M16 13v-2H7V8l-5 4 5 4v-3zM20 3h-8a2 2 0 0 0-2 2v3h2V5h8v14h-8v-3h-2v3a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2z"/></svg>
									<span x-show="!sidebarCollapsed" x-transition class="whitespace-nowrap">{{ __('Log Out') }}</span>
								</button>
							</form>
						</div>
					</div>

					<!-- Close button for mobile -->
					<button @click="sidebarOpen = false" class="md:hidden absolute top-3 {{ ($isRtl ?? false) ? 'left-3' : 'right-3' }} p-3 rounded-xl text-white/80 hover:text-white hover:bg-white/10 active:bg-white/20 touch-manipulation transition-colors">
						<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
						</svg>
					</button>
				</aside>

				<!-- Overlay for mobile -->
				<div @click="sidebarOpen = false" x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-20 bg-black/40 md:hidden"></div>

			<!-- Content -->
			<main class="flex-1 min-h-screen min-w-0 overflow-x-hidden overflow-y-auto">
					<!-- Header slot if provided -->
					@isset($header)
						<header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
							<div class="px-4 py-4 sm:px-6 lg:px-8 flex items-center gap-4">
								<!-- Sidebar toggle button for desktop -->
								<button @click="toggleSidebar()" class="hidden md:flex p-2 rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" title="Toggle Sidebar">
									<svg x-show="!sidebarCollapsed" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
									</svg>
									<svg x-show="sidebarCollapsed" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
									</svg>
								</button>
								<div class="flex-1">
									{{ $header }}
								</div>
								<!-- Theme toggle for desktop -->
								<button onclick="toggleDarkMode()" class="hidden md:flex p-2 rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" title="Toggle Theme" id="desktopThemeBtn">
									<svg id="desktopThemeIcon" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
									</svg>
								</button>
								<!-- Language selector for desktop -->
								<div class="hidden md:flex items-center gap-2 relative" x-data="{ langOpen: false }" @click.outside="langOpen = false">
									<button @click="langOpen = !langOpen" class="flex items-center gap-2 px-3 py-2 rounded-md text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
										<span class="text-lg">{{ $supportedLocales[$currentLocale ?? 'en']['flag'] ?? '🌐' }}</span>
										<span class="hidden lg:inline">{{ $supportedLocales[$currentLocale ?? 'en']['native'] ?? 'English' }}</span>
										<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
										</svg>
									</button>
									<div x-show="langOpen" x-transition class="absolute top-full {{ ($isRtl ?? false) ? 'left-0' : 'right-0' }} mt-1 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50 min-w-[150px]">
										@foreach($supportedLocales ?? [] as $code => $locale)
											<a href="{{ request()->fullUrlWithQuery(['lang' => $code]) }}" class="flex items-center gap-3 px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 {{ ($currentLocale ?? 'en') === $code ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400' : 'text-gray-700 dark:text-gray-300' }}">
												<span class="text-lg">{{ $locale['flag'] }}</span>
												<span>{{ $locale['native'] }}</span>
											</a>
										@endforeach
									</div>
								</div>
								<!-- Country selector for desktop -->
								<div class="hidden md:flex items-center gap-2">
									<select onchange="window.location.href='?country=' + this.value" class="rounded border-gray-300 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
										@php
											$countries = \App\Models\Country::where('name', '!=', 'Global')->orderBy('name')->get();
											$currentCountryId = session('current_country_id');
										@endphp
										<option value="0" {{ $currentCountryId == 0 || !$currentCountryId ? 'selected' : '' }}>
											{{ __('Global') }}
										</option>
										@foreach($countries as $country)
											<option value="{{ $country->id }}" {{ $currentCountryId == $country->id ? 'selected' : '' }}>
												{{ $country->name }}
											</option>
										@endforeach
									</select>
								</div>
							</div>
						</header>
					@else
						<!-- If no header, add toggle button in a small header bar -->
						<div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
							<div class="px-4 py-3 sm:px-6 lg:px-8">
								<button @click="toggleSidebar()" class="hidden md:flex p-2 rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" title="Toggle Sidebar">
									<svg x-show="!sidebarCollapsed" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
									</svg>
									<svg x-show="sidebarCollapsed" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
									</svg>
								</button>
							</div>
						</div>
					@endisset

					<div class="p-4 sm:p-6 lg:p-8">
						{{ $slot }}
					</div>
				</main>
			</div>
		</div>
		<script>
			function toggleDarkMode() {
				const isDark = document.documentElement.classList.contains('dark');
				const mobileIcon = document.getElementById('mobileThemeIcon');
				const desktopIcon = document.getElementById('desktopThemeIcon');
				
				// SVG paths
				const moonPath = 'M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z';
				const sunPath = 'M12 3v1m0 16v1m9-9h-1m-16 0H1m15.364 1.636l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z';
				
				if (isDark) {
					// Switch to light mode
					document.documentElement.classList.remove('dark');
					localStorage.setItem('theme', 'light');
					if (mobileIcon) mobileIcon.querySelector('path').setAttribute('d', moonPath);
					if (desktopIcon) desktopIcon.querySelector('path').setAttribute('d', moonPath);
				} else {
					// Switch to dark mode
					document.documentElement.classList.add('dark');
					localStorage.setItem('theme', 'dark');
					if (mobileIcon) mobileIcon.querySelector('path').setAttribute('d', sunPath);
					if (desktopIcon) desktopIcon.querySelector('path').setAttribute('d', sunPath);
				}
			}
			
			// Initialize theme on page load
			document.addEventListener('DOMContentLoaded', function() {
				const savedTheme = localStorage.getItem('theme');
				const moonPath = 'M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z';
				const sunPath = 'M12 3v1m0 16v1m9-9h-1m-16 0H1m15.364 1.636l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z';
				const mobileIcon = document.getElementById('mobileThemeIcon');
				const desktopIcon = document.getElementById('desktopThemeIcon');
				
				if (document.documentElement.classList.contains('dark')) {
					if (mobileIcon) mobileIcon.querySelector('path').setAttribute('d', sunPath);
					if (desktopIcon) desktopIcon.querySelector('path').setAttribute('d', sunPath);
				} else {
					if (mobileIcon) mobileIcon.querySelector('path').setAttribute('d', moonPath);
					if (desktopIcon) desktopIcon.querySelector('path').setAttribute('d', moonPath);
				}
			});
		</script>
	</body>
	<!-- Alpine for sidebar toggling (already available via app.js if using Breeze bootstrap.js) -->
</html>


