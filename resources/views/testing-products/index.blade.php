<x-admin-layout>
	<x-slot name="header">
		<div class="flex items-center justify-between">
			<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
				{{ __('Testing Products') }}
			</h2>
			<a href="{{ route('testing-products.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
				{{ __('Add Testing Product') }}
			</a>
		</div>
	</x-slot>

	<div class="py-6">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
			@if (session('status'))
				<div class="mb-4 px-4 py-3 bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 rounded-md">
					{{ session('status') }}
				</div>
			@endif

			@if (session('error'))
				<div class="mb-4 px-4 py-3 bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 rounded-md">
					{{ session('error') }}
				</div>
			@endif

			<!-- Search Form -->
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 mb-4">
				<form method="GET" action="{{ route('testing-products.index') }}" class="flex gap-2">
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
						<a href="{{ route('testing-products.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition">
							{{ __('Clear') }}
						</a>
					@endif
				</form>
			</div>

			<div class="space-y-4">
				@forelse($testingProducts as $testingProduct)
					<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
						<!-- Product Header -->
						<div class="p-4 bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
							<div class="flex items-center justify-between">
								<div class="flex-1">
									<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $testingProduct->product_name }}</h3>
									<div class="flex gap-4 mt-2 text-sm text-gray-500 dark:text-gray-400">
										<span>{{ __('Created') }}: {{ $testingProduct->created_at->format('M d, Y') }}</span>
										<a href="{{ $testingProduct->product_link }}" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ __('View Product') }}</a>
										@if($testingProduct->facebook_library_link)
											<a href="{{ $testingProduct->facebook_library_link }}" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline">{{ __('FB Library') }}</a>
										@endif
										@if($testingProduct->video_url)
											<a href="{{ $testingProduct->video_url }}" target="_blank" class="text-red-600 dark:text-red-400 hover:underline">{{ __('Video') }}</a>
										@endif
									</div>
								</div>
								<div class="flex items-center gap-2">
									<a href="{{ route('testing-products.assign-media-buyers', $testingProduct) }}" class="px-3 py-1.5 text-sm bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-md hover:bg-blue-200 dark:hover:bg-blue-900/50 transition">
										{{ __('Assign') }}
									</a>
									<a href="{{ route('testing-products.edit', $testingProduct) }}" class="px-3 py-1.5 text-sm bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-md hover:bg-indigo-200 dark:hover:bg-indigo-900/50 transition">
										{{ __('Edit') }}
									</a>
									<form method="POST" action="{{ route('testing-products.destroy', $testingProduct) }}" onsubmit="return confirm('Are you sure?');" class="inline">
										@csrf
										@method('DELETE')
										<button type="submit" class="px-3 py-1.5 text-sm bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-md hover:bg-red-200 dark:hover:bg-red-900/50 transition">
											{{ __('Delete') }}
										</button>
									</form>
								</div>
							</div>
						</div>

						<!-- Media Buyers Results -->
						@if($testingProduct->mediaBuyers->isNotEmpty())
							<div class="overflow-x-auto">
								<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
									<thead class="bg-gray-100 dark:bg-gray-800">
										<tr>
											<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Media Buyer') }}</th>
											<th class="px-4 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Status') }}</th>
											<th class="px-4 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Leads') }}</th>
											<th class="px-4 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Ads Spend') }}</th>
											<th class="px-4 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Cost/Lead') }}</th>
											<th class="px-4 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Actions') }}</th>
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
																<button type="submit" class="px-2 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700 transition">
																	{{ __('Approve') }}
																</button>
															</form>
															<form action="{{ route('testing-products.reject', [$testingProduct, $mediaBuyer]) }}" method="POST" class="inline">
																@csrf
																@method('PATCH')
																<button type="submit" class="px-2 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 transition">
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
							<div class="p-4 text-center text-sm text-gray-500 dark:text-gray-400">
								{{ __('No media buyers assigned yet.') }}
								<a href="{{ route('testing-products.assign-media-buyers', $testingProduct) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline ml-1">
									{{ __('Assign now') }}
								</a>
							</div>
						@endif
					</div>
				@empty
					<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-8 text-center">
						<p class="text-gray-500 dark:text-gray-400">{{ __('No testing products found.') }}</p>
					</div>
				@endforelse
			</div>

			@if($testingProducts->hasPages())
				<div class="mt-4">
					{{ $testingProducts->links() }}
				</div>
			@endif
		</div>
	</div>
</x-admin-layout>

