<x-admin-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Sourcing') }}
		</h2>
	</x-slot>
	<div class="py-6">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
			<div class="flex items-center justify-between">
				<form method="GET" class="flex items-end gap-3">
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Search') }}</label>
						<input name="q" value="{{ $q }}" class="mt-1 w-64 rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100" placeholder="{{ __('Product name') }}">
					</div>
					<div>
						<button class="px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-800">{{ __('Filter') }}</button>
					</div>
				</form>
				<a href="{{ route('sourcings.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">{{ __('Add Sourcing') }}</a>
			</div>
			@if(session('status'))
				<div class="bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-300 px-4 py-3 rounded">
					{{ session('status') }}
				</div>
			@endif
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4">
				<div class="overflow-x-auto">
					<table class="min-w-full text-sm">
						<thead>
							<tr class="text-left text-gray-500">
								<th class="py-2">{{ __('Product Name') }}</th>
								<th class="py-2">{{ __('Category') }}</th>
								<th class="py-2">{{ __('Quantity') }}</th>
								<th class="py-2">{{ __('Unit Price') }}</th>
								<th class="py-2">{{ __('Price Total') }}</th>
								<th class="py-2">{{ __('Cost Total') }}</th>
								<th class="py-2">{{ __('Shipping Type') }}</th>
								<th class="py-2">{{ __('Supplier') }}</th>
								<th class="py-2">{{ __('Status') }}</th>
								<th class="py-2">{{ __('Sourcing Date') }}</th>
								<th class="py-2"></th>
							</tr>
						</thead>
						<tbody class="text-gray-900 dark:text-gray-100">
							@foreach($sourcings as $sourcing)
								<tr class="border-t border-gray-200 dark:border-gray-700">
									<td class="py-2">{{ $sourcing->product_name }}</td>
									<td class="py-2">{{ $sourcing->category->name ?? __('N/A') }}</td>
									<td class="py-2">{{ number_format($sourcing->quantity) }}</td>
									<td class="py-2">{{ number_format($sourcing->price, 2) }}</td>
									<td class="py-2">{{ number_format($sourcing->cost, 2) }}</td>
									<td class="py-2">{{ number_format($sourcing->cost_total, 2) }}</td>
									<td class="py-2">{{ $sourcing->shipping_type ? ucfirst(str_replace('_', ' ', $sourcing->shipping_type)) : __('N/A') }}</td>
									<td class="py-2">{{ $sourcing->supplier->name ?? __('N/A') }}</td>
									<td class="py-2">
										@if($sourcing->validated)
											<span class="px-2 py-1 text-xs bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-300 rounded">{{ __('Validated') }}</span>
										@else
											<span class="px-2 py-1 text-xs bg-yellow-100 dark:bg-yellow-800 text-yellow-700 dark:text-yellow-300 rounded">{{ __('Pending') }}</span>
										@endif
									</td>
									<td class="py-2">{{ $sourcing->sourcing_date ? $sourcing->sourcing_date->format('Y-m-d') : __('N/A') }}</td>
									<td class="py-2 text-right space-x-2">
										@if(!$sourcing->validated)
											<form action="{{ route('sourcings.validate', $sourcing) }}" method="POST" class="inline">
												@csrf
												<button type="submit" class="text-green-600 hover:underline" onclick="return confirm('{{ __('Validate this sourcing and create the product?') }}')">{{ __('Validate') }}</button>
											</form>
										@endif
										<a href="{{ route('sourcings.edit', $sourcing) }}" class="text-indigo-600 hover:underline">{{ __('Edit') }}</a>
										<form action="{{ route('sourcings.destroy', $sourcing) }}" method="POST" class="inline">
											@csrf @method('DELETE')
											<button type="submit" class="text-red-600 hover:underline" onclick="return confirm('{{ __('Delete this sourcing?') }}')">{{ __('Delete') }}</button>
										</form>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
				<div class="mt-4">{{ $sourcings->links() }}</div>
			</div>
		</div>
	</div>
</x-admin-layout>

