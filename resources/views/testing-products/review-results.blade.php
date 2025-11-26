<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Review Testing Results') }}
		</h2>
	</x-slot>

	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
			
			<!-- Success/Error Messages -->
			@if (session('status'))
				<div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-200 rounded">
					{{ session('status') }}
				</div>
			@endif

			@if (session('error'))
				<div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-200 rounded">
					{{ session('error') }}
				</div>
			@endif
			
			@if($testingProducts->isEmpty())
				<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-8 text-center">
					<svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
					</svg>
					<h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('No results to review') }}</h3>
					<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('No media buyers have submitted testing results yet.') }}</p>
					<div class="mt-6">
						<a href="{{ route('testing-products.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
							{{ __('Back to Testing Products') }}
						</a>
					</div>
				</div>
			@else
				<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
					<div class="p-6 border-b border-gray-200 dark:border-gray-700">
						<div class="flex items-center justify-between">
							<div>
								<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Submitted Results') }}</h3>
								<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('Review and approve or reject testing results from media buyers') }}</p>
							</div>
							<a href="{{ route('testing-products.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition">
								{{ __('Back') }}
							</a>
						</div>
					</div>

					<div class="divide-y divide-gray-200 dark:divide-gray-700">
						@foreach($testingProducts as $testingProduct)
							<div class="p-6">
								<h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ $testingProduct->product_name }}</h4>
								
								<div class="space-y-4">
									@foreach($testingProduct->mediaBuyers as $mediaBuyer)
										<div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-900/50">
											<div class="flex items-start justify-between mb-3">
												<div class="flex items-center gap-3">
													<div class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
														<span class="text-indigo-600 dark:text-indigo-400 font-semibold text-sm">
															{{ strtoupper(substr($mediaBuyer->name, 0, 2)) }}
														</span>
													</div>
													<div>
														<p class="font-medium text-gray-900 dark:text-gray-100">{{ $mediaBuyer->name }}</p>
														<p class="text-xs text-gray-500 dark:text-gray-400">{{ $mediaBuyer->email }}</p>
													</div>
												</div>
												<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
													{{ __('Pending Review') }}
												</span>
											</div>

											<div class="grid grid-cols-2 gap-4 mb-4 p-3 bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700">
												<div>
													<p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('Leads Generated') }}</p>
													<p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($mediaBuyer->pivot->leads ?? 0) }}</p>
												</div>
												<div>
													<p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('Ads Spent') }}</p>
													<p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($mediaBuyer->pivot->ads_spend ?? 0, 2) }}</p>
												</div>
												<div class="col-span-2">
													<p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('Cost Per Lead') }}</p>
													<p class="text-xl font-semibold text-indigo-600 dark:text-indigo-400">
														@if($mediaBuyer->pivot->leads > 0)
															{{ number_format($mediaBuyer->pivot->ads_spend / $mediaBuyer->pivot->leads, 2) }}
														@else
															N/A
														@endif
													</p>
												</div>
											</div>

											<div class="flex gap-2">
												<form action="{{ route('testing-products.approve', [$testingProduct, $mediaBuyer]) }}" method="POST" class="flex-1">
													@csrf
													@method('PATCH')
													<button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition flex items-center justify-center gap-2">
														<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
														</svg>
														{{ __('Approve') }}
													</button>
												</form>
												<form action="{{ route('testing-products.reject', [$testingProduct, $mediaBuyer]) }}" method="POST" class="flex-1">
													@csrf
													@method('PATCH')
													<button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition flex items-center justify-center gap-2">
														<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
														</svg>
														{{ __('Reject') }}
													</button>
												</form>
											</div>
										</div>
									@endforeach
								</div>
							</div>
						@endforeach
					</div>
				</div>
			@endif
		</div>
	</div>
</x-app-layout>

