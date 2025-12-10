<x-media-buyer-layout>
	<x-slot name="header">
		<div class="flex items-center justify-between">
			<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
				{{ __('My Products') }}
			</h2>
		</div>
	</x-slot>

	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
			@if (session('status'))
				<div class="mb-4 px-4 py-3 bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 rounded-md">
					{{ session('status') }}
				</div>
			@endif

			<!-- Search Form -->
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 mb-4">
				<form method="GET" action="{{ route('media-buyer.products') }}" class="flex gap-2">
					<input 
						type="text" 
						name="q" 
						value="{{ request('q') }}" 
						placeholder="Search by product name..." 
						class="flex-1 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600"
					>
					<button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
						{{ __('Search') }}
					</button>
					@if(request('q'))
						<a href="{{ route('media-buyer.products') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition">
							{{ __('Clear') }}
						</a>
					@endif
				</form>
			</div>

			@if($products->isEmpty())
				<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-8 text-center">
					<svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
					</svg>
					<h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('No products assigned') }}</h3>
					<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Contact your administrator to get products assigned to you.') }}</p>
				</div>
			@else
				<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
					<div class="overflow-x-auto">
						<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
							<thead class="bg-gray-50 dark:bg-gray-900">
								<tr>
									<th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
										{{ __('Image') }}
									</th>
									<th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
										{{ __('Product') }}
									</th>
									<th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
										{{ __('Category') }}
									</th>
									<th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
										{{ __('Country') }}
									</th>
									<th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
										{{ __('Remaining Qty') }}
									</th>
									<th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
										{{ __('Cost Total') }}
									</th>
									<th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
										{{ __('My Leads') }}
									</th>
									<th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
										{{ __('Ads Cost Total') }}
									</th>
									<th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
										{{ __('Cost Per Lead') }}
									</th>
									<th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
										{{ __('Cost Per Delivered') }}
									</th>
									<th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
										{{ __('Delivery Rate') }}
									</th>
								</tr>
							</thead>
							<tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
								@foreach($products as $product)
									@php
										// Get campaigns for this media buyer and product
										$userCampaigns = \App\Models\AdsCampaign::where('user_id', auth()->id())
											->whereHas('products', function($q) use ($product) {
												$q->where('products.id', $product->id);
											})
											->with('products')
											->get();
										
										// Calculate metrics from this user's campaigns for this product
										$myLeads = 0;
										$myAdsCostTotal = 0;
										
										foreach ($userCampaigns as $campaign) {
											$productInCampaign = $campaign->products->where('id', $product->id)->first();
											if ($productInCampaign) {
												$myLeads += $productInCampaign->pivot->leads ?? 0;
												$myAdsCostTotal += $productInCampaign->pivot->amount_spent ?? 0;
											}
										}
										
										// Calculate Cost Per Lead (my data)
										$myCostPerLead = $myLeads > 0 ? $myAdsCostTotal / $myLeads : 0;
										
										// Get orders from invoices for this product (global - we'll use the product's total orders)
										$totalOrders = $product->total_orders ?? 0;
										
										// Calculate Cost Per Delivered (my data)
										$myCostPerDelivered = $totalOrders > 0 ? $myAdsCostTotal / $totalOrders : 0;
										
										// Get delivery rate from product (this is global)
										$deliveryRate = $product->delivery_rate ?? 0;
									@endphp
									<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
										<td class="px-3 py-3 whitespace-nowrap">
											@if($product->image)
												<img src="{{ \Illuminate\Support\Facades\Storage::url($product->image) }}" alt="{{ $product->name }}" class="h-10 w-10 object-cover rounded-md">
											@else
												<div class="h-10 w-10 bg-gray-200 dark:bg-gray-700 rounded-md flex items-center justify-center">
													<svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
													</svg>
												</div>
											@endif
										</td>
										<td class="px-3 py-3 whitespace-nowrap">
											<div class="font-medium text-gray-900 dark:text-gray-100">
												{{ $product->name }}
											</div>
										</td>
										<td class="px-3 py-3 whitespace-nowrap">
											<div class="text-gray-500 dark:text-gray-400">
												{{ $product->category?->name ?? '-' }}
											</div>
										</td>
										<td class="px-3 py-3 whitespace-nowrap">
											<div class="text-gray-500 dark:text-gray-400">
												{{ $product->country?->name ?? '-' }}
											</div>
										</td>
										<td class="px-3 py-3 whitespace-nowrap">
											<div class="text-gray-900 dark:text-gray-100">
												{{ $product->remaining_qty ?? 0 }}
											</div>
										</td>
										<td class="px-3 py-3 whitespace-nowrap">
											<div class="font-semibold text-gray-900 dark:text-gray-100">
												{{ $product->pivot->cost_total ? number_format($product->pivot->cost_total, 2) : '-' }}
											</div>
										</td>
										<td class="px-3 py-3 whitespace-nowrap">
											<div class="text-gray-900 dark:text-gray-100">
												{{ number_format($myLeads) }}
											</div>
										</td>
										<td class="px-3 py-3 whitespace-nowrap">
											<div class="font-semibold text-indigo-600 dark:text-indigo-400">
												{{ number_format($myAdsCostTotal, 2) }}
											</div>
										</td>
										<td class="px-3 py-3 whitespace-nowrap">
											<div class="text-gray-900 dark:text-gray-100">
												{{ number_format($myCostPerLead, 2) }}
											</div>
										</td>
										<td class="px-3 py-3 whitespace-nowrap">
											<div class="text-gray-900 dark:text-gray-100">
												{{ number_format($myCostPerDelivered, 2) }}
											</div>
										</td>
										<td class="px-3 py-3 whitespace-nowrap">
											@php
												$bgColorClass = '';
												if ($deliveryRate >= 20) {
													$bgColorClass = 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
												} elseif ($deliveryRate >= 15 && $deliveryRate <= 19) {
													$bgColorClass = 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200';
												} elseif ($deliveryRate < 15) {
													$bgColorClass = 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
												}
											@endphp
											<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $bgColorClass }}">
												{{ number_format($deliveryRate, 2) }}%
											</span>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>

				@if($products->hasPages())
					<div class="mt-6">
						{{ $products->links() }}
					</div>
				@endif
			@endif
		</div>
	</div>
</x-media-buyer-layout>

