<x-admin-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Create Sourcing') }}
		</h2>
	</x-slot>
	<div class="py-6">
		<div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
				<form method="POST" action="{{ route('sourcings.store') }}" enctype="multipart/form-data" class="space-y-4">
					@csrf
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Product Name') }}</label>
						<input type="text" name="product_name" value="{{ old('product_name') }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700" required>
					</div>
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Product Image') }}</label>
						<input type="file" name="product_image" accept="image/*" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700">
						<p class="mt-1 text-xs text-gray-500">{{ __('Max size: 2MB. Formats: JPEG, PNG, JPG, GIF') }}</p>
					</div>
					<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
						<div>
							<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Category') }}</label>
							<select name="category_id" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700">
								<option value="">{{ __('None') }}</option>
								@foreach($categories as $category)
									<option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
								@endforeach
							</select>
						</div>
						<div>
							<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Quantity') }}</label>
							<input type="number" name="quantity" min="0" value="{{ old('quantity', 0) }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700" required>
						</div>
					</div>
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Country') }}</label>
						<select name="country_id" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700" required>
							<option value="">{{ __('Select a country') }}</option>
							@foreach($countries as $country)
								<option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
							@endforeach
						</select>
					</div>
					<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
						<div>
							<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Unit Price') }}</label>
							<input type="number" step="0.01" name="price" id="unit_price" min="0" value="{{ old('price', 0) }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700" required>
						</div>
						<div>
							<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Price Total') }}</label>
							<input type="text" id="price_total" value="0.00" readonly class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700 bg-gray-100 dark:bg-gray-700">
							<p class="mt-1 text-xs text-gray-500">{{ __('Calculated automatically: Unit Price × Quantity') }}</p>
						</div>
					</div>
					<script>
						document.addEventListener('DOMContentLoaded', function() {
							const unitPriceInput = document.getElementById('unit_price');
							const quantityInput = document.querySelector('input[name="quantity"]');
							const priceTotalInput = document.getElementById('price_total');

							function calculatePriceTotal() {
								const unitPrice = parseFloat(unitPriceInput.value) || 0;
								const quantity = parseFloat(quantityInput.value) || 0;
								const priceTotal = unitPrice * quantity;
								priceTotalInput.value = priceTotal.toFixed(2);
							}

							unitPriceInput.addEventListener('input', calculatePriceTotal);
							quantityInput.addEventListener('input', calculatePriceTotal);
							calculatePriceTotal(); // Calculate on page load
						});
					</script>
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Shipping Type') }}</label>
						<select name="shipping_type" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700">
							<option value="">{{ __('Select shipping type') }}</option>
							<option value="in_transit" {{ old('shipping_type') == 'in_transit' ? 'selected' : '' }}>{{ __('In Transit') }}</option>
							<option value="arrived" {{ old('shipping_type') == 'arrived' ? 'selected' : '' }}>{{ __('Arrived') }}</option>
						</select>
					</div>
					<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
						<div>
							<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Additional Fees') }}</label>
							<input type="number" step="0.01" name="additional_fees" min="0" value="{{ old('additional_fees', 0) }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700">
						</div>
						<div>
							<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Testing Fees') }}</label>
							<input type="number" step="0.01" name="testing_fees" min="0" value="{{ old('testing_fees', 0) }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700">
						</div>
					</div>
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Supplier') }}</label>
						<select name="supplier_id" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700">
							<option value="">{{ __('None') }}</option>
							@foreach($suppliers as $supplier)
								<option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
							@endforeach
						</select>
					</div>
					<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
						<div>
							<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Shipping Cost') }}</label>
							<input type="number" step="0.01" name="shipping_cost" min="0" value="{{ old('shipping_cost', 0) }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700">
						</div>
						<div>
							<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Shipping Method') }}</label>
							<select name="shipping_method" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700">
								<option value="">{{ __('Select method') }}</option>
								@foreach($shippingMethods as $method)
									<option value="{{ $method->name }}" {{ old('shipping_method') == $method->name ? 'selected' : '' }}>{{ $method->name }}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Sourcing Date') }}</label>
						<input type="date" name="sourcing_date" value="{{ old('sourcing_date') }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700">
					</div>
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Notes') }}</label>
						<textarea name="notes" rows="3" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700">{{ old('notes') }}</textarea>
					</div>
					<div class="pt-4">
						<button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">{{ __('Save') }}</button>
						<a href="{{ route('sourcings.index') }}" class="ms-2 text-gray-600 hover:underline">{{ __('Cancel') }}</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</x-admin-layout>

