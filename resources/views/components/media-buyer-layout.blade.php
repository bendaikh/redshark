@props(['header' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="csrf-token" content="{{ csrf_token() }}">

		<title>{{ config('app.name', 'Laravel') }} — Media Buyer</title>

		<!-- Fonts -->
		<link rel="preconnect" href="https://fonts.bunny.net">
		<link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

		<!-- Scripts -->
		@vite(['resources/css/app.css', 'resources/js/app.js'])
	</head>
	<body class="font-sans antialiased" x-data="{ 
		sidebarOpen: false, 
		sidebarCollapsed: false, 
		darkMode: false,
		init() {
			// Load sidebar state from localStorage
			const saved = localStorage.getItem('mediaBuyerSidebarCollapsed');
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
			localStorage.setItem('mediaBuyerSidebarCollapsed', this.sidebarCollapsed);
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
			<div class="md:hidden flex items-center justify-between px-4 py-3 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
				<button @click="sidebarOpen = true" class="p-2 rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
					<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
					</svg>
				</button>
				<div class="flex items-center gap-3">
					<!-- Cube icon -->
					<svg class="h-6 w-6 text-blue-600" viewBox="0 0 24 24" fill="currentColor">
						<path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4a2 2 0 0 0 1-1.73zM12 3.84 18.74 8 12 12.16 5.26 8zm-7 6.32 6 3.6v6.4l-6-3.43zm8 10v-6.4l6-3.6v6.57z"/>
					</svg>
					<span class="text-sm font-semibold text-gray-800 dark:text-gray-200">Media Buyer</span>
				</div>
				<!-- Country selector for mobile -->
				<div class="flex items-center gap-2">
					<select onchange="window.location.href='?country=' + this.value" class="rounded border-gray-300 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
						@php
							$accessibleCountries = auth()->user()->getAccessibleCountries();
							$currentCountryId = session('current_country_id');
						@endphp
						<option value="0" {{ $currentCountryId == 0 || !$currentCountryId ? 'selected' : '' }}>
							All
						</option>
						@foreach($accessibleCountries as $country)
							<option value="{{ $country->id }}" {{ $currentCountryId == $country->id ? 'selected' : '' }}>
								{{ $country->name }}
							</option>
						@endforeach
					</select>
				</div>
				<!-- Theme toggle for mobile -->
				<button @click="toggleTheme()" class="p-2 rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" title="Toggle Theme">
					<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path x-show="!darkMode" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
						<path x-show="darkMode" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1m-16 0H1m15.364 1.636l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
					</svg>
				</button>
			</div>

			<div class="flex">
				<!-- Sidebar -->
				<aside
					class="fixed inset-y-0 left-0 z-30 w-72 transform transition-all duration-300 md:transform-none md:static bg-gradient-to-br from-blue-900 via-indigo-900 to-blue-800 dark:from-blue-950 dark:via-indigo-950 dark:to-blue-900 border-r border-blue-700/50 dark:border-blue-800/50 overflow-y-auto shadow-2xl"
					:class="{ 
						'-translate-x-full': !sidebarOpen,
						'md:w-72': !sidebarCollapsed,
						'md:w-20': sidebarCollapsed
					}"
					x-cloak
				>
					<div class="flex h-full flex-col justify-between p-4">
						<div>
							<div class="hidden md:flex items-center justify-center px-2 pb-6 border-b border-blue-700/50 dark:border-blue-800/50" :class="{ 'justify-start': !sidebarCollapsed }">
								<a href="{{ route('media-buyer.dashboard') }}" class="inline-flex items-center gap-2 group" :class="{ 'justify-center': sidebarCollapsed }">
									<svg class="h-7 w-7 text-blue-400 shrink-0 group-hover:text-blue-300 transition-colors" viewBox="0 0 24 24" fill="currentColor">
										<path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4a2 2 0 0 0 1-1.73zM12 3.84 18.74 8 12 12.16 5.26 8zm-7 6.32 6 3.6v6.4l-6-3.43zm8 10v-6.4l6-3.6v6.57z"/>
									</svg>
									<span class="text-lg font-bold text-white whitespace-nowrap group-hover:text-blue-300 transition-colors" x-show="!sidebarCollapsed" x-transition>Media Buyer</span>
								</a>
							</div>

							<nav class="space-y-2 pt-6">
								<!-- Dashboard Section -->
								<a href="{{ route('media-buyer.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('media-buyer.dashboard') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/50' : 'text-blue-100 hover:text-white hover:bg-blue-800/60 dark:hover:bg-blue-900/60' }}" :class="{ 'justify-center': sidebarCollapsed }" title="Dashboard">
									<svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
									<span x-show="!sidebarCollapsed" x-transition class="whitespace-nowrap">Dashboard</span>
								</a>
								<!-- Testing Section -->
								<a href="{{ route('media-buyer.testing') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('media-buyer.testing') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/50' : 'text-blue-100 hover:text-white hover:bg-blue-800/60 dark:hover:bg-blue-900/60' }}" :class="{ 'justify-center': sidebarCollapsed }" title="Testing">
									<svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM9 18H7v-5h2v5zm4 0h-2V6h2v12zm4 0h-2v-8h2v8z"/></svg>
									<span x-show="!sidebarCollapsed" x-transition class="whitespace-nowrap">Testing</span>
								</a>
								<!-- Products Section -->
								<a href="{{ route('media-buyer.products') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('media-buyer.products') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/50' : 'text-blue-100 hover:text-white hover:bg-blue-800/60 dark:hover:bg-blue-900/60' }}" :class="{ 'justify-center': sidebarCollapsed }" title="Products">
									<svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M20 6h-2.18c.11-.31.18-.65.18-1a2.996 2.996 0 0 0-5.5-1.65l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-5-2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM9 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm11 15H4v-2h16v2zm0-5H4V8h5.08L7 10.83 8.62 12 11 8.76l1-1.36 1 1.36L15.38 12 17 10.83 14.92 8H20v6z"/></svg>
									<span x-show="!sidebarCollapsed" x-transition class="whitespace-nowrap">Products</span>
								</a>
								<!-- Expenses Section -->
								<a href="{{ route('media-buyer.expenses') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('media-buyer.expenses*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/50' : 'text-blue-100 hover:text-white hover:bg-blue-800/60 dark:hover:bg-blue-900/60' }}" :class="{ 'justify-center': sidebarCollapsed }" title="Expenses">
									<svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.77-.45-2.34-.94-2.34-1.67 0-.84.79-1.43 2.1-1.43 1.38 0 1.9.66 1.94 1.64h1.71c-.05-1.34-.87-2.57-2.49-2.97V5H10.9v1.69c-1.51.32-2.72 1.3-2.72 2.81 0 1.79 1.49 2.69 3.66 3.21 1.95.46 2.34 1.15 2.34 1.87 0 .53-.39 1.39-2.1 1.39-1.6 0-2.23-.72-2.32-1.64H8.04c.1 1.7 1.36 2.66 2.86 2.97V19h2.34v-1.67c1.52-.29 2.72-1.16 2.73-2.77-.01-2.2-1.9-2.96-3.66-3.42z"/></svg>
									<span x-show="!sidebarCollapsed" x-transition class="whitespace-nowrap">Expenses</span>
								</a>
								<!-- Campaigns Section -->
								<a href="{{ route('media-buyer.campaigns') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('media-buyer.campaigns*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/50' : 'text-blue-100 hover:text-white hover:bg-blue-800/60 dark:hover:bg-blue-900/60' }}" :class="{ 'justify-center': sidebarCollapsed }" title="Ad Campaigns">
									<svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92s2.92-1.31 2.92-2.92c0-1.61-1.31-2.92-2.92-2.92zM18 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM6 13c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm12 7.02c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1z"/></svg>
									<span x-show="!sidebarCollapsed" x-transition class="whitespace-nowrap">Ad Campaigns</span>
								</a>
							</nav>
						</div>

						<div class="pt-4 mt-6 border-t border-blue-700/50 dark:border-blue-800/50 space-y-2">
							<a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:text-white hover:bg-blue-800/60 dark:hover:bg-blue-900/60 transition-all duration-200" :class="{ 'justify-center': sidebarCollapsed }" title="Profile">
								<svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5zm0 2c-4 0-8 2-8 4v2h16v-2c0-2-4-4-8-4z"/></svg>
								<span x-show="!sidebarCollapsed" x-transition class="whitespace-nowrap">Profile</span>
							</a>
							<form method="POST" action="{{ route('logout') }}">
								@csrf
								<button type="submit" class="w-full text-left flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-300 hover:text-white hover:bg-red-500/20 transition-all duration-200" :class="{ 'justify-center': sidebarCollapsed }" title="Logout">
									<svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M16 13v-2H7V8l-5 4 5 4v-3zM20 3h-8a2 2 0 0 0-2 2v3h2V5h8v14h-8v-3h-2v3a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2z"/></svg>
									<span x-show="!sidebarCollapsed" x-transition class="whitespace-nowrap">Logout</span>
								</button>
							</form>
						</div>
					</div>

					<!-- Close button for mobile -->
					<button @click="sidebarOpen = false" class="md:hidden absolute top-3 right-3 p-2 rounded-md text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700">
						<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
					</button>
				</aside>

				<!-- Overlay for mobile -->
				<div @click="sidebarOpen = false" x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-20 bg-black/40 md:hidden"></div>

				<!-- Content -->
				<main class="flex-1 min-h-screen">
					<!-- Header slot if provided -->
					@if(isset($header))
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
								<button @click="toggleTheme()" class="hidden md:flex p-2 rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" title="Toggle Theme">
									<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path x-show="!darkMode" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
										<path x-show="darkMode" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1m-16 0H1m15.364 1.636l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
									</svg>
								</button>
								<!-- Country selector for desktop -->
								<div class="hidden md:flex items-center gap-2">
									<select onchange="window.location.href='?country=' + this.value" class="rounded border-gray-300 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
										@php
											$accessibleCountries = auth()->user()->getAccessibleCountries();
											$currentCountryId = session('current_country_id');
										@endphp
										<option value="0" {{ $currentCountryId == 0 || !$currentCountryId ? 'selected' : '' }}>
											All
										</option>
										@foreach($accessibleCountries as $country)
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
					@endif

					<div class="p-4 sm:p-6 lg:p-8">
						{{ $slot }}
					</div>
				</main>
			</div>
		</div>
	</body>
</html>

