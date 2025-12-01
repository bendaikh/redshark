<x-admin-layout>
	<x-slot name="header">
		<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
			<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
				{{ __('Testing Products') }}
			</h2>
			<a href="{{ route('testing-products.create') }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-indigo-600 border border-transparent rounded-xl font-semibold text-sm text-white hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 touch-manipulation shadow-lg shadow-indigo-500/25">
				<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
				{{ __('Add Testing Product') }}
			</a>
		</div>
	</x-slot>

	<div class="py-4 sm:py-6">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
			@if (session('status'))
				<div class="mb-4 px-4 py-3 bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 rounded-lg text-sm">
					{{ session('status') }}
				</div>
			@endif

			@if (session('error'))
				<div class="mb-4 px-4 py-3 bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 rounded-lg text-sm">
					{{ session('error') }}
				</div>
			@endif

			<!-- Search Form -->
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl p-4 mb-4">
				<form method="GET" action="{{ route('testing-products.index') }}" class="flex flex-col sm:flex-row gap-2">
					<input 
						type="text" 
						name="q" 
						value="{{ request('q') }}" 
						placeholder="{{ __('Search by product name...') }}" 
						class="flex-1 py-2.5 px-3 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600"
					>
					<div class="flex gap-2">
						<button type="submit" class="flex-1 sm:flex-none px-5 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition touch-manipulation">
							{{ __('Search') }}
						</button>
						@if(request('q'))
							<a href="{{ route('testing-products.index') }}" class="flex-1 sm:flex-none px-5 py-2.5 bg-gray-500 text-white font-medium rounded-lg hover:bg-gray-600 transition text-center touch-manipulation">
								{{ __('Clear') }}
							</a>
						@endif
					</div>
				</form>
			</div>

			<div class="space-y-4">
				@forelse($testingProducts as $testingProduct)
					<div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl overflow-hidden">
						<!-- Product Header -->
						<div class="p-4 bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
							<div class="flex flex-col gap-3">
								<!-- Product Name & Created Date -->
								<div>
									<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $testingProduct->product_name }}</h3>
									<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('Created') }}: {{ $testingProduct->created_at->format('M d, Y') }}</p>
								</div>
								
								<!-- Quick Links -->
								<div class="flex flex-wrap gap-2">
									<a href="{{ $testingProduct->product_link }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition touch-manipulation">
										<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
										{{ __('View Product') }}
									</a>
									@if($testingProduct->facebook_library_link)
										<a href="{{ $testingProduct->facebook_library_link }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/50 transition touch-manipulation">
											<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
											{{ __('FB Library') }}
										</a>
									@endif
									@if($testingProduct->video_url)
										<a href="{{ $testingProduct->video_url }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/50 transition touch-manipulation">
											<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
											{{ __('Video') }}
										</a>
									@endif
								</div>
								
								<!-- Action Buttons -->
								<div class="flex flex-wrap gap-2 pt-2 border-t border-gray-200 dark:border-gray-700">
									<a href="{{ route('testing-products.assign-media-buyers', $testingProduct) }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition touch-manipulation">
										<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
										{{ __('Assign') }}
									</a>
									<a href="{{ route('testing-products.edit', $testingProduct) }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition touch-manipulation">
										<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
										{{ __('Edit') }}
									</a>
									<form method="POST" action="{{ route('testing-products.destroy', $testingProduct) }}" onsubmit="return confirm('{{ __('Are you sure?') }}');" class="inline">
										@csrf
										@method('DELETE')
										<button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium bg-red-600 text-white rounded-lg hover:bg-red-700 transition touch-manipulation">
											<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
											{{ __('Delete') }}
										</button>
									</form>
								</div>
							</div>
						</div>

						<!-- Media Buyers Results -->
						@if($testingProduct->mediaBuyers->isNotEmpty())
							<!-- Mobile Card Layout -->
							<div class="block lg:hidden divide-y divide-gray-100 dark:divide-gray-700">
								@foreach($testingProduct->mediaBuyers as $mediaBuyer)
									@php
										$status = $mediaBuyer->pivot->status ?? 'to_do';
										$statusColors = [
											'to_do' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
											'in_progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
											'done' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
											'approved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
											'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
										];
										$statusLabels = [
											'to_do' => __('To Do'),
											'in_progress' => __('In Progress'),
											'done' => __('Done'),
											'approved' => __('Approved'),
											'rejected' => __('Rejected'),
										];
										$leads = $mediaBuyer->pivot->leads ?? 0;
										$adsSpend = $mediaBuyer->pivot->ads_spend ?? 0;
										$costPerLead = $leads > 0 ? $adsSpend / $leads : 0;
									@endphp
									<div class="p-4">
										<!-- Media Buyer Info -->
										<div class="flex items-start justify-between mb-3">
											<div>
												<p class="font-medium text-gray-900 dark:text-gray-100">{{ $mediaBuyer->name }}</p>
												<p class="text-sm text-gray-500 dark:text-gray-400">{{ $mediaBuyer->email }}</p>
											</div>
											<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $statusColors[$status] }}">
												{{ $statusLabels[$status] }}
											</span>
										</div>
										
										<!-- Stats Grid -->
										<div class="grid grid-cols-3 gap-2 mb-3">
											<div class="text-center p-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
												<p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Leads') }}</p>
												<p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $leads > 0 ? number_format($leads) : '—' }}</p>
											</div>
											<div class="text-center p-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
												<p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Ads Spend') }}</p>
												<p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $adsSpend > 0 ? number_format($adsSpend, 2) : '—' }}</p>
											</div>
											<div class="text-center p-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
												<p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Cost/Lead') }}</p>
												<p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $costPerLead > 0 ? number_format($costPerLead, 2) : '—' }}</p>
											</div>
										</div>
										
										<!-- Actions -->
										@if($status == 'done')
											<div class="flex gap-2">
												<form action="{{ route('testing-products.approve', [$testingProduct, $mediaBuyer]) }}" method="POST" class="flex-1">
													@csrf
													@method('PATCH')
													<button type="submit" class="w-full py-2.5 text-sm font-medium bg-green-600 text-white rounded-lg hover:bg-green-700 transition touch-manipulation">
														{{ __('Approve') }}
													</button>
												</form>
												<form action="{{ route('testing-products.reject', [$testingProduct, $mediaBuyer]) }}" method="POST" class="flex-1">
													@csrf
													@method('PATCH')
													<button type="submit" class="w-full py-2.5 text-sm font-medium bg-red-600 text-white rounded-lg hover:bg-red-700 transition touch-manipulation">
														{{ __('Reject') }}
													</button>
												</form>
											</div>
										@elseif($status == 'approved')
											<div class="text-center py-2 bg-green-50 dark:bg-green-900/20 rounded-lg">
												<span class="text-sm text-green-600 dark:text-green-400 font-medium">✓ {{ __('Approved') }}</span>
											</div>
										@elseif($status == 'rejected')
											<div class="text-center py-2 bg-red-50 dark:bg-red-900/20 rounded-lg">
												<span class="text-sm text-red-600 dark:text-red-400 font-medium">✗ {{ __('Rejected') }}</span>
											</div>
										@else
											<div class="text-center py-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
												<span class="text-sm text-gray-500 dark:text-gray-400">{{ __('Pending review') }}</span>
											</div>
										@endif
									</div>
								@endforeach
							</div>
							
							<!-- Desktop Table Layout -->
							<div class="hidden lg:block overflow-x-auto">
								<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
									<thead class="bg-gray-100 dark:bg-gray-800">
										<tr>
											<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Media Buyer') }}</th>
											<th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Status') }}</th>
											<th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Leads') }}</th>
											<th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Ads Spend') }}</th>
											<th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Cost/Lead') }}</th>
											<th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Actions') }}</th>
										</tr>
									</thead>
									<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
										@foreach($testingProduct->mediaBuyers as $mediaBuyer)
											@php
												$status = $mediaBuyer->pivot->status ?? 'to_do';
												$statusColors = [
													'to_do' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
													'in_progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
													'done' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
													'approved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
													'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
												];
												$statusLabels = [
													'to_do' => __('To Do'),
													'in_progress' => __('In Progress'),
													'done' => __('Done'),
													'approved' => __('Approved'),
													'rejected' => __('Rejected'),
												];
												$leads = $mediaBuyer->pivot->leads ?? 0;
												$adsSpend = $mediaBuyer->pivot->ads_spend ?? 0;
												$costPerLead = $leads > 0 ? $adsSpend / $leads : 0;
											@endphp
											<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
												<td class="px-4 py-3">
													<div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $mediaBuyer->name }}</div>
													<div class="text-xs text-gray-500 dark:text-gray-400">{{ $mediaBuyer->email }}</div>
												</td>
												<td class="px-4 py-3 text-center">
													<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$status] }}">
														{{ $statusLabels[$status] }}
													</span>
												</td>
												<td class="px-4 py-3 text-center text-sm text-gray-900 dark:text-gray-100">
													{{ $leads > 0 ? number_format($leads) : '—' }}
												</td>
												<td class="px-4 py-3 text-center text-sm text-gray-900 dark:text-gray-100">
													{{ $adsSpend > 0 ? number_format($adsSpend, 2) : '—' }}
												</td>
												<td class="px-4 py-3 text-center text-sm text-gray-900 dark:text-gray-100">
													{{ $costPerLead > 0 ? number_format($costPerLead, 2) : '—' }}
												</td>
												<td class="px-4 py-3 text-center">
													@if($status == 'done')
														<div class="flex items-center justify-center gap-2">
															<form action="{{ route('testing-products.approve', [$testingProduct, $mediaBuyer]) }}" method="POST" class="inline">
																@csrf
																@method('PATCH')
																<button type="submit" class="px-3 py-1.5 text-xs font-medium bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
																	{{ __('Approve') }}
																</button>
															</form>
															<form action="{{ route('testing-products.reject', [$testingProduct, $mediaBuyer]) }}" method="POST" class="inline">
																@csrf
																@method('PATCH')
																<button type="submit" class="px-3 py-1.5 text-xs font-medium bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
																	{{ __('Reject') }}
																</button>
															</form>
														</div>
													@elseif($status == 'approved')
														<span class="text-xs text-green-600 dark:text-green-400 font-medium">✓ {{ __('Approved') }}</span>
													@elseif($status == 'rejected')
														<span class="text-xs text-red-600 dark:text-red-400 font-medium">✗ {{ __('Rejected') }}</span>
													@else
														<span class="text-xs text-gray-400 dark:text-gray-500">{{ __('Pending') }}</span>
													@endif
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						@else
							<div class="p-6 text-center">
								<div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-700 mb-3">
									<svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
								</div>
								<p class="text-sm text-gray-500 dark:text-gray-400 mb-2">{{ __('No media buyers assigned yet.') }}</p>
								<a href="{{ route('testing-products.assign-media-buyers', $testingProduct) }}" class="inline-flex items-center gap-1.5 text-sm text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
									<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
									{{ __('Assign now') }}
								</a>
							</div>
						@endif
					</div>
				@empty
					<div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl p-8 text-center">
						<div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 mb-4">
							<svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
						</div>
						<p class="text-gray-500 dark:text-gray-400">{{ __('No testing products found.') }}</p>
					</div>
				@endforelse
			</div>

			@if($testingProducts->hasPages())
				<div class="mt-6">
					{{ $testingProducts->links() }}
				</div>
			@endif
		</div>
	</div>
</x-admin-layout>

