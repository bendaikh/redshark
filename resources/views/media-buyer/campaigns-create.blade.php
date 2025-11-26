<x-media-buyer-layout>
	<x-slot name="header">
		<div class="flex items-center gap-4">
			<a href="{{ route('media-buyer.campaigns') }}" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100">
				<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
				</svg>
			</a>
			<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
				{{ __('Create Ad Campaign') }}
			</h2>
		</div>
	</x-slot>

	<div class="py-12">
		<div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
				<form method="POST" action="{{ route('media-buyer.campaigns.store') }}" class="space-y-4" id="adsCampaignForm">
					@csrf
					<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
						<div>
							<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Date From') }}</label>
							<input type="date" name="date_from" value="{{ old('date_from') }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100" required>
							@error('date_from')
								<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
							@enderror
						</div>
						<div>
							<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Date To') }}</label>
							<input type="date" name="date_to" value="{{ old('date_to') }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100" required>
							@error('date_to')
								<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
							@enderror
						</div>
					</div>
					<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
						<div>
							<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Platform') }}</label>
							<select name="platform_id" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100" required>
								<option value="">{{ __('Select Platform') }}</option>
								@foreach($platforms as $platform)
									<option value="{{ $platform->id }}" {{ old('platform_id') == $platform->id ? 'selected' : '' }}>{{ $platform->name }}</option>
								@endforeach
							</select>
							@error('platform_id')
								<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
							@enderror
						</div>
						<div>
							<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Country') }}</label>
							<select name="country_id" id="countrySelect" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100" required>
								<option value="">{{ __('Select Country') }}</option>
								@foreach($countries as $c)
									<option value="{{ $c->id }}" {{ old('country_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
								@endforeach
							</select>
							@error('country_id')
								<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
							@enderror
						</div>
					</div>
					
					<div class="border-t border-gray-200 dark:border-gray-700 pt-4">
						<label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-3">{{ __('Select Products') }}</label>
						<select id="productSelect" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100" disabled>
							<option value="">{{ __('Please select a country first') }}</option>
						</select>
						@error('products')
							<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
						@enderror
					</div>

					<div class="border-t border-gray-200 dark:border-gray-700 pt-4">
						<label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-3">{{ __('Selected Products, Amount Spent & Leads') }}</label>
						<div id="productsContainer" class="flex flex-wrap gap-3">
							<!-- Product tags will be added here -->
						</div>
						<div id="noProductsMessage" class="text-sm text-gray-500 dark:text-gray-400 mt-2">
							{{ __('No products selected yet. Select products from the dropdown above.') }}
						</div>
					</div>

					<div class="pt-4 flex gap-3">
						<button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">{{ __('Save') }}</button>
						<a href="{{ route('media-buyer.campaigns') }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">{{ __('Cancel') }}</a>
					</div>
				</form>
			</div>
		</div>
	</div>

	<script>
		let products = [];
		let productRowIndex = 0;
		const addedProductIds = new Set();

		// Function to load products for a country
		function loadProductsForCountry(countryId) {
			const productSelect = document.getElementById('productSelect');
			
			if (countryId) {
				// Fetch products for selected country (only products assigned to this media buyer)
				fetch(`{{ route('media-buyer.campaigns.products-by-country') }}?country_id=${countryId}`)
					.then(response => response.json())
					.then(data => {
						products = data;
						if (products.length === 0) {
							productSelect.innerHTML = '<option value="">{{ __("No products assigned to you for this country") }}</option>';
							productSelect.disabled = true;
						} else {
							updateProductSelect();
							productSelect.disabled = false;
						}
					})
					.catch(error => {
						console.error('Error fetching products:', error);
						productSelect.innerHTML = '<option value="">{{ __("Error loading products") }}</option>';
						productSelect.disabled = true;
					});
			} else {
				products = [];
				productSelect.innerHTML = '<option value="">{{ __("Please select a country first") }}</option>';
				productSelect.disabled = true;
			}
		}

		// Listen to country selection changes
		document.getElementById('countrySelect').addEventListener('change', function() {
			loadProductsForCountry(this.value);
		});

		// Load products on page load if country is pre-selected (from old input)
		document.addEventListener('DOMContentLoaded', function() {
			const countrySelect = document.getElementById('countrySelect');
			if (countrySelect.value) {
				loadProductsForCountry(countrySelect.value);
			}
		});

		function updateProductSelect() {
			const productSelect = document.getElementById('productSelect');
			productSelect.innerHTML = '<option value="">{{ __("Select a product to add") }}</option>';
			
			products.forEach(product => {
				if (!addedProductIds.has(product.id)) {
					const option = document.createElement('option');
					option.value = product.id;
					option.textContent = product.name;
					option.setAttribute('data-name', product.name);
					productSelect.appendChild(option);
				}
			});
		}

		document.getElementById('productSelect').addEventListener('change', function() {
			const productId = parseInt(this.value);
			if (productId && !addedProductIds.has(productId)) {
				const selectedOption = this.options[this.selectedIndex];
				const productName = selectedOption.getAttribute('data-name');
				addProductTag(productId, productName);
				addedProductIds.add(productId);
				this.value = ''; // Reset select
				updateProductSelect(); // Update to remove added product from dropdown
			}
		});

		function addProductTag(productId, productName, amountSpent = '', leads = '') {
			const container = document.getElementById('productsContainer');
			const noProductsMessage = document.getElementById('noProductsMessage');
			
			// Hide no products message
			if (noProductsMessage) {
				noProductsMessage.style.display = 'none';
			}

			const tag = document.createElement('div');
			tag.className = 'inline-flex items-center gap-2 bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 px-4 py-2 rounded-lg border border-indigo-300 dark:border-indigo-700';
			tag.id = `productTag_${productRowIndex}`;
			
			tag.innerHTML = `
				<span class="font-medium">${productName}</span>
				<input type="hidden" name="products[${productRowIndex}][id]" value="${productId}">
				<label class="text-xs text-gray-600 dark:text-gray-400">Amount:</label>
				<input type="number" step="0.01" min="0" name="products[${productRowIndex}][amount_spent]" value="${amountSpent}" placeholder="0.00" class="w-20 px-2 py-1 text-sm rounded border-indigo-300 dark:border-indigo-600 dark:bg-indigo-800 dark:text-indigo-100" required>
				<label class="text-xs text-gray-600 dark:text-gray-400">Leads:</label>
				<input type="number" min="0" name="products[${productRowIndex}][leads]" value="${leads}" placeholder="0" class="w-20 px-2 py-1 text-sm rounded border-indigo-300 dark:border-indigo-600 dark:bg-indigo-800 dark:text-indigo-100">
				<button type="button" onclick="removeProductTag(${productRowIndex}, ${productId})" class="text-indigo-600 dark:text-indigo-300 hover:text-red-600 dark:hover:text-red-400 font-bold" title="Remove">
					×
				</button>
			`;
			
			container.appendChild(tag);
			productRowIndex++;
		}

		function removeProductTag(index, productId) {
			const tag = document.getElementById(`productTag_${index}`);
			if (tag) {
				tag.remove();
				addedProductIds.delete(productId);
				updateProductSelect(); // Update to re-add product to dropdown
				
				// Show no products message if container is empty
				const container = document.getElementById('productsContainer');
				const noProductsMessage = document.getElementById('noProductsMessage');
				if (container.children.length === 0 && noProductsMessage) {
					noProductsMessage.style.display = 'block';
				}
			}
		}
	</script>
</x-media-buyer-layout>

