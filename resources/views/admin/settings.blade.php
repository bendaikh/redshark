<x-admin-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-2xl text-gray-900 dark:text-gray-100 leading-tight">
			{{ __('Settings') }}
		</h2>
	</x-slot>

	<div class="space-y-6">
		<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
			<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Application Settings') }}</h3>
			<p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Configure your application settings here.') }}</p>
		</div>

		<!-- Quick add: Country -->
		<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
			<div class="flex items-center justify-between mb-4">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Add Country') }}</h3>
				<a href="{{ route('countries.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">{{ __('Manage Countries') }}</a>
			</div>

			<form method="POST" action="{{ route('countries.store') }}" class="space-y-4">
				@csrf
				<input type="hidden" name="redirect_to" value="settings">
				<div>
					<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Name') }}</label>
					<input name="name" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100" required>
				</div>

				<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Code') }}</label>
						<input name="code" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100" required placeholder="e.g. US">
					</div>
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Currency') }}</label>
						<input name="currency" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100" placeholder="e.g. USD">
					</div>
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Timezone') }}</label>
						<input name="timezone" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100" placeholder="e.g. America/New_York">
					</div>
				</div>

				<div class="flex items-center">
					<input type="checkbox" name="active" value="1" checked class="me-2">
					<label class="text-sm text-gray-600 dark:text-gray-300">{{ __('Active') }}</label>
				</div>

				<div class="pt-2">
					<button class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">{{ __('Add Country') }}</button>
				</div>
			</form>
		</div>

		<!-- Countries list -->
		<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
			<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Countries') }}</h3>

			@if(isset($countries) && $countries->count())
				<div class="overflow-x-auto">
					<table class="min-w-full text-sm">
						<thead>
							<tr class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
								<th class="py-2 pe-2">{{ __('Name') }}</th>
								<th class="py-2 pe-2">{{ __('Code') }}</th>
								<th class="py-2 pe-2">{{ __('Currency') }}</th>
								<th class="py-2 pe-2">{{ __('Active') }}</th>
								<th class="py-2 pe-2 text-right">{{ __('Actions') }}</th>
							</tr>
						</thead>
						<tbody class="text-gray-900 dark:text-gray-100">
							@foreach($countries as $c)
								<tr class="border-b border-gray-100 dark:border-gray-700/60">
									<td class="py-2 pe-2">{{ $c->name }}</td>
									<td class="py-2 pe-2">{{ $c->code }}</td>
									<td class="py-2 pe-2">{{ $c->currency }}</td>
									<td class="py-2 pe-2">
										<span class="px-2 py-1 rounded text-xs {{ $c->active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-200 text-gray-700' }}">{{ $c->active ? __('Yes') : __('No') }}</span>
									</td>
									<td class="py-2 pe-2">
										<form action="{{ route('countries.destroy', $c) }}" method="POST" class="inline float-right" onsubmit="return confirm('{{ __('Delete this country?') }}')">
											@csrf
											@method('DELETE')
											<input type="hidden" name="redirect_to" value="settings">
											<button type="submit" class="inline-flex items-center p-1.5 rounded hover:bg-red-50 dark:hover:bg-red-900/20 text-red-600 dark:text-red-400" title="{{ __('Delete') }}">
												<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
													<path fill-rule="evenodd" d="M9 3.75A2.25 2.25 0 0 1 11.25 1.5h1.5A2.25 2.25 0 0 1 15 3.75V4.5h3.75a.75.75 0 0 1 0 1.5h-.42l-1.07 13.367A2.25 2.25 0 0 1 15.02 22.5H8.98a2.25 2.25 0 0 1-2.241-2.133L5.67 6h-.42a.75.75 0 0 1 0-1.5H9V3.75Zm1.5.75h3V3.75a.75.75 0 0 0-.75-.75h-1.5a.75.75 0 0 0-.75.75V4.5ZM7.173 6l1.06 13.25a.75.75 0 0 0 .747.7h6.048a.75.75 0 0 0 .747-.7L16.827 6H7.173ZM9.75 8.25a.75.75 0 0 1 .75.75v8.25a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm4.5 0A.75.75 0 0 1 15 9v8.25a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Z" clip-rule="evenodd" />
												</svg>
												<span class="sr-only">{{ __('Delete') }}</span>
											</button>
										</form>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
				<div class="mt-4">{{ $countries->links() }}</div>
			@else
				<p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No countries yet.') }}</p>
			@endif
		</div>
	</div>
</x-admin-layout>

