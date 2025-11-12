<x-admin-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Shipping Methods') }}
		</h2>
	</x-slot>
	<div class="py-6">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
			<div class="flex items-center justify-between mb-4">
				<div></div>
				<a href="{{ route('shipping-methods.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">{{ __('Create Shipping Method') }}</a>
			</div>
			@if(session('status'))
				<div class="bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-300 px-4 py-3 rounded">
					{{ session('status') }}
				</div>
			@endif
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Managed Shipping Methods') }}</h3>
				<div class="overflow-x-auto">
					<table class="min-w-full text-sm">
						<thead>
							<tr class="text-left text-gray-500">
								<th class="py-2">{{ __('Name') }}</th>
								<th class="py-2">{{ __('Description') }}</th>
								<th class="py-2">{{ __('Status') }}</th>
								<th class="py-2"></th>
							</tr>
						</thead>
						<tbody class="text-gray-900 dark:text-gray-100">
							@forelse($managedMethods as $method)
								<tr class="border-t border-gray-200 dark:border-gray-700">
									<td class="py-2 font-medium">{{ $method->name }}</td>
									<td class="py-2">{{ $method->description ?? __('N/A') }}</td>
									<td class="py-2">
										<span class="px-2 py-1 rounded text-xs {{ $method->active ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100' }}">
											{{ $method->active ? __('Active') : __('Inactive') }}
										</span>
									</td>
									<td class="py-2 text-right space-x-2">
										<a href="{{ route('shipping-methods.edit', $method) }}" class="text-indigo-600 hover:underline">{{ __('Edit') }}</a>
										<form action="{{ route('shipping-methods.destroy', $method) }}" method="POST" class="inline">
											@csrf @method('DELETE')
											<button type="submit" class="text-red-600 hover:underline" onclick="return confirm('{{ __('Delete this shipping method?') }}')">{{ __('Delete') }}</button>
										</form>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="4" class="py-4 text-center text-gray-500">{{ __('No shipping methods found') }}</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</x-admin-layout>

