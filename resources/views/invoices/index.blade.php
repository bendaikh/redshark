<x-admin-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Invoices') }}
		</h2>
	</x-slot>
	<div class="py-4 sm:py-6">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
			<!-- Header with Add button -->
			<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 hidden sm:block">{{ __('Invoices') }}</h3>
				<a href="{{ route('invoices.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 active:bg-indigo-800 transition-colors touch-manipulation w-full sm:w-auto">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
					{{ __('Add Invoice') }}
				</a>
			</div>
			
			<!-- Filters -->
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl p-4">
				<form method="GET" class="space-y-3 sm:space-y-0 sm:grid sm:grid-cols-2 md:grid-cols-5 sm:gap-3">
					<div>
						<label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1.5">{{ __('From') }}</label>
						<input type="date" name="from" value="{{ $from }}" class="w-full py-2.5 px-3 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
					</div>
					<div>
						<label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1.5">{{ __('To') }}</label>
						<input type="date" name="to" value="{{ $to }}" class="w-full py-2.5 px-3 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
					</div>
					<div class="md:col-span-2">
						<label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1.5">{{ __('Country') }}</label>
						<select name="country_id" class="w-full py-2.5 px-3 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
							<option value="">{{ __('All') }}</option>
							@foreach($countries as $c)
								<option value="{{ $c->id }}" {{ (string)$countryId === (string)$c->id ? 'selected' : '' }}>{{ $c->name }}</option>
							@endforeach
						</select>
					</div>
					<div class="flex items-end">
						<button class="w-full sm:w-auto px-6 py-2.5 bg-gray-800 dark:bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-900 dark:hover:bg-gray-500 active:bg-gray-950 transition-colors touch-manipulation">{{ __('Filter') }}</button>
					</div>
				</form>
			</div>
			
			<!-- Table -->
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl">
				<div class="overflow-x-auto">
					<table class="min-w-full text-sm">
						<thead>
							<tr class="text-xs uppercase tracking-wider bg-gray-50 dark:bg-gray-900 text-gray-500 dark:text-gray-300">
								<th class="py-3 px-3 text-left whitespace-nowrap">{{ __('Date Range') }}</th>
								<th class="py-3 px-3 text-left whitespace-nowrap">{{ __('Country') }}</th>
								<th class="py-3 px-3 text-left whitespace-nowrap">{{ __('Products') }}</th>
								<th class="py-3 px-3 text-right whitespace-nowrap">{{ __('Revenue') }}</th>
								<th class="py-3 px-3 text-right whitespace-nowrap">{{ __('Orders') }}</th>
								<th class="py-3 px-3 text-right whitespace-nowrap">{{ __('Qty Sold') }}</th>
								<th class="py-3 px-3 text-center whitespace-nowrap">{{ __('Actions') }}</th>
							</tr>
						</thead>
						<tbody class="text-gray-700 dark:text-gray-200 divide-y divide-gray-100 dark:divide-gray-700">
							@foreach($invoices as $inv)
								@php
									$totalRevenue = $inv->items->sum('revenue');
									$totalOrders = $inv->items->sum('total_orders');
									$quantitySold = $inv->items->sum('quantity_sold');
									$productsCount = $inv->items->count();
								@endphp
								<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
									<td class="py-3 px-3 whitespace-nowrap">
										@if($inv->date_from || $inv->date_to)
											<span class="text-sm">{{ $inv->date_from ? $inv->date_from->format('M d') : '?' }}</span>
											<span class="text-gray-400 mx-1">→</span>
											<span class="text-sm">{{ $inv->date_to ? $inv->date_to->format('M d, Y') : '?' }}</span>
										@else
											<span class="text-gray-400 text-sm">{{ __('Not set') }}</span>
										@endif
									</td>
									<td class="py-3 px-3 whitespace-nowrap">{{ $inv->country->name }}</td>
									<td class="py-3 px-3">
										<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300">
											{{ $productsCount }} {{ __('product(s)') }}
										</span>
									</td>
									<td class="py-3 px-3 text-right font-medium tabular-nums">{{ number_format($totalRevenue, 2) }}</td>
									<td class="py-3 px-3 text-right tabular-nums">{{ number_format($totalOrders, 0) }}</td>
									<td class="py-3 px-3 text-right tabular-nums">{{ number_format($quantitySold, 0) }}</td>
									<td class="py-3 px-3">
										<div class="flex items-center justify-center gap-1">
											<a href="{{ route('invoices.edit', $inv) }}" class="p-2 text-indigo-600 hover:text-indigo-800 hover:bg-indigo-100 dark:hover:bg-indigo-900/30 rounded-lg transition-colors touch-manipulation" title="{{ __('Edit') }}">
												<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
												</svg>
											</a>
											<form action="{{ route('invoices.destroy', $inv) }}" method="POST" class="inline">
												@csrf @method('DELETE')
												<button type="submit" class="p-2 text-red-600 hover:text-red-800 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg transition-colors touch-manipulation" onclick="return confirm('{{ __('Delete this invoice?') }}')" title="{{ __('Delete') }}">
													<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
													</svg>
												</button>
											</form>
										</div>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
				<div class="mt-4 px-3 pb-3 sm:px-0 sm:pb-0">{{ $invoices->links() }}</div>
			</div>
		</div>
	</div>
</x-admin-layout>
