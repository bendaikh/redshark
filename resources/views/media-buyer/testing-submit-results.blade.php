<x-media-buyer-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Submit Testing Results') }}
		</h2>
	</x-slot>

	<div class="py-12">
		<div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
				<div class="p-6 border-b border-gray-200 dark:border-gray-700">
					<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $testingProduct->product_name }}</h3>
					<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('Enter your testing results below') }}</p>
				</div>

				<form action="{{ route('media-buyer.testing.store-results', $testingProduct) }}" method="POST" class="p-6 space-y-6">
					@csrf

					<!-- Product Details -->
					<div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
						<h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-3">{{ __('Product Information') }}</h4>
						<div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
							@if($testingProduct->link)
								<div>
									<span class="text-gray-600 dark:text-gray-400">{{ __('Link:') }}</span>
									<a href="{{ $testingProduct->link }}" target="_blank" class="ml-2 text-blue-600 dark:text-blue-400 hover:underline">
										{{ __('View Product') }}
									</a>
								</div>
							@endif
							@if($testingProduct->description)
								<div class="col-span-2">
									<span class="text-gray-600 dark:text-gray-400">{{ __('Description:') }}</span>
									<p class="text-gray-900 dark:text-gray-100 mt-1">{{ $testingProduct->description }}</p>
								</div>
							@endif
						</div>
					</div>

					<!-- Leads Input -->
					<div>
						<label for="leads" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
							{{ __('Number of Leads') }} <span class="text-red-500">*</span>
						</label>
						<input 
							type="number" 
							id="leads" 
							name="leads" 
							value="{{ old('leads', $pivot->pivot->leads ?? '') }}" 
							min="0"
							required
							class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600"
						>
						@error('leads')
							<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
						@enderror
					</div>

					<!-- Ads Spend Input -->
					<div>
						<label for="ads_spend" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
							{{ __('Total Ads Spend') }} <span class="text-red-500">*</span>
						</label>
						<input 
							type="number" 
							id="ads_spend" 
							name="ads_spend" 
							value="{{ old('ads_spend', $pivot->pivot->ads_spend ?? '') }}" 
							step="0.01"
							min="0"
							required
							class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600"
						>
						@error('ads_spend')
							<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
						@enderror
					</div>

					<!-- Current Status Info -->
					@if(in_array($pivot->pivot->status, ['done', 'rejected']))
						<div class="p-4 {{ $pivot->pivot->status == 'rejected' ? 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700' : 'bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700' }} rounded-lg">
							<p class="text-sm {{ $pivot->pivot->status == 'rejected' ? 'text-red-800 dark:text-red-200' : 'text-blue-800 dark:text-blue-200' }}">
								@if($pivot->pivot->status == 'rejected')
									<strong>{{ __('Note:') }}</strong> {{ __('Your previous submission was rejected. Please review and resubmit your results.') }}
								@else
									<strong>{{ __('Note:') }}</strong> {{ __('You are updating previously submitted results.') }}
								@endif
							</p>
						</div>
					@endif

					<!-- Action Buttons -->
					<div class="flex gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
						<a href="{{ route('media-buyer.testing') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
							{{ __('Cancel') }}
						</a>
						<button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
							{{ __('Submit Results') }}
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</x-media-buyer-layout>

