<x-admin-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-2xl text-gray-900 dark:text-gray-100 leading-tight">
			{{ __('Global Dashboard') }}
		</h2>
	</x-slot>

	<!-- Load ApexCharts -->
	<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

	<div class="space-y-6">
		<!-- Filters -->
		<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4">
			<form method="GET" class="grid grid-cols-1 md:grid-cols-[1fr_1fr_auto] gap-3 items-end">
				<div>
					<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('From') }}</label>
					<div class="mt-1 relative">
						<input type="date" name="from" value="{{ request('from') }}" class="w-full rounded-md border-gray-300 pl-3 pr-10 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
						<div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
							<svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M7 10h10M7 14h6M7 2h10a2 2 0 0 1 2 2v3H5V4a2 2 0 0 1 2-2zM5 9h14v9a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2z"/></svg>
						</div>
					</div>
				</div>
				<div>
					<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('To') }}</label>
					<div class="mt-1 relative">
						<input type="date" name="to" value="{{ request('to') }}" class="w-full rounded-md border-gray-300 pl-3 pr-10 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
						<div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
							<svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M7 10h10M7 14h6M7 2h10a2 2 0 0 1 2 2v3H5V4a2 2 0 0 1 2-2zM5 9h14v9a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2z"/></svg>
						</div>
					</div>
				</div>
				<div class="flex md:justify-end">
					<button class="px-4 py-2 h-10 md:h-auto bg-gray-900 text-white rounded-md hover:bg-gray-800 dark:bg-indigo-600 dark:hover:bg-indigo-500">{{ __('Apply') }}</button>
				</div>
			</form>
		</div>

		<!-- Revenue Trends Section -->
		<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
			<div class="p-6">
				<div class="flex items-center justify-between mb-4">
					<div>
				<h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">
					<span class="inline-flex items-center">
						<svg class="w-7 h-7 text-green-600 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
						</svg>
						{{ __('ACCOUNTING BALANCE') }}
					</span>
				</h3>
						<p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
							{{ number_format($totalOrders ?? 0) }} {{ __('ORDERS') }}
						</p>
						@if(!$from && !$to)
							<p class="text-xs text-orange-600 dark:text-orange-400 mt-1">
								{{ __('Showing last 30 days') }}
							</p>
						@endif
					</div>
					<div class="flex items-center space-x-2">
					<div class="text-right">
						<p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Total Balance') }}</p>
						<p class="text-2xl font-bold text-green-600 dark:text-green-400">
							${{ number_format(array_sum($chartRevenues ?? []), 2) }}
						</p>
					</div>
					</div>
				</div>
				<div class="text-sm text-gray-600 dark:text-gray-400 mb-3">
					{{ __('Balance trends by day') }}
				</div>
				<div id="revenueChart" class="w-full" style="height: 250px;"></div>
			</div>
		</div>

		<!-- Accounting Data Section -->
		<div class="mb-6">
			<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Accounting Data') }}</h3>
			<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
				<div class="p-5 bg-white dark:bg-gray-800 rounded-lg shadow-sm border-l-4 border-green-500">
					<div class="flex items-center justify-between">
						<div>
							<div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Total Balance') }}</div>
							<div class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">
								${{ number_format($accountingBalance, 2) }}
							</div>
						</div>
						<div class="h-12 w-12 rounded-md bg-green-50 text-green-600 dark:bg-green-900/40 dark:text-green-300 flex items-center justify-center">
							<svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
								<path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
							</svg>
						</div>
					</div>
				</div>
				<div class="p-5 bg-white dark:bg-gray-800 rounded-lg shadow-sm border-l-4 border-red-500">
					<div class="flex items-center justify-between">
						<div>
							<div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Total Expenses') }}</div>
							<div class="mt-2 text-3xl font-bold text-red-600 dark:text-red-400">
								${{ number_format($accountingExpenses, 2) }}
							</div>
						</div>
						<div class="h-12 w-12 rounded-md bg-red-50 text-red-600 dark:bg-red-900/40 dark:text-red-300 flex items-center justify-center">
							<svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
								<path d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
							</svg>
						</div>
					</div>
				</div>
				<div class="p-5 bg-white dark:bg-gray-800 rounded-lg shadow-sm border-l-4 {{ $accountingNetProfit >= 0 ? 'border-green-500' : 'border-red-500' }}">
					<div class="flex items-center justify-between">
						<div>
							<div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Net Profit Balance') }}</div>
							<div class="mt-2 text-3xl font-bold {{ $accountingNetProfit >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
								${{ number_format($accountingNetProfit, 2) }}
							</div>
							<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
								{{ __('Total Balance - All Expenses') }}
							</p>
						</div>
						<div class="h-12 w-12 rounded-md {{ $accountingNetProfit >= 0 ? 'bg-green-50 text-green-600 dark:bg-green-900/40 dark:text-green-300' : 'bg-red-50 text-red-600 dark:bg-red-900/40 dark:text-red-300' }} flex items-center justify-center">
							<svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
								<path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
							</svg>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Marketing Performance Section -->
		<div class="mb-6">
			<div class="flex items-center justify-between mb-4">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Marketing Performance') }}</h3>
				<form method="GET" class="flex items-center gap-2">
					<!-- Preserve existing filters -->
					@if($from)
						<input type="hidden" name="from" value="{{ $from }}">
					@endif
					@if($to)
						<input type="hidden" name="to" value="{{ $to }}">
					@endif
					
					<div class="flex items-center gap-2">
						<label class="text-sm text-gray-600 dark:text-gray-300">{{ __('Filter by Product:') }}</label>
						<select name="product_id" onchange="this.form.submit()" class="rounded-md border-gray-300 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
							<option value="">{{ __('All Products') }}</option>
							@foreach($allProducts as $product)
								<option value="{{ $product->id }}" {{ $productId == $product->id ? 'selected' : '' }}>
									{{ $product->name }}
								</option>
							@endforeach
						</select>
					</div>
				</form>
			</div>
			@if($selectedProduct)
				<div class="mb-3 p-3 bg-indigo-50 dark:bg-indigo-900/20 border-l-4 border-indigo-500 rounded">
					<p class="text-sm text-indigo-800 dark:text-indigo-300">
						<span class="font-semibold">{{ __('Filtered by Product:') }}</span> {{ $selectedProduct->name }}
						<a href="{{ route('admin.dashboard', array_merge(request()->only(['from', 'to']))) }}" class="ml-2 text-indigo-600 dark:text-indigo-400 hover:underline">
							{{ __('Clear Filter') }}
						</a>
					</p>
				</div>
			@endif
			
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
				<div class="p-5 bg-white dark:bg-gray-800 rounded-lg shadow-sm border-l-4 border-indigo-500">
					<p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Total Leads') }}</p>
					<p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($totalLeads ?? 0) }}</p>
				</div>
				<div class="p-5 bg-white dark:bg-gray-800 rounded-lg shadow-sm border-l-4 border-red-500">
					<p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Total Ads Spend') }}</p>
					<p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">${{ number_format($totalAdsSpent ?? 0, 2) }}</p>
				</div>
				<div class="p-5 bg-white dark:bg-gray-800 rounded-lg shadow-sm border-l-4 border-amber-500">
					<p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Cost Per Lead') }}</p>
					<p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">
						{{ $costPerLead !== null ? '$' . number_format($costPerLead, 2) : __('N/A') }}
					</p>
				</div>
				<div class="p-5 bg-white dark:bg-gray-800 rounded-lg shadow-sm border-l-4 border-blue-500">
					<p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Total Orders') }}</p>
					<p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($totalOrders ?? 0) }}</p>
				</div>
				<div class="p-5 bg-white dark:bg-gray-800 rounded-lg shadow-sm border-l-4 border-green-500">
					<p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Cost Per Delivered Order') }}</p>
					<p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">
						{{ $costPerDelivered !== null ? '$' . number_format($costPerDelivered, 2) : __('N/A') }}
					</p>
				</div>
				<div class="p-5 bg-white dark:bg-gray-800 rounded-lg shadow-sm border-l-4 border-purple-500">
					<p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Delivery Rate') }}</p>
					<p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">
						{{ $deliveryRate !== null ? number_format($deliveryRate, 2) . '%' : __('N/A') }}
					</p>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Total Orders / Total Leads') }}</p>
				</div>
			</div>

			<div class="mt-6">
				<h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ __('Total Ads Spend by Platform') }}</h4>
				<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
					<div class="p-6">
						@if(($totalSpendByPlatform ?? collect())->isEmpty())
							<p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No ads spend data available yet.') }}</p>
						@else
							<div class="space-y-3">
								@foreach($totalSpendByPlatform as $platformStat)
									<div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-2 last:border-0 last:pb-0">
										<div>
											<p class="text-sm font-medium text-gray-700 dark:text-gray-200">
												{{ $platformStat->platform ?? __('Unknown Platform') }}
											</p>
										</div>
										<p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
											${{ number_format($platformStat->total_spend ?? 0, 2) }}
										</p>
									</div>
								@endforeach
							</div>
						@endif
					</div>
				</div>
			</div>

			<!-- Business KPIs relocated under marketing performance -->
			<div class="mt-8">
				<h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ __('Business KPIs') }}</h4>
				<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
					<div class="p-5 bg-white dark:bg-gray-800 rounded-lg shadow-sm border-l-4 border-green-500">
						<div class="flex items-center justify-between">
							<div>
								<div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Total Profits') }}</div>
								<div class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">${{ number_format($totalProfits, 2) }}</div>
							</div>
							<div class="h-12 w-12 rounded-md bg-green-50 text-green-600 dark:bg-green-900/40 dark:text-green-300 flex items-center justify-center">
								<svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
							</div>
						</div>
					</div>
					<div class="p-5 bg-white dark:bg-gray-800 rounded-lg shadow-sm border-l-4 border-red-500">
						<div class="flex items-center justify-between">
							<div>
								<div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Total Ads Spends') }}</div>
								<div class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">${{ number_format($totalAdsSpent, 2) }}</div>
							</div>
							<div class="h-12 w-12 rounded-md bg-red-50 text-red-600 dark:bg-red-900/40 dark:text-red-300 flex items-center justify-center">
								<svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.67v-1.93c-1.71-.36-3.16-1.46-3.27-3.4h1.96c.1 1.05.82 1.87 2 1.96V12.5c-1.79-.1-3.33-1.39-3.33-3.4 0-1.93 1.57-3.4 3.33-3.4V4h2.67v1.1c1.71.36 3.16 1.46 3.27 3.4H12.5c-.1-1.05-.82-1.87-2-1.96V11.5c1.79.1 3.33 1.39 3.33 3.4 0 1.93-1.57 3.4-3.33 3.4z"/></svg>
							</div>
						</div>
					</div>
					<div class="p-5 bg-white dark:bg-gray-800 rounded-lg shadow-sm border-l-4 border-blue-500">
						<div class="flex items-center justify-between">
							<div>
								<div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Total Invoices') }}</div>
								<div class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($totalInvoices) }}</div>
							</div>
							<div class="h-12 w-12 rounded-md bg-blue-50 text-blue-600 dark:bg-blue-900/40 dark:text-blue-300 flex items-center justify-center">
								<svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor"><path d="M8 2h8a2 2 0 0 1 2 2v18l-6-3-6 3V4a2 2 0 0 1 2-2z"/></svg>
							</div>
						</div>
					</div>
					<div class="p-5 bg-white dark:bg-gray-800 rounded-lg shadow-sm border-l-4 border-purple-500">
						<div class="flex items-center justify-between">
							<div>
								<div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Stock Quantity') }}</div>
								<div class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($totalStockQty) }}</div>
							</div>
							<div class="h-12 w-12 rounded-md bg-purple-50 text-purple-600 dark:bg-purple-900/40 dark:text-purple-300 flex items-center justify-center">
								<svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor"><path d="M20 6h-2.18c.11-.31.18-.65.18-1a2.996 2.996 0 0 0-5.5-1.65l-.5.67-.5-.68C10.96 2.54 10 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-5-2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM9 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm11 15H4v-2h16v2zm0-5H4V8h5.08L7 10.83 8.62 12 11 8.76l1-1.36 1 1.36L15.38 12 17 10.83 14.92 8H20v6z"/></svg>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Management Cards -->
		<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
			<!-- Stock Management -->
			<div class="p-5 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">{{ __('Stock Management') }}</h3>
				<div class="space-y-2">
					<div class="flex justify-between">
						<span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Total Stock Value') }}</span>
						<span class="text-sm font-semibold text-gray-900 dark:text-gray-100">${{ number_format($totalStockValue, 2) }}</span>
					</div>
					<div class="flex justify-between">
						<span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Low Stock Items') }}</span>
						<span class="text-sm font-semibold text-red-600">{{ $lowStockProducts->count() }}</span>
					</div>
				</div>
			</div>

			<!-- Sourcing Management -->
			<div class="p-5 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">{{ __('Sourcing Management') }}</h3>
				<div class="space-y-2">
					<div class="flex justify-between">
						<span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Total Sourcings') }}</span>
						<span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $totalSourcings }}</span>
					</div>
					<div class="flex justify-between">
						<span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Validated') }}</span>
						<span class="text-sm font-semibold text-green-600">{{ $validatedSourcings }}</span>
					</div>
					<div class="flex justify-between">
						<span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Pending') }}</span>
						<span class="text-sm font-semibold text-orange-600">{{ $pendingSourcings }}</span>
					</div>
				</div>
			</div>

			<!-- Ads Management -->
			<div class="p-5 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">{{ __('Ads Management') }}</h3>
				<div class="space-y-2">
					<div class="flex justify-between">
						<span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Total Campaigns') }}</span>
						<span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $totalAdsCampaigns }}</span>
					</div>
					<div class="flex justify-between">
						<span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Total Spent') }}</span>
						<span class="text-sm font-semibold text-gray-900 dark:text-gray-100">${{ number_format($totalAdsSpent, 2) }}</span>
					</div>
				</div>
			</div>
		</div>

		<!-- Business Overview Chart -->
		<div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
			<h3 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ __('Business Overview') }}</h3>
			<p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
				{{ __('Comprehensive analytics showing your business performance over time') }}
				@if(!$from || !$to)
					<span class="text-xs text-orange-600 dark:text-orange-400">({{ __('Showing all-time data. Use date filters for specific periods.') }})</span>
				@endif
			</p>
			<div id="businessOverviewChart" style="min-height: 400px;">
				@if(empty($chartData) || (isset($chartData['profits']) && $chartData['profits']->isEmpty()))
					<div class="flex items-center justify-center h-96 text-gray-500 dark:text-gray-400">
						<div class="text-center">
							<svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
							</svg>
							<p class="mt-4">{{ __('No data available for the selected period.') }}</p>
							<p class="text-sm mt-2">{{ __('Add invoices and ads campaigns to see analytics.') }}</p>
						</div>
					</div>
				@endif
			</div>
		</div>

		<!-- Charts Section -->
		@if(!empty($chartData))
		<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
			<!-- Profits Over Time -->
			<div class="p-5 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Net Profit Over Time') }}</h3>
				<div id="profitsChart" style="min-height: 300px;"></div>
			</div>

			<!-- Ads Spending Over Time -->
			<div class="p-5 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Ads Spending Over Time') }}</h3>
				<div id="adsChart" style="min-height: 300px;"></div>
			</div>

			<!-- Invoices Over Time -->
			<div class="p-5 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Invoices Over Time') }}</h3>
				<div id="invoicesChart" style="min-height: 300px;"></div>
			</div>

			<!-- Revenue vs Ads Cost -->
			<div class="p-5 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Revenue vs Ads Cost') }}</h3>
				<div id="revenueVsAdsChart" style="min-height: 300px;"></div>
			</div>
		</div>
		@endif

		<!-- Profitable Products -->
		<div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
			<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Top Profitable Products') }}</h3>
			@if($profitableProducts->isEmpty())
				<p class="text-gray-500 dark:text-gray-400">{{ __('No profitable products found.') }}</p>
			@else
				<div class="overflow-x-auto">
					<table class="min-w-full text-sm">
						<thead>
							<tr class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
								<th class="py-3 pe-2">{{ __('Product') }}</th>
								<th class="py-3 pe-2">{{ __('Total Amount') }}</th>
								<th class="py-3 pe-2">{{ __('Ads Cost') }}</th>
								<th class="py-3 pe-2">{{ __('Net Profit') }}</th>
							</tr>
						</thead>
						<tbody class="text-gray-900 dark:text-gray-100">
							@foreach($profitableProducts as $product)
								<tr class="border-b border-gray-100 dark:border-gray-700/60">
									<td class="py-3 pe-2 font-medium">{{ $product->name }}</td>
									<td class="py-3 pe-2">${{ number_format($product->total_amount, 2) }}</td>
									<td class="py-3 pe-2">${{ number_format($product->total_ads_cost, 2) }}</td>
									<td class="py-3 pe-2 font-semibold text-green-600">${{ number_format($product->net_profit, 2) }}</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			@endif
		</div>

		<!-- Low Stock Alerts -->
		@if($lowStockProducts->isNotEmpty())
		<div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
			<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Low Stock Alerts') }}</h3>
			<div class="overflow-x-auto">
				<table class="min-w-full text-sm">
					<thead>
						<tr class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
							<th class="py-3 pe-2">{{ __('Product') }}</th>
							<th class="py-3 pe-2">{{ __('Category') }}</th>
							<th class="py-3 pe-2">{{ __('Current Qty') }}</th>
							<th class="py-3 pe-2">{{ __('Threshold') }}</th>
						</tr>
					</thead>
					<tbody class="text-gray-900 dark:text-gray-100">
						@foreach($lowStockProducts as $product)
							<tr class="border-b border-gray-100 dark:border-gray-700/60">
								<td class="py-3 pe-2 font-medium">{{ $product->name }}</td>
								<td class="py-3 pe-2">{{ $product->category?->name ?? __('None') }}</td>
								<td class="py-3 pe-2 text-red-600 font-semibold">{{ $product->quantity }}</td>
								<td class="py-3 pe-2">{{ $product->low_stock_threshold }}</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
		@endif

		<!-- By Country Overview -->
		<div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
			<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">{{ __('By Country Overview') }}</h3>
			<p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ __('Breakdown of key metrics by country.') }}</p>

			<div class="overflow-x-auto">
				<table class="min-w-full text-sm">
					<thead>
						<tr class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
							<th class="py-3 pe-2">{{ __('Country') }}</th>
							<th class="py-3 pe-2">{{ __('Products') }}</th>
							<th class="py-3 pe-2">{{ __('Invoices') }}</th>
							<th class="py-3 pe-2">{{ __('Total Value') }}</th>
						</tr>
					</thead>
					<tbody class="text-gray-900 dark:text-gray-100">
						@foreach($byCountry as $row)
							<tr class="border-b border-gray-100 dark:border-gray-700/60">
								<td class="py-3 pe-2 font-medium">{{ $row->name }}</td>
								<td class="py-3 pe-2">{{ number_format($row->products_count ?? 0) }}</td>
								<td class="py-3 pe-2">{{ number_format($row->invoices_count ?? 0) }}</td>
								<td class="py-3 pe-2">${{ number_format($row->stock_value ?? 0, 2) }}</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<script>
		document.addEventListener('DOMContentLoaded', function() {
			const isDark = document.documentElement.classList.contains('dark');
			const chartTheme = { mode: isDark ? 'dark' : 'light' };

			// Revenue Trends Chart
			const chartDates = @json($chartDates ?? []);
			const chartRevenues = @json($chartRevenues ?? []);

			if (chartDates.length > 0) {
				const revenueChart = new ApexCharts(document.querySelector("#revenueChart"), {
					chart: {
						type: 'area',
						height: 250,
						toolbar: {
							show: false
						},
						sparkline: {
							enabled: false
						},
						zoom: {
							enabled: false
						}
					},
					series: [{
						name: 'Balance',
						data: chartRevenues
					}],
					xaxis: {
						categories: chartDates,
						labels: {
							style: {
								colors: isDark ? '#9ca3af' : '#6b7280',
								fontSize: '11px'
							},
							rotate: -45,
							rotateAlways: false,
							hideOverlappingLabels: true,
							showDuplicates: false,
							trim: false
						},
						axisBorder: {
							show: false
						},
						axisTicks: {
							show: false
						}
					},
					yaxis: {
						labels: {
							style: {
								colors: isDark ? '#9ca3af' : '#6b7280',
								fontSize: '12px'
							},
							formatter: function(value) {
								return '$' + value.toFixed(2);
							}
						}
					},
					stroke: {
						curve: 'smooth',
						width: 3,
						colors: ['#10b981']
					},
					fill: {
						type: 'gradient',
						gradient: {
							shadeIntensity: 1,
							opacityFrom: 0.5,
							opacityTo: 0.1,
							stops: [0, 90, 100]
						},
						colors: ['#10b981']
					},
					dataLabels: {
						enabled: false
					},
					grid: {
						borderColor: isDark ? '#374151' : '#e5e7eb',
						strokeDashArray: 4,
						xaxis: {
							lines: {
								show: false
							}
						},
						yaxis: {
							lines: {
								show: true
							}
						}
					},
					tooltip: {
						enabled: true,
						theme: isDark ? 'dark' : 'light',
						y: {
							formatter: function(value) {
								return '$' + value.toFixed(2);
							}
						}
					},
					markers: {
						size: 0,
						hover: {
							size: 5,
							sizeOffset: 3
						}
					}
				});
				revenueChart.render();
			}

			// Declare chart data variables once (if available)
			@if(!empty($chartData) && isset($chartData['profits']) && !$chartData['profits']->isEmpty())
			const profitsData = @json($chartData['profits']);
			@endif
			@if(!empty($chartData) && isset($chartData['invoices']) && !$chartData['invoices']->isEmpty())
			const invoicesData = @json($chartData['invoices']);
			@endif
			@if(!empty($chartData) && isset($chartData['ads']) && !$chartData['ads']->isEmpty())
			const adsData = @json($chartData['ads']);
			@endif

			// Business Overview Chart - Comprehensive Analytics
			@if(!empty($chartData) && isset($chartData['profits']) && isset($chartData['invoices']) && !$chartData['profits']->isEmpty())
			const businessOverviewData = profitsData;
			
			// Combine data for comprehensive view
			const months = businessOverviewData.map(item => item.month);
			const totalAmounts = businessOverviewData.map(item => parseFloat(item.total_amount) || 0);
			const adsCosts = businessOverviewData.map(item => parseFloat(item.ads_cost) || 0);
			const netProfits = businessOverviewData.map(item => parseFloat(item.net_profit) || 0);
			const invoiceCounts = months.map(month => {
				const invoice = invoicesData.find(inv => inv.month === month);
				return invoice ? parseInt(invoice.count) || 0 : 0;
			});
			const invoiceTotals = months.map(month => {
				const invoice = invoicesData.find(inv => inv.month === month);
				return invoice ? parseFloat(invoice.total) || 0 : 0;
			});

			const businessOverviewChart = new ApexCharts(document.querySelector("#businessOverviewChart"), {
				series: [
					{
						name: '{{ __('Total Amount') }}',
						type: 'column',
						data: totalAmounts
					},
					{
						name: '{{ __('Ads Cost') }}',
						type: 'column',
						data: adsCosts
					},
					{
						name: '{{ __('Net Profit') }}',
						type: 'line',
						data: netProfits
					},
					{
						name: '{{ __('Invoices Count') }}',
						type: 'line',
						data: invoiceCounts
					}
				],
				chart: { 
					height: 400, 
					type: 'line',
					toolbar: { show: true },
					zoom: { enabled: true }
				},
				stroke: { 
					width: [0, 0, 3, 3],
					curve: 'smooth'
				},
				xaxis: { 
					categories: months,
					title: { text: '{{ __('Month') }}' }
				},
				yaxis: [
					{
						title: { text: '{{ __('Amount ($)') }}' },
						labels: { formatter: (value) => '$' + value.toFixed(2) }
					},
					{
						opposite: true,
						title: { text: '{{ __('Count') }}' },
						labels: { formatter: (value) => Math.round(value) }
					}
				],
				colors: ['#3b82f6', '#ef4444', '#10b981', '#f59e0b'],
				theme: chartTheme,
				dataLabels: { 
					enabled: false 
				},
				legend: {
					position: 'top',
					horizontalAlign: 'center'
				},
				tooltip: {
					shared: true,
					intersect: false,
					y: {
						formatter: function (val, opts) {
							if (opts.seriesIndex === 2 || opts.seriesIndex === 0 || opts.seriesIndex === 1) {
								return '$' + val.toFixed(2);
							}
							return val;
						}
					}
				},
				plotOptions: {
					bar: {
						columnWidth: '50%'
					}
				}
			});
			businessOverviewChart.render();
			@endif

			// Profits Chart
			@if(!empty($chartData) && isset($chartData['profits']) && !$chartData['profits']->isEmpty())
			const profitsChart = new ApexCharts(document.querySelector("#profitsChart"), {
				series: [{
					name: '{{ __('Net Profit') }}',
					data: profitsData.map(item => parseFloat(item.net_profit) || 0)
				}],
				chart: { type: 'area', height: 300, toolbar: { show: false } },
				xaxis: { categories: profitsData.map(item => item.month) },
				stroke: { curve: 'smooth', width: 2 },
				colors: ['#10b981'],
				theme: chartTheme,
				dataLabels: { enabled: false },
				yaxis: { labels: { formatter: (value) => '$' + value.toFixed(2) } }
			});
			profitsChart.render();
			@endif

			// Ads Chart
			@if(!empty($chartData) && isset($chartData['ads']) && !$chartData['ads']->isEmpty())
			const adsChart = new ApexCharts(document.querySelector("#adsChart"), {
				series: [{
					name: '{{ __('Ads Spent') }}',
					data: adsData.map(item => parseFloat(item.spent) || 0)
				}],
				chart: { type: 'bar', height: 300, toolbar: { show: false } },
				xaxis: { categories: adsData.map(item => item.month) },
				colors: ['#ef4444'],
				theme: chartTheme,
				dataLabels: { enabled: false },
				yaxis: { labels: { formatter: (value) => '$' + value.toFixed(2) } }
			});
			adsChart.render();
			@endif

			// Invoices Chart
			@if(!empty($chartData) && isset($chartData['invoices']) && !$chartData['invoices']->isEmpty())
			const invoicesChart = new ApexCharts(document.querySelector("#invoicesChart"), {
				series: [{
					name: '{{ __('Invoices') }}',
					data: invoicesData.map(item => parseInt(item.count) || 0)
				}],
				chart: { type: 'line', height: 300, toolbar: { show: false } },
				xaxis: { categories: invoicesData.map(item => item.month) },
				stroke: { curve: 'smooth', width: 2 },
				colors: ['#3b82f6'],
				theme: chartTheme,
				dataLabels: { enabled: false }
			});
			invoicesChart.render();
			@endif

			// Revenue vs Ads Chart
			@if(!empty($chartData) && isset($chartData['profits']) && isset($chartData['ads']) && !$chartData['profits']->isEmpty())
			const revenueVsAdsChart = new ApexCharts(document.querySelector("#revenueVsAdsChart"), {
				series: [{
					name: '{{ __('Total Amount') }}',
					data: profitsData.map(item => parseFloat(item.total_amount) || 0)
				}, {
					name: '{{ __('Ads Cost') }}',
					data: profitsData.map(item => parseFloat(item.ads_cost) || 0)
				}],
				chart: { type: 'line', height: 300, toolbar: { show: false } },
				xaxis: { categories: profitsData.map(item => item.month) },
				stroke: { curve: 'smooth', width: 2 },
				colors: ['#10b981', '#ef4444'],
				theme: chartTheme,
				dataLabels: { enabled: false },
				yaxis: { labels: { formatter: (value) => '$' + value.toFixed(2) } }
			});
			revenueVsAdsChart.render();
			@endif
		});
	</script>
</x-admin-layout>
