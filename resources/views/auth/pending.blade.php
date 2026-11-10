<x-guest-layout>
	<div class="mb-6">
		<h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ __('Account pending approval') }}</h2>
		<p class="mt-2 text-gray-600 dark:text-gray-400">
			{{ __('Your account does not have access yet. An administrator needs to assign a role before you can use the app.') }}
		</p>
	</div>

	<form method="POST" action="{{ route('logout') }}">
		@csrf
		<button
			type="submit"
			class="inline-flex w-full items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
		>
			{{ __('Log out') }}
		</button>
	</form>
</x-guest-layout>
