<x-admin-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Stock') }}
		</h2>
	</x-slot>

	<!-- Load ApexCharts immediately -->
	<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
	<div class="py-4 sm:py-6">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
			<!-- Header with Add button -->
			<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 hidden sm:block">{{ __('Products') }}</h3>
				<a href="{{ route('products.create') }}" class="inline-flex items-center justify-center gap-2.5 px-5 py-3 bg-indigo-600 text-white text-base font-semibold rounded-xl hover:bg-indigo-700 active:bg-indigo-800 transition-colors touch-manipulation w-full sm:w-auto shadow-lg shadow-indigo-500/25">
					<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
					<span>{{ __('Add Product') }}</span>
				</a>
			</div>
			
			<!-- Filters -->
			<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
				<form method="GET" class="space-y-3 sm:space-y-0 sm:flex sm:flex-wrap sm:items-end sm:gap-3">
					<div class="flex-1 min-w-0 sm:min-w-[200px] sm:max-w-xs">
						<label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1.5">{{ __('Search') }}</label>
						<input name="q" value="{{ $q }}" class="w-full py-2.5 px-3 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="{{ __('Name or category') }}">
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
					<div class="sm:w-40">
						<label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1.5">{{ __('Date From') }}</label>
						<input type="date" name="date_from" value="{{ $dateFrom ?? '' }}" class="w-full py-2.5 px-3 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:[color-scheme:dark]">
					</div>
					<div class="sm:w-40">
						<label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1.5">{{ __('Date To') }}</label>
						<input type="date" name="date_to" value="{{ $dateTo ?? '' }}" class="w-full py-2.5 px-3 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:[color-scheme:dark]">
					</div>
					<div class="sm:flex-shrink-0 flex gap-2">
						<button class="flex-1 sm:flex-none px-6 py-3 bg-gray-800 dark:bg-gray-600 text-white text-base font-semibold rounded-xl hover:bg-gray-900 dark:hover:bg-gray-500 active:bg-gray-950 transition-colors touch-manipulation shadow-lg shadow-gray-500/25">{{ __('Filter') }}</button>
						@if($dateFrom || $dateTo || $q || $countryId)
							<a href="{{ route('products.index') }}" class="px-4 py-3 text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors">{{ __('Clear') }}</a>
						@endif
					</div>
				</form>
				@if($dateFrom || $dateTo)
					<div class="mt-3 p-2 bg-indigo-50 dark:bg-indigo-900/20 border-l-4 border-indigo-500 rounded text-sm text-indigo-800 dark:text-indigo-300">
						{{ __('Date filter applied:') }} 
						<span class="font-semibold">
							{{ $dateFrom ? \Carbon\Carbon::parse($dateFrom)->format('M d, Y') : __('Start') }} 
							— 
							{{ $dateTo ? \Carbon\Carbon::parse($dateTo)->format('M d, Y') : __('End') }}
						</span>
						<span class="text-xs ml-2">({{ __('Affects: Delivery %, Ads Cost, CPL, CPD, Net Profit') }})</span>
					</div>
				@endif
			</div>
		<!-- Mobile Card Layout -->
		<div class="block lg:hidden space-y-3">
			@foreach($products as $p)
				@php
					$hasDateFilter = !empty($dateFrom) || !empty($dateTo);
					$deliveryRate = $hasDateFilter ? $p->getFilteredDeliveryRate($dateFrom, $dateTo) : $p->delivery_rate;
					$adsCost = $hasDateFilter ? $p->getFilteredAdsCost($dateFrom, $dateTo) : $p->total_ads_cost;
					$cpl = $hasDateFilter ? $p->getFilteredCostPerLead($dateFrom, $dateTo) : $p->cost_per_lead;
					$cpd = $hasDateFilter ? $p->getFilteredCostPerDelivered($dateFrom, $dateTo) : $p->cost_per_delivered;
					$netProfit = $hasDateFilter ? $p->getFilteredNetProfit($dateFrom, $dateTo) : $p->net_profit;
					
					$cardBorder = 'border-l-4 border-gray-300';
					if ($deliveryRate >= 20) {
						$cardBorder = 'border-l-4 border-green-500';
					} elseif ($deliveryRate >= 15 && $deliveryRate <= 19) {
						$cardBorder = 'border-l-4 border-orange-500';
					} elseif ($deliveryRate <= 14) {
						$cardBorder = 'border-l-4 border-red-500';
					}
				@endphp
				<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm {{ $cardBorder }} overflow-hidden">
					<!-- Card Header -->
					<div class="p-4 flex items-start gap-3">
						@if($p->image)
							<img src="{{ \Illuminate\Support\Facades\Storage::url($p->image) }}" alt="{{ $p->name }}" class="h-14 w-14 object-cover rounded-lg flex-shrink-0">
						@else
							<div class="h-14 w-14 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center flex-shrink-0">
								<svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
							</div>
						@endif
						<div class="flex-1 min-w-0">
							<h4 class="text-base font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $p->name }}</h4>
							<div class="flex flex-wrap items-center gap-2 mt-1 text-sm text-gray-500 dark:text-gray-400">
								<span>{{ $p->category?->name ?? '-' }}</span>
								<span class="text-gray-300 dark:text-gray-600">•</span>
								<span>{{ $p->country?->name ?? '-' }}</span>
							</div>
						</div>
					</div>
					
					<!-- Stats Grid -->
					<div class="px-4 pb-3 grid grid-cols-3 gap-3">
						<div class="text-center p-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
							<p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Qty') }}</p>
							<p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $p->remaining_qty }}/{{ $p->initial_qty }}</p>
						</div>
						<div class="text-center p-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
							<p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Delivery') }}</p>
							<p class="text-sm font-semibold {{ $deliveryRate >= 20 ? 'text-green-600' : ($deliveryRate >= 15 ? 'text-orange-600' : 'text-red-600') }}">{{ number_format($deliveryRate, 1) }}%</p>
						</div>
						<div class="text-center p-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
							<p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Profit') }}</p>
							<p class="text-sm font-semibold {{ $netProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($netProfit, 2) }}</p>
						</div>
					</div>
					
					<!-- Expandable Details -->
					<div x-data="{ expanded: false }">
						<button @click="expanded = !expanded" class="w-full px-4 py-2 text-sm text-indigo-600 dark:text-indigo-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 flex items-center justify-center gap-1 transition-colors">
							<span x-text="expanded ? '{{ __('Less details') }}' : '{{ __('More details') }}'"></span>
							<svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
						</button>
						<div x-show="expanded" x-collapse class="px-4 pb-4 space-y-2 text-sm border-t border-gray-100 dark:border-gray-700">
							<div class="flex justify-between pt-3">
								<span class="text-gray-500 dark:text-gray-400">{{ __('Cost Total') }}</span>
								<span class="font-medium text-gray-900 dark:text-gray-100">{{ number_format($p->average_cost, 2) }}</span>
							</div>
							<div class="flex justify-between">
								<span class="text-gray-500 dark:text-gray-400">{{ __('Ads Cost') }}</span>
								<span class="font-medium text-gray-900 dark:text-gray-100">{{ number_format($adsCost, 2) }}</span>
							</div>
							<div class="flex justify-between">
								<span class="text-gray-500 dark:text-gray-400">{{ __('CPL') }}</span>
								<span class="font-medium text-gray-900 dark:text-gray-100">{{ number_format($cpl, 2) }}</span>
							</div>
							<div class="flex justify-between">
								<span class="text-gray-500 dark:text-gray-400">{{ __('CPD') }}</span>
								<span class="font-medium text-gray-900 dark:text-gray-100">{{ number_format($cpd, 2) }}</span>
							</div>
						</div>
					</div>
					
					<!-- Actions -->
					<div class="flex items-center justify-around border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 px-2 py-2">
						<button @click="window.loadStatistics({{ $p->id }})" class="flex-1 flex flex-col items-center gap-1 p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors touch-manipulation">
							<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
							</svg>
							<span class="text-xs">{{ __('Stats') }}</span>
						</button>
						<a href="{{ route('products.edit', $p) }}" class="flex-1 flex flex-col items-center gap-1 p-2 text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-lg transition-colors touch-manipulation">
							<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
							</svg>
							<span class="text-xs">{{ __('Edit') }}</span>
						</a>
						<a href="{{ route('products.assign-media-buyers', $p) }}" class="flex-1 flex flex-col items-center gap-1 p-2 text-green-600 hover:bg-green-50 dark:hover:bg-green-900/30 rounded-lg transition-colors touch-manipulation">
							<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
							</svg>
							<span class="text-xs">{{ __('Assign') }}</span>
						</a>
						<form action="{{ route('products.destroy', $p) }}" method="POST" class="flex-1" onsubmit="return confirm('{{ __('Delete this product?') }}')">
							@csrf @method('DELETE')
							<button type="submit" class="w-full flex flex-col items-center gap-1 p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors touch-manipulation">
								<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
								</svg>
								<span class="text-xs">{{ __('Delete') }}</span>
							</button>
						</form>
					</div>
				</div>
			@endforeach
			<div class="mt-4 px-1">{{ $products->links() }}</div>
		</div>

		<!-- Desktop Table Layout -->
		<div class="hidden lg:block bg-white dark:bg-gray-800 shadow-sm rounded-xl">
			<div class="overflow-x-auto">
				<table class="min-w-full text-sm">
					<thead>
						<tr class="text-xs uppercase tracking-wider bg-gray-50 dark:bg-gray-900 text-gray-500 dark:text-gray-300">
							<th class="py-3 px-3 text-left whitespace-nowrap">{{ __('Image') }}</th>
							<th class="py-3 px-3 text-left whitespace-nowrap">{{ __('Name') }}</th>
							<th class="py-3 px-3 text-left whitespace-nowrap">{{ __('Category') }}</th>
							<th class="py-3 px-3 text-right whitespace-nowrap">{{ __('Initial Qty') }}</th>
							<th class="py-3 px-3 text-right whitespace-nowrap">{{ __('Remaining') }}</th>
							<th class="py-3 px-3 text-right whitespace-nowrap">{{ __('Cost Total') }}</th>
							<th class="py-3 px-3 text-left whitespace-nowrap">{{ __('Country') }}</th>
							<th class="py-3 px-3 text-right whitespace-nowrap">{{ __('Delivery %') }}</th>
							<th class="py-3 px-3 text-right whitespace-nowrap">{{ __('Ads Cost') }}</th>
							<th class="py-3 px-3 text-right whitespace-nowrap">{{ __('CPL') }}</th>
							<th class="py-3 px-3 text-right whitespace-nowrap">{{ __('CPD') }}</th>
							<th class="py-3 px-3 text-right whitespace-nowrap">{{ __('Net Profit') }}</th>
							<th class="py-3 px-3 text-center whitespace-nowrap">{{ __('Actions') }}</th>
						</tr>
					</thead>
					<tbody class="text-gray-700 dark:text-gray-200 text-sm divide-y divide-gray-100 dark:divide-gray-700">
						@foreach($products as $p)
							@php
								$hasDateFilter = !empty($dateFrom) || !empty($dateTo);
								$deliveryRate = $hasDateFilter ? $p->getFilteredDeliveryRate($dateFrom, $dateTo) : $p->delivery_rate;
								$adsCost = $hasDateFilter ? $p->getFilteredAdsCost($dateFrom, $dateTo) : $p->total_ads_cost;
								$cpl = $hasDateFilter ? $p->getFilteredCostPerLead($dateFrom, $dateTo) : $p->cost_per_lead;
								$cpd = $hasDateFilter ? $p->getFilteredCostPerDelivered($dateFrom, $dateTo) : $p->cost_per_delivered;
								$netProfit = $hasDateFilter ? $p->getFilteredNetProfit($dateFrom, $dateTo) : $p->net_profit;
								
								$bgColor = 'bg-white dark:bg-gray-800';
								if ($deliveryRate >= 20) {
									$bgColor = 'bg-green-50 dark:bg-green-900/30';
								} elseif ($deliveryRate >= 15 && $deliveryRate <= 19) {
									$bgColor = 'bg-orange-50 dark:bg-orange-900/30';
								} elseif ($deliveryRate <= 14) {
									$bgColor = 'bg-red-50 dark:bg-red-900/30';
								}
							@endphp
							<tr class="{{ $bgColor }} hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
								<td class="py-3 px-3">
									@if($p->image)
										<img src="{{ \Illuminate\Support\Facades\Storage::url($p->image) }}" alt="{{ $p->name }}" class="h-10 w-10 object-cover rounded-lg">
									@else
										<div class="h-10 w-10 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
											<svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
										</div>
									@endif
								</td>
								<td class="py-3 px-3 font-medium whitespace-nowrap">{{ $p->name }}</td>
								<td class="py-3 px-3 whitespace-nowrap">{{ $p->category?->name ?? '-' }}</td>
								<td class="py-3 px-3 text-right tabular-nums">{{ $p->initial_qty }}</td>
								<td class="py-3 px-3 text-right tabular-nums">{{ $p->remaining_qty }}</td>
								<td class="py-3 px-3 text-right tabular-nums">{{ number_format($p->average_cost, 2) }}</td>
								<td class="py-3 px-3 whitespace-nowrap">{{ $p->country?->name ?? '-' }}</td>
								<td class="py-3 px-3 text-right tabular-nums font-medium">{{ number_format($deliveryRate, 2) }}%</td>
								<td class="py-3 px-3 text-right tabular-nums">{{ number_format($adsCost, 2) }}</td>
								<td class="py-3 px-3 text-right tabular-nums">{{ number_format($cpl, 2) }}</td>
								<td class="py-3 px-3 text-right tabular-nums">{{ number_format($cpd, 2) }}</td>
								<td class="py-3 px-3 text-right tabular-nums font-medium {{ $netProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($netProfit, 2) }}</td>
								<td class="py-3 px-3">
									<div class="flex items-center justify-center gap-1">
										<button @click="window.loadStatistics({{ $p->id }})" class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-lg transition-colors touch-manipulation" title="{{ __('Statistics') }}">
											<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
											</svg>
										</button>
										<a href="{{ route('products.edit', $p) }}" class="p-2 text-indigo-600 hover:text-indigo-800 hover:bg-indigo-100 dark:hover:bg-indigo-900/30 rounded-lg transition-colors touch-manipulation" title="{{ __('Edit') }}">
											<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
											</svg>
										</a>
										<a href="{{ route('products.assign-media-buyers', $p) }}" class="p-2 text-green-600 hover:text-green-800 hover:bg-green-100 dark:hover:bg-green-900/30 rounded-lg transition-colors touch-manipulation" title="{{ __('Assign') }}">
											<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
											</svg>
										</a>
										<form action="{{ route('products.destroy', $p) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Delete this product?') }}')">
											@csrf @method('DELETE')
											<button type="submit" class="p-2 text-red-600 hover:text-red-800 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg transition-colors touch-manipulation" title="{{ __('Delete') }}">
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
			<div class="mt-4 px-3 pb-4">{{ $products->links() }}</div>
		</div>
		</div>
	</div>

	<script>
		// Define function immediately so it's available when Alpine processes the page
		window.loadStatistics = function(productId) {
			if (!window.statsModal) {
				// Wait a bit for Alpine to initialize
				setTimeout(() => window.loadStatistics(productId), 100);
				return;
			}
			
			const modal = window.statsModal;
			modal.showStatsModal = true;
			modal.loading = true;
			modal.statsData = null;

			fetch(`/admin/products/${productId}/statistics`)
				.then(response => response.json())
				.then(data => {
					modal.statsData = data;
					modal.loading = false;
				})
				.catch(error => {
					console.error('Error loading statistics:', error);
					modal.loading = false;
					alert('{{ __('Error loading statistics') }}');
				});
		};

		// Define renderCharts function immediately (will call renderChartsFull when ApexCharts loads)
		window.renderCharts = function(data) {
			if (window.renderChartsFull) {
				window.renderChartsFull(data);
			} else {
				// Wait for renderChartsFull to be defined
				setTimeout(() => window.renderCharts(data), 100);
			}
		};

		// Full implementation of renderCharts (ApexCharts is now loaded)
		window.renderChartsFull = function(data) {
			console.log('renderCharts called with data:', data);
			
			if (!data || !data.product) {
				console.error('Invalid data for charts');
				return;
			}

			if (typeof ApexCharts === 'undefined') {
				console.error('ApexCharts is not loaded');
				return;
			}

			const productId = data.product.id;
			const isDark = document.documentElement.classList.contains('dark');

			// Clear any existing charts first
			const chartContainers = [
				`#ordersChart-${productId}`,
				`#revenueChart-${productId}`,
				`#adsChart-${productId}`,
				`#leadsChart-${productId}`
			];
			
			chartContainers.forEach(selector => {
				const el = document.querySelector(selector);
				if (el) {
					el.innerHTML = ''; // Clear previous content
				} else {
					console.warn('Chart container not found:', selector);
				}
			});

			// Orders Chart
			const invoiceItems = data.invoiceItems || [];
			console.log('Invoice items:', invoiceItems);
			const ordersDates = invoiceItems.length > 0 ? invoiceItems.map(item => item.date) : ['No Data'];
			const ordersData = invoiceItems.length > 0 ? invoiceItems.map(item => parseInt(item.orders) || 0) : [0];
			const soldData = invoiceItems.length > 0 ? invoiceItems.map(item => parseInt(item.sold) || 0) : [0];

			const ordersChartEl = document.querySelector(`#ordersChart-${productId}`);
			console.log('Orders chart element:', ordersChartEl);
			if (ordersChartEl) {
				try {
					const ordersChart = new ApexCharts(ordersChartEl, {
						series: [{
							name: '{{ __('Orders') }}',
							data: ordersData
						}, {
							name: '{{ __('Quantity Sold') }}',
							data: soldData
						}],
						chart: { type: 'line', height: 250, toolbar: { show: false } },
						stroke: { curve: 'smooth', width: 2 },
						xaxis: { categories: ordersDates },
						colors: ['#F58220', '#10b981'],
						theme: { mode: isDark ? 'dark' : 'light' }
					});
					ordersChart.render();
					console.log('Orders chart rendered');
				} catch (error) {
					console.error('Error rendering orders chart:', error);
				}
			} else {
				console.error('Orders chart element not found for product:', productId);
			}

			// Revenue Chart
			const revenueData = invoiceItems.length > 0 ? invoiceItems.map(item => parseFloat(item.revenue) || 0) : [0];
			const revenueChartEl = document.querySelector(`#revenueChart-${productId}`);
			console.log('Revenue chart element:', revenueChartEl);
			if (revenueChartEl) {
				try {
					const revenueChart = new ApexCharts(revenueChartEl, {
						series: [{
							name: '{{ __('Revenue') }}',
							data: revenueData
						}],
						chart: { type: 'area', height: 250, toolbar: { show: false } },
						stroke: { curve: 'smooth', width: 2 },
						xaxis: { categories: ordersDates },
						yaxis: { labels: { formatter: (value) => value.toFixed(2) } },
						dataLabels: { enabled: false },
						colors: ['#8b5cf6'],
						theme: { mode: isDark ? 'dark' : 'light' }
					});
					revenueChart.render();
					console.log('Revenue chart rendered');
				} catch (error) {
					console.error('Error rendering revenue chart:', error);
				}
			} else {
				console.error('Revenue chart element not found for product:', productId);
			}
			
			// Ads Chart
			const adsData = data.adsData || [];
			console.log('Ads data:', adsData);
			const adsDates = adsData.length > 0 ? adsData.map(item => item.date) : ['No Data'];
			const adsSpentData = adsData.length > 0 ? adsData.map(item => parseFloat(item.spent) || 0) : [0];
			
			const adsChartEl = document.querySelector(`#adsChart-${productId}`);
			console.log('Ads chart element:', adsChartEl);
			if (adsChartEl) {
				try {
					const adsChart = new ApexCharts(adsChartEl, {
						series: [{
							name: '{{ __('Amount Spent') }}',
							data: adsSpentData
						}],
						chart: { type: 'bar', height: 250, toolbar: { show: false } },
						xaxis: { categories: adsDates },
						yaxis: { labels: { formatter: (value) => value.toFixed(2) } },
						dataLabels: { enabled: false },
						colors: ['#ef4444'],
						theme: { mode: isDark ? 'dark' : 'light' }
					});
					adsChart.render();
					console.log('Ads chart rendered');
				} catch (error) {
					console.error('Error rendering ads chart:', error);
				}
			} else {
				console.error('Ads chart element not found for product:', productId);
			}

			// Leads Chart
			const leadsData = adsData.length > 0 ? adsData.map(item => parseInt(item.leads) || 0) : [0];
			const leadsChartEl = document.querySelector(`#leadsChart-${productId}`);
			console.log('Leads chart element:', leadsChartEl);
			if (leadsChartEl) {
				try {
					const leadsChart = new ApexCharts(leadsChartEl, {
						series: [{
							name: '{{ __('Leads') }}',
							data: leadsData
						}],
						chart: { type: 'line', height: 250, toolbar: { show: false } },
						stroke: { curve: 'smooth', width: 2 },
						xaxis: { categories: adsDates },
						colors: ['#f97316'],
						theme: { mode: isDark ? 'dark' : 'light' }
					});
					leadsChart.render();
					console.log('Leads chart rendered');
				} catch (error) {
					console.error('Error rendering leads chart:', error);
				}
			} else {
				console.error('Leads chart element not found for product:', productId);
			}
		};
	</script>

	<!-- Statistics Modal -->
	<div x-data="{ showStatsModal: false, loading: false, statsData: null }" 
		 x-init="window.statsModal = $data" 
		 x-show="showStatsModal" 
		 x-cloak 
		 class="fixed inset-0 z-50 overflow-y-auto" 
		 @keydown.escape.window="showStatsModal = false"
		 x-effect="if (statsData && !loading) { $nextTick(() => { if(window.renderChartsFull) window.renderChartsFull(statsData); }) }">
		<div class="flex items-end sm:items-center justify-center min-h-screen p-0 sm:p-4">
			<div x-show="showStatsModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-sm" @click="showStatsModal = false"></div>
			
			<div x-show="showStatsModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95" class="relative w-full sm:max-w-5xl bg-white dark:bg-gray-800 rounded-t-2xl sm:rounded-2xl shadow-xl overflow-hidden max-h-[90vh] flex flex-col">
				<!-- Header -->
				<div class="flex items-center justify-between p-4 border-b border-gray-100 dark:border-gray-700 sticky top-0 bg-white dark:bg-gray-800 z-10">
					<h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-gray-100 truncate pr-4" x-text="statsData && statsData.product ? statsData.product.name + ' - ' + '{{ __('Statistics') }}' : '{{ __('Loading...') }}'"></h3>
					<button @click="showStatsModal = false" class="p-2 -mr-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors touch-manipulation">
						<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
						</svg>
					</button>
				</div>
				
				<!-- Content -->
				<div class="flex-1 overflow-y-auto p-4 sm:p-6">
					<div x-show="loading" class="text-center py-12">
						<div class="inline-block animate-spin rounded-full h-10 w-10 border-3 border-blue-600 border-t-transparent"></div>
						<p class="mt-3 text-gray-500">{{ __('Loading statistics...') }}</p>
					</div>

					<template x-if="!loading && statsData">
						<div class="space-y-4 sm:space-y-6">
							<!-- Summary Cards - Scrollable on mobile -->
							<div class="overflow-x-auto -mx-4 px-4 sm:mx-0 sm:px-0">
								<div class="flex sm:grid sm:grid-cols-5 gap-3 min-w-max sm:min-w-0">
									<div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3 sm:p-4 min-w-[120px] sm:min-w-0">
										<p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mb-1">{{ __('Total Leads') }}</p>
										<p class="text-lg sm:text-2xl font-bold text-gray-900 dark:text-gray-100" x-text="statsData && statsData.product ? statsData.product.total_leads.toLocaleString() : '0'"></p>
									</div>
									<div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3 sm:p-4 min-w-[120px] sm:min-w-0">
										<p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mb-1">{{ __('Total Orders') }}</p>
										<p class="text-lg sm:text-2xl font-bold text-gray-900 dark:text-gray-100" x-text="statsData && statsData.product ? statsData.product.total_orders.toLocaleString() : '0'"></p>
									</div>
									<div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3 sm:p-4 min-w-[120px] sm:min-w-0">
										<p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mb-1">{{ __('Ads Cost') }}</p>
										<p class="text-lg sm:text-2xl font-bold text-gray-900 dark:text-gray-100" x-text="statsData && statsData.product ? parseFloat(statsData.product.total_ads_cost).toFixed(2) : '0.00'"></p>
									</div>
									<div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3 sm:p-4 min-w-[120px] sm:min-w-0">
										<p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mb-1">{{ __('Delivery %') }}</p>
										<p class="text-lg sm:text-2xl font-bold text-gray-900 dark:text-gray-100" x-text="statsData && statsData.product ? parseFloat(statsData.product.delivery_rate).toFixed(2) + '%' : '0.00%'"></p>
									</div>
									<div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3 sm:p-4 min-w-[120px] sm:min-w-0">
										<p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mb-1">{{ __('Net Profit') }}</p>
										<p class="text-lg sm:text-2xl font-bold text-gray-900 dark:text-gray-100" x-text="statsData && statsData.product ? parseFloat(statsData.product.net_profit).toFixed(2) : '0.00'"></p>
									</div>
								</div>
							</div>

							<!-- Charts -->
							<div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4" 
								x-init="$nextTick(() => { 
									setTimeout(() => { 
										if (window.renderChartsFull && statsData && statsData.product) {
											const productId = statsData.product.id;
											const chartEl = document.querySelector(`#ordersChart-${productId}`);
											if (chartEl && typeof ApexCharts !== 'undefined') {
												window.renderChartsFull(statsData);
											}
										}
									}, 300);
								})">
								<div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3 sm:p-4">
									<h4 class="text-sm font-semibold mb-2 sm:mb-3">{{ __('Orders Over Time') }}</h4>
									<div x-bind:id="statsData && statsData.product ? 'ordersChart-' + statsData.product.id : ''" style="min-height: 200px;"></div>
								</div>
								<div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3 sm:p-4">
									<h4 class="text-sm font-semibold mb-2 sm:mb-3">{{ __('Revenue Over Time') }}</h4>
									<div x-bind:id="statsData && statsData.product ? 'revenueChart-' + statsData.product.id : ''" style="min-height: 200px;"></div>
								</div>
								<div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3 sm:p-4">
									<h4 class="text-sm font-semibold mb-2 sm:mb-3">{{ __('Ads Spending Over Time') }}</h4>
									<div x-bind:id="statsData && statsData.product ? 'adsChart-' + statsData.product.id : ''" style="min-height: 200px;"></div>
								</div>
								<div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3 sm:p-4">
									<h4 class="text-sm font-semibold mb-2 sm:mb-3">{{ __('Leads Over Time') }}</h4>
									<div x-bind:id="statsData && statsData.product ? 'leadsChart-' + statsData.product.id : ''" style="min-height: 200px;"></div>
								</div>
							</div>
						</div>
					</template>
				</div>
			</div>
		</div>
	</div>
</x-admin-layout>

