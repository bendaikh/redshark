<x-admin-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Invoices') }}
		</h2>
	</x-slot>
	<div class="py-6">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4">
				<form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3">
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('From') }}</label>
						<input type="date" name="from" value="{{ $from }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100">
					</div>
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('To') }}</label>
						<input type="date" name="to" value="{{ $to }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100">
					</div>
					<div class="md:col-span-2">
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Country') }}</label>
						<select name="country_id" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100">
							<option value="">{{ __('All') }}</option>
							@foreach($countries as $c)
								<option value="{{ $c->id }}" {{ (string)$countryId === (string)$c->id ? 'selected' : '' }}>{{ $c->name }}</option>
							@endforeach
						</select>
					</div>
					<div class="flex items-end">
						<button class="px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-800">{{ __('Filter') }}</button>
					</div>
				</form>
			</div>
			<div class="flex items-center justify-end">
				<a href="{{ route('invoices.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">{{ __('Add Invoice') }}</a>
			</div>
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4">
				<div class="overflow-x-auto">
					<table class="min-w-full text-sm">
						<thead>
							<tr class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
								<th class="py-2">{{ __('Date Range') }}</th>
								<th class="py-2">{{ __('Country') }}</th>
								<th class="py-2">{{ __('Products') }}</th>
								<th class="py-2">{{ __('Total Revenue') }}</th>
								<th class="py-2">{{ __('Total Orders') }}</th>
								<th class="py-2">{{ __('Quantity Sold') }}</th>
								<th class="py-2 text-right">{{ __('Actions') }}</th>
							</tr>
						</thead>
						<tbody class="text-gray-900 dark:text-gray-100">
							@foreach($invoices as $inv)
								@php
									$totalRevenue = $inv->items->sum('revenue');
									$totalOrders = $inv->items->sum('total_orders');
									$quantitySold = $inv->items->sum('quantity_sold');
									$productsCount = $inv->items->count();
								@endphp
								<tr class="border-b border-gray-200 dark:border-gray-700">
									<td class="py-2">
										@if($inv->date_from || $inv->date_to)
											{{ $inv->date_from ? $inv->date_from->format('Y-m-d') : 'N/A' }} 
											@if($inv->date_from && $inv->date_to) - @endif
											{{ $inv->date_to ? $inv->date_to->format('Y-m-d') : 'N/A' }}
										@else
											<span class="text-gray-400">{{ __('Not set') }}</span>
										@endif
									</td>
									<td class="py-2">{{ $inv->country->name }}</td>
									<td class="py-2">
										<span class="px-2 py-1 rounded text-xs bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300">
											{{ $productsCount }} {{ __('product(s)') }}
										</span>
									</td>
									<td class="py-2 font-medium">{{ number_format($totalRevenue, 2) }}</td>
									<td class="py-2 font-medium">{{ number_format($totalOrders, 0) }}</td>
									<td class="py-2 font-medium">{{ number_format($quantitySold, 0) }}</td>
									<td class="py-2 text-right space-x-2">
										<a href="{{ route('invoices.edit', $inv) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ __('Edit') }}</a>
										<form action="{{ route('invoices.destroy', $inv) }}" method="POST" class="inline">
											@csrf @method('DELETE')
											<button type="submit" class="text-red-600 dark:text-red-400 hover:underline" onclick="return confirm('{{ __('Delete this invoice?') }}')">{{ __('Delete') }}</button>
										</form>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
				<div class="mt-4">{{ $invoices->links() }}</div>
			</div>
		</div>
	</div>
</x-admin-layout>

