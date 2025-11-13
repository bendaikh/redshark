<x-admin-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Edit Product') }} - {{ $product->name }}
		</h2>
	</x-slot>
	<div class="py-6">
		<div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
				<form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data" class="space-y-4">
					@csrf @method('PUT')
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Name') }}</label>
						<input name="name" value="{{ old('name', $product->name) }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100" required>
					</div>
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Product Image') }}</label>
						@if($product->image)
							<div class="mb-2">
								<img src="{{ \Illuminate\Support\Facades\Storage::url($product->image) }}" alt="{{ $product->name }}" class="h-20 w-20 object-cover rounded">
							</div>
						@endif
						<input type="file" name="image" accept="image/*" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100">
						<p class="mt-1 text-xs text-gray-500">{{ __('Max size: 2MB. Formats: JPEG, PNG, JPG, GIF') }}</p>
					</div>
					<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
						<div>
							<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Category') }}</label>
							<select name="category_id" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100">
								<option value="">{{ __('None') }}</option>
								@foreach($categories as $category)
									<option value="{{ $category->id }}" {{ $product->category_id === $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
								@endforeach
							</select>
						</div>
						<div>
							<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Quantity') }}</label>
							<input type="number" name="quantity" min="0" value="{{ old('quantity', $product->quantity) }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100" required>
						</div>
					</div>
					<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
						<div>
							<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Cost') }}</label>
							<input type="number" step="0.01" name="cost" min="0" value="{{ old('cost', $product->cost) }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100" required>
						</div>
						<div>
							<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Supplier') }}</label>
							<select name="supplier_id" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100">
								<option value="">{{ __('None') }}</option>
								@foreach($suppliers as $supplier)
									<option value="{{ $supplier->id }}" {{ $product->supplier_id === $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Country') }}</label>
						<select name="country_id" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100" required>
							@foreach($countries as $country)
								<option value="{{ $country->id }}" {{ $product->country_id === $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
							@endforeach
						</select>
					</div>
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Low Stock Threshold') }}</label>
						<input type="number" name="low_stock_threshold" min="0" value="{{ old('low_stock_threshold', $product->low_stock_threshold) }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100">
					</div>
					<div class="pt-4">
						<button class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">{{ __('Save') }}</button>
						<a href="{{ route('products.index') }}" class="ms-2 text-gray-600 hover:underline">{{ __('Cancel') }}</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</x-admin-layout>

