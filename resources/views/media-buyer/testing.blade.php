<x-media-buyer-layout>
	<x-slot name="header">
		<div class="flex items-center justify-between">
			<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
				{{ __('My Testing Products') }}
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
				<form method="GET" action="{{ route('media-buyer.testing') }}" class="flex gap-2">
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
						<a href="{{ route('media-buyer.testing') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition">
							{{ __('Clear') }}
						</a>
					@endif
				</form>
			</div>

			@if($testingProducts->isEmpty())
				<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-8 text-center">
					<svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
					</svg>
					<h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('No testing products assigned') }}</h3>
					<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Contact your administrator to get testing products assigned to you.') }}</p>
				</div>
			@else
				<div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
					@foreach($testingProducts as $testingProduct)
						<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
							<div class="p-6">
								<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">
									{{ $testingProduct->product_name }}
								</h3>
								
								<div class="space-y-3">
									<!-- Product Link -->
									<div>
										<label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
											{{ __('Product Link') }}
										</label>
										<a href="{{ $testingProduct->product_link }}" target="_blank" class="mt-1 flex items-center gap-2 text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">
											<span class="truncate">{{ $testingProduct->product_link }}</span>
											<svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
											</svg>
										</a>
									</div>

									<!-- Facebook Library Link -->
									@if($testingProduct->facebook_library_link)
										<div>
											<label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
												{{ __('Facebook Ad Library') }}
											</label>
											<a href="{{ $testingProduct->facebook_library_link }}" target="_blank" class="mt-1 flex items-center gap-2 text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
												<svg class="h-5 w-5 shrink-0" fill="currentColor" viewBox="0 0 24 24">
													<path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
												</svg>
												<span class="truncate">{{ __('View Ads') }}</span>
											</a>
										</div>
									@endif

									<!-- Video URL -->
									@if($testingProduct->video_url)
										<div>
											<label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
												{{ __('Video') }}
											</label>
											<a href="{{ $testingProduct->video_url }}" target="_blank" class="mt-1 flex items-center gap-2 text-sm text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300">
												<svg class="h-5 w-5 shrink-0" fill="currentColor" viewBox="0 0 24 24">
													<path d="M8 5v14l11-7z"/>
												</svg>
												<span class="truncate">{{ __('Watch Video') }}</span>
											</a>
										</div>
									@endif

									<!-- Countries -->
									@if($testingProduct->countries()->isNotEmpty())
										<div>
											<label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider block mb-1">
												{{ __('Target Countries') }}
											</label>
											<div class="flex flex-wrap gap-1">
												@foreach($testingProduct->countries() as $country)
													<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
														{{ $country->name }}
													</span>
												@endforeach
											</div>
										</div>
									@endif
								</div>

								<div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
									<div class="text-xs text-gray-500 dark:text-gray-400">
										{{ __('Added') }} {{ $testingProduct->created_at->diffForHumans() }}
									</div>
								</div>
							</div>
						</div>
					@endforeach
				</div>

				@if($testingProducts->hasPages())
					<div class="mt-6">
						{{ $testingProducts->links() }}
					</div>
				@endif
			@endif
		</div>
	</div>
</x-media-buyer-layout>

