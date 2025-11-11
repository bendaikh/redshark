<x-admin-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Stock') }}
		</h2>
	</x-slot>
	<div class="py-6">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
			<div class="flex items-center justify-between">
				<form method="GET" class="flex items-end gap-3">
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Search') }}</label>
						<input name="q" value="{{ $q }}" class="mt-1 w-64 rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100" placeholder="{{ __('Name or category') }}">
					</div>
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Country') }}</label>
						<select name="country_id" class="mt-1 w-56 rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100">
							<option value="">{{ __('All') }}</option>
							@foreach($countries as $c)
								<option value="{{ $c->id }}" {{ (string)$countryId === (string)$c->id ? 'selected' : '' }}>{{ $c->name }}</option>
							@endforeach
						</select>
					</div>
					<div>
						<button class="px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-800">{{ __('Filter') }}</button>
					</div>
				</form>
				<a href="{{ route('products.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">{{ __('Add Product') }}</a>
			</div>
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4">
				<div class="overflow-x-auto">
					<table class="min-w-full text-sm">
						<thead>
							<tr class="text-left text-gray-500">
								<th class="py-2">{{ __('Name') }}</th>
								<th class="py-2">{{ __('Category') }}</th>
								<th class="py-2">{{ __('Qty') }}</th>
								<th class="py-2">{{ __('Cost') }}</th>
								<th class="py-2">{{ __('Sell Price') }}</th>
								<th class="py-2">{{ __('Supplier') }}</th>
								<th class="py-2">{{ __('Country') }}</th>
								<th class="py-2"></th>
							</tr>
						</thead>
						<tbody class="text-gray-900 dark:text-gray-100">
							@foreach($products as $p)
								<tr class="border-t border-gray-200 dark:border-gray-700">
									<td class="py-2">{{ $p->name }}</td>
									<td class="py-2">{{ $p->category }}</td>
									<td class="py-2">{{ $p->quantity }}</td>
									<td class="py-2">{{ number_format($p->cost, 2) }}</td>
									<td class="py-2">{{ number_format($p->selling_price, 2) }}</td>
									<td class="py-2">{{ $p->supplier?->name }}</td>
									<td class="py-2">{{ $p->country?->name }}</td>
									<td class="py-2 text-right space-x-2">
										<a href="{{ route('products.edit', $p) }}" class="text-indigo-600 hover:underline">{{ __('Edit') }}</a>
										<form action="{{ route('products.destroy', $p) }}" method="POST" class="inline">
											@csrf @method('DELETE')
											<button type="submit" class="text-red-600 hover:underline" onclick="return confirm('{{ __('Delete this product?') }}')">{{ __('Delete') }}</button>
										</form>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
				<div class="mt-4">{{ $products->links() }}</div>
			</div>
		</div>
	</div>
</x-admin-layout>

