<x-media-buyer-layout>
	<x-slot name="header">
		<div class="flex items-center justify-between">
			<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
				{{ __('My Ad Campaigns') }}
			</h2>
			<a href="{{ route('media-buyer.campaigns.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
				<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
				</svg>
				{{ __('Add Campaign') }}
			</a>
		</div>
	</x-slot>

	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
			@if (session('status'))
				<div class="mb-4 px-4 py-3 bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 rounded-md">
					{{ session('status') }}
				</div>
			@endif

			<!-- Filter Form -->
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 mb-4">
				<form method="GET" action="{{ route('media-buyer.campaigns') }}" class="flex gap-3 flex-wrap items-end">
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">{{ __('Date From') }}</label>
						<input type="date" name="from" value="{{ $from }}" class="rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
					</div>
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">{{ __('Date To') }}</label>
						<input type="date" name="to" value="{{ $to }}" class="rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
					</div>
					<button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
						{{ __('Filter') }}
					</button>
					@if($from || $to)
						<a href="{{ route('media-buyer.campaigns') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition">
							{{ __('Clear') }}
						</a>
					@endif
				</form>
			</div>

			<!-- Statistics Cards -->
			<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
				<!-- Total Spent Card -->
				<div class="bg-gradient-to-r from-indigo-600 to-purple-600 shadow-sm rounded-lg p-6 text-white">
					<div class="flex items-center justify-between">
						<div>
							<p class="text-sm opacity-90">{{ __('Total Amount Spent') }}</p>
							<p class="text-3xl font-bold mt-1">{{ number_format($totalSpent, 2) }}</p>
						</div>
						<svg class="h-12 w-12 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
						</svg>
					</div>
				</div>

				<!-- Total Leads Card -->
				<div class="bg-gradient-to-r from-blue-600 to-cyan-600 shadow-sm rounded-lg p-6 text-white">
					<div class="flex items-center justify-between">
						<div>
							<p class="text-sm opacity-90">{{ __('Total Leads') }}</p>
							<p class="text-3xl font-bold mt-1">{{ number_format($totalLeads) }}</p>
						</div>
						<svg class="h-12 w-12 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
						</svg>
					</div>
				</div>

				<!-- Cost Per Lead Card -->
				<div class="bg-gradient-to-r from-green-600 to-teal-600 shadow-sm rounded-lg p-6 text-white">
					<div class="flex items-center justify-between">
						<div>
							<p class="text-sm opacity-90">{{ __('Cost Per Lead') }}</p>
							<p class="text-3xl font-bold mt-1">{{ number_format($costPerLead, 2) }}</p>
						</div>
						<svg class="h-12 w-12 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
						</svg>
					</div>
				</div>
			</div>

			@if($campaigns->isEmpty())
				<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-8 text-center">
					<svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
					</svg>
					<h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('No campaigns yet') }}</h3>
					<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Start by creating your first ad campaign.') }}</p>
					<div class="mt-6">
						<a href="{{ route('media-buyer.campaigns.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
							{{ __('Add Campaign') }}
						</a>
					</div>
				</div>
			@else
				<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
					<div class="overflow-x-auto">
						<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
							<thead class="bg-gray-50 dark:bg-gray-900">
								<tr>
									<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
										{{ __('Campaign') }}
									</th>
									<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
										{{ __('Platform') }}
									</th>
									<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
										{{ __('Country') }}
									</th>
									<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
										{{ __('Period') }}
									</th>
									<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
										{{ __('Products') }}
									</th>
									<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
										{{ __('Total Spent') }}
									</th>
									<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
										{{ __('Total Leads') }}
									</th>
								</tr>
							</thead>
							<tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
								@foreach($campaigns as $campaign)
									<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
										<td class="px-6 py-4 whitespace-nowrap">
											<div class="text-sm font-medium text-gray-900 dark:text-gray-100">
												{{ $campaign->name }}
											</div>
										</td>
										<td class="px-6 py-4 whitespace-nowrap">
											<div class="text-sm text-gray-500 dark:text-gray-400">
												{{ $campaign->platform?->name ?? '-' }}
											</div>
										</td>
										<td class="px-6 py-4 whitespace-nowrap">
											<div class="text-sm text-gray-500 dark:text-gray-400">
												{{ $campaign->country?->name ?? '-' }}
											</div>
										</td>
										<td class="px-6 py-4 whitespace-nowrap">
											<div class="text-sm text-gray-500 dark:text-gray-400">
												{{ $campaign->date_from->format('Y-m-d') }} - {{ $campaign->date_to->format('Y-m-d') }}
											</div>
										</td>
										<td class="px-6 py-4">
											<div class="flex flex-wrap gap-1">
												@foreach($campaign->products as $product)
													<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
														{{ $product->name }}
													</span>
												@endforeach
											</div>
										</td>
										<td class="px-6 py-4 whitespace-nowrap">
											<div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
												{{ number_format($campaign->total_amount_spent, 2) }}
											</div>
										</td>
										<td class="px-6 py-4 whitespace-nowrap">
											<div class="text-sm text-gray-900 dark:text-gray-100">
												{{ number_format($campaign->total_leads) }}
											</div>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>

				@if($campaigns->hasPages())
					<div class="mt-6">
						{{ $campaigns->links() }}
					</div>
				@endif
			@endif
		</div>
	</div>
</x-media-buyer-layout>

