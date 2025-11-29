<x-guest-layout>
	<div class="mb-6">
		<h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ __('Welcome back') }}</h2>
		<p class="mt-1 text-gray-600 dark:text-gray-400">{{ __('Sign in to your account') }}</p>
	</div>

	@if (session('status'))
		<div class="mb-4 rounded-lg border border-emerald-300 bg-emerald-50 p-3 text-emerald-800 dark:border-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200">
			{{ session('status') }}
		</div>
	@endif

	@if ($errors->any())
		<div class="mb-4 rounded-lg border border-red-300 bg-red-50 p-3 text-red-800 dark:border-red-700 dark:bg-red-900/40 dark:text-red-200">
			{{ __('Please fix the errors below and try again.') }}
		</div>
	@endif

	<form method="POST" action="{{ route('login') }}" class="space-y-5">
		@csrf

		<div>
			<label for="email" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Email') }}</label>
			<input
				id="email"
				type="email"
				name="email"
				value="{{ old('email') }}"
				required
				autofocus
				autocomplete="username"
				class="block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500"
				placeholder="{{ __('you@example.com') }}"
			/>
			@error('email')
				<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
			@enderror
		</div>

		<div>
			<label for="password" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Password') }}</label>
			<input
				id="password"
				type="password"
				name="password"
				required
				autocomplete="current-password"
				class="block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500"
				placeholder="••••••••"
			/>
			@error('password')
				<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
			@enderror
		</div>

		<div class="flex items-center justify-between">
			<label for="remember_me" class="inline-flex items-center gap-2">
				<input id="remember_me" type="checkbox" name="remember" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
				<span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Remember me') }}</span>
			</label>
			@if (Route::has('password.request'))
				<a href="{{ route('password.request') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
					{{ __('Forgot password?') }}
				</a>
			@endif
		</div>

		<button
			type="submit"
			class="inline-flex w-full items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
		>
			{{ __('Sign in') }}
		</button>
	</form>

	@if (Route::has('register'))
		<p class="mt-6 text-center text-sm text-gray-600 dark:text-gray-400">
			{{ __("Don't have an account?") }}
			<a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">{{ __('Create one') }}</a>
		</p>
	@endif
</x-guest-layout>
