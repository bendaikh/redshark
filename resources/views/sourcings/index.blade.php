<x-admin-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Sourcing') }}
		</h2>
	</x-slot>
	<div class="py-4 sm:py-6">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
			<!-- Header with Add button -->
			<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 hidden sm:block">{{ __('Sourcing') }}</h3>
				<a href="{{ route('sourcings.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 active:bg-indigo-800 transition-colors touch-manipulation w-full sm:w-auto">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
					{{ __('Add Sourcing') }}
				</a>
			</div>
			
			<!-- Filters -->
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl p-4">
				<form method="GET" class="space-y-3 sm:space-y-0 sm:flex sm:flex-wrap sm:items-end sm:gap-3">
					<div class="flex-1 min-w-0 sm:min-w-[200px] sm:max-w-xs">
						<label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1.5">{{ __('Search') }}</label>
						<input name="q" value="{{ $q }}" class="w-full py-2.5 px-3 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="{{ __('Product name') }}">
					</div>
					<div class="sm:w-48">
						<label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1.5">{{ __('Country') }}</label>
						<select name="country_id" class="w-full py-2.5 px-3 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
							<option value="">{{ __('All') }}</option>
							@foreach($countries as $c)
								<option value="{{ $c->id }}" {{ (string)$countryId === (string)$c->id ? 'selected' : '' }}>{{ $c->name }}</option>
							@endforeach
						</select>
					</div>
					<div class="sm:flex-shrink-0">
						<button class="w-full sm:w-auto px-6 py-2.5 bg-gray-800 dark:bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-900 dark:hover:bg-gray-500 active:bg-gray-950 transition-colors touch-manipulation">{{ __('Filter') }}</button>
					</div>
				</form>
			</div>
			
			@if(session('status'))
				<div class="bg-green-100 dark:bg-green-800/50 text-green-700 dark:text-green-300 px-4 py-3 rounded-xl flex items-center gap-3">
					<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
					{{ session('status') }}
				</div>
			@endif
			
			<!-- Table -->
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl">
				<div class="overflow-x-auto">
					<table class="min-w-full text-sm">
						<thead>
							<tr class="text-xs uppercase tracking-wider bg-gray-50 dark:bg-gray-900 text-gray-500 dark:text-gray-300">
								<th class="py-3 px-3 text-left whitespace-nowrap">{{ __('Product') }}</th>
								<th class="py-3 px-3 text-left whitespace-nowrap">{{ __('Category') }}</th>
								<th class="py-3 px-3 text-right whitespace-nowrap">{{ __('Qty') }}</th>
								<th class="py-3 px-3 text-right whitespace-nowrap">{{ __('Unit Price') }}</th>
								<th class="py-3 px-3 text-right whitespace-nowrap">{{ __('Final Total') }}</th>
								<th class="py-3 px-3 text-right whitespace-nowrap">{{ __('Cost Total') }}</th>
								<th class="py-3 px-3 text-left whitespace-nowrap">{{ __('Shipping') }}</th>
								<th class="py-3 px-3 text-left whitespace-nowrap">{{ __('Supplier') }}</th>
								<th class="py-3 px-3 text-center whitespace-nowrap">{{ __('Status') }}</th>
								<th class="py-3 px-3 text-left whitespace-nowrap">{{ __('Date') }}</th>
								<th class="py-3 px-3 text-center whitespace-nowrap">{{ __('Actions') }}</th>
							</tr>
						</thead>
						<tbody class="text-gray-700 dark:text-gray-200 divide-y divide-gray-100 dark:divide-gray-700">
							@foreach($sourcings as $sourcing)
								<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
									<td class="py-3 px-3 font-medium whitespace-nowrap">{{ $sourcing->product_name }}</td>
									<td class="py-3 px-3 whitespace-nowrap">{{ $sourcing->category->name ?? '-' }}</td>
									<td class="py-3 px-3 text-right tabular-nums">{{ number_format($sourcing->quantity) }}</td>
									<td class="py-3 px-3 text-right tabular-nums">{{ number_format($sourcing->price, 2) }}</td>
									<td class="py-3 px-3 text-right tabular-nums">{{ number_format($sourcing->final_price_total, 2) }}</td>
									<td class="py-3 px-3 text-right tabular-nums font-medium">{{ number_format($sourcing->cost_total, 2) }}</td>
									<td class="py-3 px-3 whitespace-nowrap">{{ $sourcing->shipping_type ? ucfirst(str_replace('_', ' ', $sourcing->shipping_type)) : '-' }}</td>
									<td class="py-3 px-3 whitespace-nowrap">{{ $sourcing->supplier->name ?? '-' }}</td>
									<td class="py-3 px-3 text-center">
										@if($sourcing->validated)
											<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300">{{ __('Validated') }}</span>
										@else
											<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/50 text-yellow-700 dark:text-yellow-300">{{ __('Pending') }}</span>
										@endif
									</td>
									<td class="py-3 px-3 whitespace-nowrap">{{ $sourcing->sourcing_date ? $sourcing->sourcing_date->format('M d, Y') : '-' }}</td>
									<td class="py-3 px-3">
										<div class="flex items-center justify-center gap-1">
											@if(!$sourcing->validated)
												<form action="{{ route('sourcings.validate', $sourcing) }}" method="POST" class="inline">
													@csrf
													<button type="submit" class="p-2 text-green-600 hover:text-green-800 hover:bg-green-100 dark:hover:bg-green-900/30 rounded-lg transition-colors touch-manipulation" title="{{ __('Validate') }}" onclick="return confirm('{{ __('Validate this sourcing and create the product?') }}')">
														<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
														</svg>
													</button>
												</form>
											@endif
											<a href="{{ route('sourcings.edit', $sourcing) }}" class="p-2 text-indigo-600 hover:text-indigo-800 hover:bg-indigo-100 dark:hover:bg-indigo-900/30 rounded-lg transition-colors touch-manipulation" title="{{ __('Edit') }}">
												<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
												</svg>
											</a>
											<form action="{{ route('sourcings.destroy', $sourcing) }}" method="POST" class="inline">
												@csrf @method('DELETE')
												<button type="submit" class="p-2 text-red-600 hover:text-red-800 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg transition-colors touch-manipulation" title="{{ __('Delete') }}" onclick="return confirm('{{ __('Delete this sourcing?') }}')">
													<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
				<div class="mt-4 px-3 pb-3 sm:px-0 sm:pb-0">{{ $sourcings->links() }}</div>
			</div>
		</div>
	</div>
</x-admin-layout>
