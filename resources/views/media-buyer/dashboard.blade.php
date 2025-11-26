<x-media-buyer-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Dashboard') }}
		</h2>
	</x-slot>

	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
			
			<!-- Stats Cards -->
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
				<!-- Total Campaigns -->
				<div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
					<div class="flex items-center justify-between">
						<div>
							<p class="text-sm opacity-90 font-medium">{{ __('Total Campaigns') }}</p>
							<p class="text-3xl font-bold mt-2">{{ number_format($totalCampaigns) }}</p>
						</div>
						<div class="bg-white/20 p-3 rounded-lg">
							<svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
							</svg>
						</div>
					</div>
				</div>

				<!-- Total Leads -->
				<div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
					<div class="flex items-center justify-between">
						<div>
							<p class="text-sm opacity-90 font-medium">{{ __('Total Leads') }}</p>
							<p class="text-3xl font-bold mt-2">{{ number_format($totalLeads) }}</p>
						</div>
						<div class="bg-white/20 p-3 rounded-lg">
							<svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
							</svg>
						</div>
					</div>
				</div>

				<!-- Total Spent -->
				<div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
					<div class="flex items-center justify-between">
						<div>
							<p class="text-sm opacity-90 font-medium">{{ __('Total Ads Spent') }}</p>
							<p class="text-3xl font-bold mt-2">{{ number_format($totalSpent, 2) }}</p>
						</div>
						<div class="bg-white/20 p-3 rounded-lg">
							<svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
							</svg>
						</div>
					</div>
				</div>

				<!-- Avg Cost Per Lead -->
				<div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white">
					<div class="flex items-center justify-between">
						<div>
							<p class="text-sm opacity-90 font-medium">{{ __('Avg Cost Per Lead') }}</p>
							<p class="text-3xl font-bold mt-2">{{ number_format($avgCostPerLead, 2) }}</p>
						</div>
						<div class="bg-white/20 p-3 rounded-lg">
							<svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
							</svg>
						</div>
					</div>
				</div>
			</div>

			<!-- Secondary Stats -->
			<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
				<div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
					<div class="flex items-center justify-between">
						<div>
							<p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Total Expenses') }}</p>
							<p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ number_format($totalExpenses, 2) }}</p>
						</div>
						<div class="bg-red-100 dark:bg-red-900/30 p-3 rounded-lg">
							<svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
							</svg>
						</div>
					</div>
				</div>

				<div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
					<div class="flex items-center justify-between">
						<div>
							<p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Assigned Products') }}</p>
							<p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ number_format($totalProducts) }}</p>
						</div>
						<div class="bg-indigo-100 dark:bg-indigo-900/30 p-3 rounded-lg">
							<svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
							</svg>
						</div>
					</div>
				</div>
			</div>

			<!-- Recent Campaigns & Top Products -->
			<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
				<!-- Recent Campaigns -->
				<div class="bg-white dark:bg-gray-800 rounded-lg shadow">
					<div class="p-6 border-b border-gray-200 dark:border-gray-700">
						<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Recent Campaigns') }}</h3>
					</div>
					<div class="p-6">
						@if($recentCampaigns->isEmpty())
							<p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">{{ __('No campaigns yet') }}</p>
						@else
							<div class="space-y-4">
								@foreach($recentCampaigns as $campaign)
									<div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
										<div class="flex-1">
											<p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $campaign->name }}</p>
											<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
												{{ $campaign->platform?->name }} • {{ $campaign->date_from->format('M d, Y') }}
											</p>
										</div>
										<div class="text-right">
											<p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ number_format($campaign->total_amount_spent, 2) }}</p>
											<p class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($campaign->total_leads) }} leads</p>
										</div>
									</div>
								@endforeach
							</div>
						@endif
					</div>
				</div>

				<!-- Top Products -->
				<div class="bg-white dark:bg-gray-800 rounded-lg shadow">
					<div class="p-6 border-b border-gray-200 dark:border-gray-700">
						<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Top Performing Products') }}</h3>
					</div>
					<div class="p-6">
						@if($topProducts->isEmpty())
							<p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">{{ __('No products yet') }}</p>
						@else
							<div class="space-y-4">
								@foreach($topProducts as $product)
									<div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
										@if($product->image)
											<img src="{{ \Illuminate\Support\Facades\Storage::url($product->image) }}" alt="{{ $product->name }}" class="h-10 w-10 object-cover rounded">
										@else
											<div class="h-10 w-10 bg-gray-200 dark:bg-gray-600 rounded flex items-center justify-center">
												<svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
												</svg>
											</div>
										@endif
										<div class="flex-1">
											<p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $product->name }}</p>
											<p class="text-xs text-gray-500 dark:text-gray-400">{{ $product->category?->name }}</p>
										</div>
										<div class="text-right">
											<p class="text-sm font-semibold text-green-600 dark:text-green-400">{{ number_format($product->user_leads) }}</p>
											<p class="text-xs text-gray-500 dark:text-gray-400">leads</p>
										</div>
									</div>
								@endforeach
							</div>
						@endif
					</div>
				</div>
			</div>

			<!-- Quick Actions -->
			<div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Quick Actions') }}</h3>
				<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
					<a href="{{ route('media-buyer.campaigns.create') }}" class="flex flex-col items-center justify-center p-4 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-lg hover:shadow-md transition border border-blue-200 dark:border-blue-700">
						<svg class="h-8 w-8 text-blue-600 dark:text-blue-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
						</svg>
						<span class="text-sm font-medium text-blue-900 dark:text-blue-100">{{ __('Add Campaign') }}</span>
					</a>
					
					<a href="{{ route('media-buyer.expenses.create') }}" class="flex flex-col items-center justify-center p-4 bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-lg hover:shadow-md transition border border-purple-200 dark:border-purple-700">
						<svg class="h-8 w-8 text-purple-600 dark:text-purple-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
						</svg>
						<span class="text-sm font-medium text-purple-900 dark:text-purple-100">{{ __('Add Expense') }}</span>
					</a>
					
					<a href="{{ route('media-buyer.products') }}" class="flex flex-col items-center justify-center p-4 bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-lg hover:shadow-md transition border border-green-200 dark:border-green-700">
						<svg class="h-8 w-8 text-green-600 dark:text-green-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
						</svg>
						<span class="text-sm font-medium text-green-900 dark:text-green-100">{{ __('View Products') }}</span>
					</a>
					
					<a href="{{ route('media-buyer.campaigns') }}" class="flex flex-col items-center justify-center p-4 bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 rounded-lg hover:shadow-md transition border border-orange-200 dark:border-orange-700">
						<svg class="h-8 w-8 text-orange-600 dark:text-orange-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
						</svg>
						<span class="text-sm font-medium text-orange-900 dark:text-orange-100">{{ __('All Campaigns') }}</span>
					</a>
				</div>
			</div>
		</div>
	</div>
</x-media-buyer-layout>

