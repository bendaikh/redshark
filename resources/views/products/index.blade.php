<x-admin-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Stock') }}
		</h2>
	</x-slot>

	<!-- Load ApexCharts immediately -->
	<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
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
							<tr class="text-xs uppercase tracking-wider bg-gray-50 dark:bg-gray-900 text-gray-500 dark:text-gray-300">
								<th class="py-2 px-2">{{ __('Image') }}</th>
								<th class="py-2 px-2">{{ __('Name') }}</th>
								<th class="py-2 px-2">{{ __('Category') }}</th>
								<th class="py-2 px-2">{{ __('Initial Qty') }}</th>
								<th class="py-2 px-2">{{ __('Remaining Qty') }}</th>
								<th class="py-2 px-2">{{ __('Cost Total') }}</th>
								<th class="py-2 px-2">{{ __('Country') }}</th>
								<th class="py-2 px-2">{{ __('Delivery Rate') }}</th>
								<th class="py-2 px-2">{{ __('Ads Cost Total') }}</th>
								<th class="py-2 px-2">{{ __('Cost Per Lead') }}</th>
								<th class="py-2 px-2">{{ __('Cost Per Delivered') }}</th>
								<th class="py-2 px-2">{{ __('Net Profit') }}</th>
								<th class="py-2 px-2 text-center">{{ __('Actions') }}</th>
							</tr>
						</thead>
						<tbody class="text-gray-700 dark:text-gray-200 text-sm">
							@foreach($products as $p)
								@php
									$deliveryRate = $p->delivery_rate;
									$bgColor = '';
									if ($deliveryRate >= 20) {
										$bgColor = 'bg-green-100 dark:bg-green-900';
									} elseif ($deliveryRate >= 15 && $deliveryRate <= 19) {
										$bgColor = 'bg-orange-100 dark:bg-orange-900';
									} elseif ($deliveryRate <= 14) {
										$bgColor = 'bg-red-100 dark:bg-red-900';
									}
								@endphp
								<tr class="border-t border-gray-200 dark:border-gray-700 {{ $bgColor }}">
									<td class="py-2 px-2">
										@if($p->image)
											<img src="{{ \Illuminate\Support\Facades\Storage::url($p->image) }}" alt="{{ $p->name }}" class="h-10 w-10 object-cover rounded">
										@endif
									</td>
								<td class="py-2 px-2">{{ $p->name }}</td>
								<td class="py-2 px-2">{{ $p->category?->name }}</td>
								<td class="py-2 px-2">{{ $p->quantity }}</td>
								<td class="py-2 px-2">{{ $p->remaining_qty }}</td>
								<td class="py-2 px-2">{{ number_format($p->average_cost, 2) }}</td>
								<td class="py-2 px-2">{{ $p->country?->name }}</td>
									<td class="py-2 px-2">{{ number_format($deliveryRate, 2) }}%</td>
									<td class="py-2 px-2">{{ number_format($p->total_ads_cost, 2) }}</td>
									<td class="py-2 px-2">{{ number_format($p->cost_per_lead, 2) }}</td>
									<td class="py-2 px-2">{{ number_format($p->cost_per_delivered, 2) }}</td>
									<td class="py-2 px-2">{{ number_format($p->net_profit, 2) }}</td>
									<td class="py-2 px-2">
										<div class="flex items-center justify-center gap-2">
											<button @click="window.loadStatistics({{ $p->id }})" class="p-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded transition-colors" title="{{ __('Statistics') }}">
												<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
												</svg>
											</button>
											<a href="{{ route('products.edit', $p) }}" class="p-1.5 text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded transition-colors" title="{{ __('Edit') }}">
												<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
												</svg>
											</a>
											<form action="{{ route('products.destroy', $p) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Delete this product?') }}')">
												@csrf @method('DELETE')
												<button type="submit" class="p-1.5 text-red-600 hover:text-red-800 hover:bg-red-50 dark:hover:bg-red-900/20 rounded transition-colors" title="{{ __('Delete') }}">
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
				<div class="mt-4">{{ $products->links() }}</div>
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
						colors: ['#3b82f6', '#10b981'],
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
		<div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
			<div x-show="showStatsModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 dark:bg-opacity-50" @click="showStatsModal = false"></div>
			
			<div x-show="showStatsModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full">
				<div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
					<div class="flex items-center justify-between mb-4">
						<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100" x-text="statsData && statsData.product ? statsData.product.name + ' - ' + '{{ __('Statistics') }}' : '{{ __('Loading...') }}'"></h3>
						<button @click="showStatsModal = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
							<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
							</svg>
						</button>
					</div>
					
					<div x-show="loading" class="text-center py-8">
						<div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
						<p class="mt-2 text-gray-500">{{ __('Loading statistics...') }}</p>
					</div>

					<template x-if="!loading && statsData">
						<div class="space-y-6">
							<!-- Summary Cards -->
							<div class="grid grid-cols-2 md:grid-cols-5 gap-4">
								<div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
									<p class="text-sm text-gray-500 dark:text-gray-400 mb-1">{{ __('Total Leads') }}</p>
									<p class="text-2xl font-bold text-gray-900 dark:text-gray-100" x-text="statsData && statsData.product ? statsData.product.total_leads.toLocaleString() : '0'"></p>
								</div>
								<div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
									<p class="text-sm text-gray-500 dark:text-gray-400 mb-1">{{ __('Total Orders') }}</p>
									<p class="text-2xl font-bold text-gray-900 dark:text-gray-100" x-text="statsData && statsData.product ? statsData.product.total_orders.toLocaleString() : '0'"></p>
								</div>
								<div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
									<p class="text-sm text-gray-500 dark:text-gray-400 mb-1">{{ __('Ads Cost Total') }}</p>
									<p class="text-2xl font-bold text-gray-900 dark:text-gray-100" x-text="statsData && statsData.product ? parseFloat(statsData.product.total_ads_cost).toFixed(2) : '0.00'"></p>
								</div>
								<div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
									<p class="text-sm text-gray-500 dark:text-gray-400 mb-1">{{ __('Delivery Rate') }}</p>
									<p class="text-2xl font-bold text-gray-900 dark:text-gray-100" x-text="statsData && statsData.product ? parseFloat(statsData.product.delivery_rate).toFixed(2) + '%' : '0.00%'"></p>
								</div>
								<div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
									<p class="text-sm text-gray-500 dark:text-gray-400 mb-1">{{ __('Net Profit') }}</p>
									<p class="text-2xl font-bold text-gray-900 dark:text-gray-100" x-text="statsData && statsData.product ? parseFloat(statsData.product.net_profit).toFixed(2) : '0.00'"></p>
								</div>
							</div>

							<!-- Charts -->
							<div class="grid grid-cols-1 md:grid-cols-2 gap-4" 
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
								<div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
									<h4 class="text-sm font-semibold mb-3">{{ __('Orders Over Time') }}</h4>
									<div x-bind:id="statsData && statsData.product ? 'ordersChart-' + statsData.product.id : ''" style="min-height: 250px;"></div>
								</div>
								<div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
									<h4 class="text-sm font-semibold mb-3">{{ __('Revenue Over Time') }}</h4>
									<div x-bind:id="statsData && statsData.product ? 'revenueChart-' + statsData.product.id : ''" style="min-height: 250px;"></div>
								</div>
								<div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
									<h4 class="text-sm font-semibold mb-3">{{ __('Ads Spending Over Time') }}</h4>
									<div x-bind:id="statsData && statsData.product ? 'adsChart-' + statsData.product.id : ''" style="min-height: 250px;"></div>
								</div>
								<div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
									<h4 class="text-sm font-semibold mb-3">{{ __('Leads Over Time') }}</h4>
									<div x-bind:id="statsData && statsData.product ? 'leadsChart-' + statsData.product.id : ''" style="min-height: 250px;"></div>
								</div>
							</div>
						</div>
					</template>
				</div>
			</div>
		</div>
	</div>
</x-admin-layout>

