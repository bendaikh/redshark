<x-admin-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Create Testing Product') }}
		</h2>
	</x-slot>

	<div class="py-6">
		<div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
				<form method="POST" action="{{ route('testing-products.store') }}" class="space-y-6">
					@csrf

					<!-- Product Name -->
					<div>
						<label for="product_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
							{{ __('Product Name') }} <span class="text-red-500">*</span>
						</label>
						<input 
							type="text" 
							name="product_name" 
							id="product_name" 
							value="{{ old('product_name') }}"
							class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600" 
							required
						>
						@error('product_name')
							<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
						@enderror
					</div>

					<!-- Product Link -->
					<div>
						<label for="product_link" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
							{{ __('Product Link') }} <span class="text-red-500">*</span>
						</label>
						<input 
							type="url" 
							name="product_link" 
							id="product_link" 
							value="{{ old('product_link') }}"
							placeholder="https://example.com/product"
							class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600" 
							required
						>
						@error('product_link')
							<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
						@enderror
					</div>

					<!-- Facebook Library Link -->
					<div>
						<label for="facebook_library_link" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
							{{ __('Facebook Library Link') }}
						</label>
						<input 
							type="url" 
							name="facebook_library_link" 
							id="facebook_library_link" 
							value="{{ old('facebook_library_link') }}"
							placeholder="https://facebook.com/ads/library/..."
							class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600"
						>
						@error('facebook_library_link')
							<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
						@enderror
					</div>

					<!-- Country Selection (Multiple) -->
					<div>
						<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
							{{ __('Countries') }}
						</label>
						<div class="mt-2 space-y-2 max-h-60 overflow-y-auto border border-gray-300 dark:border-gray-700 rounded-md p-4">
							@foreach($countries as $country)
								<div class="flex items-center">
									<input 
										type="checkbox" 
										name="country_ids[]" 
										id="country_{{ $country->id }}" 
										value="{{ $country->id }}"
										{{ in_array($country->id, old('country_ids', [])) ? 'checked' : '' }}
										class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 border-gray-300 dark:border-gray-700 rounded"
									>
									<label for="country_{{ $country->id }}" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
										{{ $country->name }}
									</label>
								</div>
							@endforeach
						</div>
						@error('country_ids')
							<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
						@enderror
						@error('country_ids.*')
							<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
						@enderror
					</div>

					<!-- Video URL -->
					<div>
						<label for="video_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
							{{ __('Video URL') }}
						</label>
						<input 
							type="url" 
							name="video_url" 
							id="video_url" 
							value="{{ old('video_url') }}"
							placeholder="https://youtube.com/watch?v=..."
							class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600"
						>
						@error('video_url')
							<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
						@enderror
					</div>

					<!-- Form Actions -->
					<div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
						<a href="{{ route('testing-products.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-600 transition ease-in-out duration-150">
							{{ __('Cancel') }}
						</a>
						<button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
							{{ __('Create Testing Product') }}
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</x-admin-layout>

