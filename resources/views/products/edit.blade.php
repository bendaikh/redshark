<x-admin-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Edit Product') }} - {{ $product->name }}
		</h2>
	</x-slot>
	<div class="py-4 sm:py-6">
		<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl p-4 sm:p-6">
				<form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data" class="space-y-5">
					@csrf @method('PUT')
					<div>
						<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Name') }}</label>
						<input name="name" value="{{ old('name', $product->name) }}" class="w-full py-2.5 px-3 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
					</div>
					<div>
						<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Product Image') }}</label>
						@if($product->image)
							<div class="mb-3">
								<img src="{{ \Illuminate\Support\Facades\Storage::url($product->image) }}" alt="{{ $product->name }}" class="h-20 w-20 object-cover rounded-lg">
							</div>
						@endif
						<input type="file" name="image" accept="image/*" class="w-full py-2 px-3 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/50 dark:file:text-indigo-300">
						<p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">{{ __('Max size: 2MB. Formats: JPEG, PNG, JPG, GIF') }}</p>
					</div>
					<div>
						<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Category') }}</label>
						<select name="category_id" class="w-full py-2.5 px-3 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
							<option value="">{{ __('None') }}</option>
							@foreach($categories as $category)
								<option value="{{ $category->id }}" {{ $product->category_id === $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
							@endforeach
						</select>
					</div>
					<div>
						<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Country') }}</label>
						<select name="country_id" class="w-full py-2.5 px-3 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
							@foreach($countries as $country)
								<option value="{{ $country->id }}" {{ $product->country_id === $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
							@endforeach
						</select>
					</div>
					<div>
						<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Low Stock Threshold') }}</label>
						<input type="number" name="low_stock_threshold" min="0" value="{{ old('low_stock_threshold', $product->low_stock_threshold) }}" class="w-full py-2.5 px-3 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
					</div>
					<div class="pt-4 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
						<a href="{{ route('products.index') }}" class="w-full sm:w-auto px-6 py-2.5 text-center text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors touch-manipulation">{{ __('Cancel') }}</a>
						<button class="w-full sm:w-auto px-6 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 active:bg-indigo-800 transition-colors touch-manipulation">{{ __('Save') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</x-admin-layout>
