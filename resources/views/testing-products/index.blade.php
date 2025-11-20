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

			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
				<div class="overflow-x-auto">
					<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
						<thead class="bg-gray-50 dark:bg-gray-900">
							<tr>
								<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
									{{ __('Product Name') }}
								</th>
								<th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
									{{ __('Product Link') }}
								</th>
								<th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
									{{ __('FB Library') }}
								</th>
								<th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
									{{ __('Video') }}
								</th>
								<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
									{{ __('Countries') }}
								</th>
								<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
									{{ __('Created') }}
								</th>
								<th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
									{{ __('Actions') }}
								</th>
							</tr>
						</thead>
						<tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
							@forelse($testingProducts as $testingProduct)
								<tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
									<td class="px-6 py-4 whitespace-nowrap">
										<div class="text-sm font-medium text-gray-900 dark:text-gray-100">
											{{ $testingProduct->product_name }}
										</div>
									</td>
									<td class="px-6 py-4 text-center">
										<a href="{{ $testingProduct->product_link }}" target="_blank" class="inline-flex items-center justify-center p-2 text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-md transition" title="{{ $testingProduct->product_link }}">
											<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
											</svg>
										</a>
									</td>
									<td class="px-6 py-4 text-center">
										@if($testingProduct->facebook_library_link)
											<a href="{{ $testingProduct->facebook_library_link }}" target="_blank" class="inline-flex items-center justify-center p-2 text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-md transition" title="{{ $testingProduct->facebook_library_link }}">
												<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
													<path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
												</svg>
											</a>
										@else
											<span class="text-gray-400 dark:text-gray-600">—</span>
										@endif
									</td>
									<td class="px-6 py-4 text-center">
										@if($testingProduct->video_url)
											<a href="{{ $testingProduct->video_url }}" target="_blank" class="inline-flex items-center justify-center p-2 text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-md transition" title="{{ $testingProduct->video_url }}">
												<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
													<path d="M8 5v14l11-7z"/>
												</svg>
											</a>
										@else
											<span class="text-gray-400 dark:text-gray-600">—</span>
										@endif
									</td>
									<td class="px-6 py-4">
										<div class="flex flex-wrap gap-1">
											@foreach($testingProduct->countries() as $country)
												<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
													{{ $country->name }}
												</span>
											@endforeach
											@if($testingProduct->countries()->isEmpty())
												<span class="text-sm text-gray-500 dark:text-gray-400">{{ __('N/A') }}</span>
											@endif
										</div>
									</td>
									<td class="px-6 py-4 whitespace-nowrap">
										<div class="text-sm text-gray-500 dark:text-gray-400">
											{{ $testingProduct->created_at->format('M d, Y') }}
										</div>
									</td>
									<td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
										<div class="flex items-center justify-end gap-2">
											<a href="{{ route('testing-products.edit', $testingProduct) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
												{{ __('Edit') }}
											</a>
											<form method="POST" action="{{ route('testing-products.destroy', $testingProduct) }}" onsubmit="return confirm('Are you sure you want to delete this testing product?');" class="inline">
												@csrf
												@method('DELETE')
												<button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">
													{{ __('Delete') }}
												</button>
											</form>
										</div>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
										{{ __('No testing products found.') }}
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>

				@if($testingProducts->hasPages())
					<div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
						{{ $testingProducts->links() }}
					</div>
				@endif
			</div>
		</div>
	</div>
</x-admin-layout>

