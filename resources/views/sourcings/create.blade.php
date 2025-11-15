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
					<div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-200 dark:border-blue-800">
						<label class="flex items-center space-x-2 cursor-pointer">
							<input type="checkbox" name="is_restock" value="1" id="is_restock" {{ old('is_restock') ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
							<span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Restock Existing Product') }}</span>
						</label>
						<p class="mt-2 text-xs text-gray-600 dark:text-gray-400">{{ __('Check this to restock an existing product instead of creating a new one') }}</p>
					</div>
					
					<div id="restock_section" style="display: none;">
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Select Product to Restock') }}</label>
						<select name="product_id" id="product_select" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700">
							<option value="">{{ __('Select a product') }}</option>
							@foreach($products as $product)
								<option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
							@endforeach
						</select>
					</div>
					
					<div id="new_product_section">
						<div>
							<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Product Name') }}</label>
							<input type="text" name="product_name" id="product_name" value="{{ old('product_name') }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700" required>
						</div>
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
							const isRestockCheckbox = document.getElementById('is_restock');
							const restockSection = document.getElementById('restock_section');
							const newProductSection = document.getElementById('new_product_section');
							const productSelect = document.getElementById('product_select');
							const productNameInput = document.getElementById('product_name');
							const products = @json($products->keyBy('id'));

							function toggleRestockSections() {
								if (isRestockCheckbox.checked) {
									restockSection.style.display = 'block';
									newProductSection.style.display = 'none';
									productNameInput.removeAttribute('required');
									productSelect.setAttribute('required', 'required');
								} else {
									restockSection.style.display = 'none';
									newProductSection.style.display = 'block';
									productSelect.removeAttribute('required');
									productNameInput.setAttribute('required', 'required');
								}
							}

							function updateProductInfo() {
								if (isRestockCheckbox.checked && productSelect.value) {
									const product = products[productSelect.value];
									if (product) {
										productNameInput.value = product.name;
									}
								}
							}

							function calculatePriceTotal() {
								const unitPrice = parseFloat(unitPriceInput.value) || 0;
								const quantity = parseFloat(quantityInput.value) || 0;
								const priceTotal = unitPrice * quantity;
								priceTotalInput.value = priceTotal.toFixed(2);
							}

							isRestockCheckbox.addEventListener('change', toggleRestockSections);
							productSelect.addEventListener('change', updateProductInfo);
							unitPriceInput.addEventListener('input', calculatePriceTotal);
							quantityInput.addEventListener('input', calculatePriceTotal);
							
							// Initialize on page load
							toggleRestockSections();
							calculatePriceTotal();
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

