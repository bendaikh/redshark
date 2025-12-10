<x-admin-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Create Invoice') }}
		</h2>
	</x-slot>
	<div class="py-6">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
				<form method="POST" action="{{ route('invoices.store') }}" class="space-y-6" id="invoiceForm">
					@csrf
					
					<!-- Date Range -->
					<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
						<div>
							<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Date From') }}</label>
							<input type="date" name="date_from" value="{{ old('date_from') }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700">
						</div>
						<div>
							<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Date To') }}</label>
							<input type="date" name="date_to" value="{{ old('date_to') }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700">
						</div>
					</div>

					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Country') }}</label>
						<select name="country_id" id="countrySelect" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700" required>
							@foreach($countries as $c)
								<option value="{{ $c->id }}" {{ ($currentCountry?->id === $c->id) ? 'selected' : '' }}>{{ $c->name }}</option>
							@endforeach
						</select>
					</div>

					<!-- Product Selection -->
					<div class="border-t border-gray-200 dark:border-gray-700 pt-4">
						<label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-3">{{ __('Select Products') }}</label>
					<select id="productSelect" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700">
						<option value="">{{ __('Select a product to add') }}</option>
						<!-- Products will be populated by JavaScript based on selected country -->
					</select>
					<p id="noProductsForCountry" class="mt-2 text-sm text-amber-600 dark:text-amber-400 hidden">
						{{ __('No products available for this country.') }}
					</p>
						@error('products')
							<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
						@enderror
					</div>

					<!-- Selected Products with Fields -->
					<div class="border-t border-gray-200 dark:border-gray-700 pt-4">
						<label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-3">{{ __('Selected Products & Details') }}</label>
						<div id="productsContainer" class="space-y-4">
							<!-- Product cards will be added here -->
						</div>
						<div id="noProductsMessage" class="text-sm text-gray-500 dark:text-gray-400 mt-2">
							{{ __('No products selected yet. Select products from the dropdown above.') }}
						</div>
					</div>

					<div class="pt-4 border-t border-gray-200 dark:border-gray-700">
						<button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">{{ __('Save') }}</button>
						<a href="{{ route('invoices.index') }}" class="ms-2 text-gray-600 hover:underline">{{ __('Cancel') }}</a>
					</div>
				</form>
			</div>
		</div>
	</div>

	<script>
		const allProducts = @json($products);
		const deliveryFees = @json($deliveryFees);
		let productRowIndex = 0;
		const addedProductIds = new Set();

		// Filter products by country
		function filterProductsByCountry(countryId) {
			const productSelect = document.getElementById('productSelect');
			const noProductsMsg = document.getElementById('noProductsForCountry');
			
			// Clear current options except the first one
			productSelect.innerHTML = '<option value="">{{ __("Select a product to add") }}</option>';
			
			// Filter products by country_id
			const filteredProducts = allProducts.filter(p => p.country_id == countryId);
			
			// Show/hide no products message
			if (filteredProducts.length === 0) {
				noProductsMsg.classList.remove('hidden');
			} else {
				noProductsMsg.classList.add('hidden');
			}
			
			// Add filtered products to select
			filteredProducts.forEach(product => {
				// Skip if already added
				if (addedProductIds.has(product.id)) return;
				
				const option = document.createElement('option');
				option.value = product.id;
				option.setAttribute('data-name', product.name);
				option.setAttribute('data-cost', product.average_cost || 0);
				option.textContent = product.name;
				productSelect.appendChild(option);
			});
		}

		// Initialize products filter on page load
		document.addEventListener('DOMContentLoaded', function() {
			const countrySelect = document.getElementById('countrySelect');
			filterProductsByCountry(countrySelect.value);
		});

		// Filter products when country changes
		document.getElementById('countrySelect').addEventListener('change', function() {
			// Clear added products when country changes
			const container = document.getElementById('productsContainer');
			container.innerHTML = '';
			addedProductIds.clear();
			document.getElementById('noProductsMessage').style.display = 'block';
			
			filterProductsByCountry(this.value);
		});

		document.getElementById('productSelect').addEventListener('change', function() {
			const productId = parseInt(this.value);
			if (productId && !addedProductIds.has(productId)) {
				const selectedOption = this.options[this.selectedIndex];
				const productName = selectedOption.getAttribute('data-name');
				const productCost = parseFloat(selectedOption.getAttribute('data-cost')) || 0;
				addProductCard(productId, productName, productCost);
				addedProductIds.add(productId);
				this.value = ''; // Reset select
				
				// Remove the added product from the dropdown
				selectedOption.remove();
			}
		});

		function addProductCard(productId, productName, productCost) {
			const container = document.getElementById('productsContainer');
			const noProductsMessage = document.getElementById('noProductsMessage');
			
			// Hide no products message
			if (noProductsMessage) {
				noProductsMessage.style.display = 'none';
			}

			const card = document.createElement('div');
			card.className = 'bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg border border-gray-200 dark:border-gray-600';
			card.id = `productCard_${productRowIndex}`;
			
			card.innerHTML = `
				<div class="flex items-center justify-between mb-4">
					<h4 class="font-semibold text-gray-900 dark:text-gray-100">${productName}</h4>
					<button type="button" onclick="removeProductCard(${productRowIndex}, ${productId})" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 font-bold" title="Remove">×</button>
				</div>
				<input type="hidden" name="products[${productRowIndex}][id]" value="${productId}">
				<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
					<div>
						<label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">{{ __('Revenue') }}</label>
						<input type="number" step="0.01" min="0" name="products[${productRowIndex}][revenue]" value="0" 
							class="w-full px-3 py-2 text-sm rounded border-gray-300 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600 calculate-profit" 
							onchange="calculateNetProfit(${productRowIndex})" placeholder="0.00">
					</div>
					<div>
						<label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">{{ __('Total Orders') }}</label>
						<input type="number" min="0" name="products[${productRowIndex}][total_orders]" value="0" 
							class="w-full px-3 py-2 text-sm rounded border-gray-300 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600 calculate-profit" 
							onchange="calculateNetProfit(${productRowIndex})" placeholder="0">
					</div>
					<div>
						<label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">{{ __('Quantity Sold') }}</label>
						<input type="number" min="0" name="products[${productRowIndex}][quantity_sold]" value="0" 
							class="w-full px-3 py-2 text-sm rounded border-gray-300 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600 calculate-profit" 
							onchange="calculateNetProfit(${productRowIndex})" placeholder="0">
					</div>
					<div>
						<label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">{{ __('Delivery Fees') }}</label>
						<select name="products[${productRowIndex}][delivery_fee_id]" 
							class="w-full px-3 py-2 text-sm rounded border-gray-300 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600 calculate-profit" 
							onchange="calculateNetProfit(${productRowIndex})">
							<option value="">{{ __('None') }}</option>
							${deliveryFees.map(fee => `<option value="${fee.id}" data-fee="${fee.fee_per_unit}">${fee.name || 'Unnamed'} (${fee.fee_per_unit})</option>`).join('')}
						</select>
					</div>
					<div>
						<label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">{{ __('Cost') }} <span class="text-gray-400">({{ __('Auto') }})</span></label>
						<input type="number" step="0.01" value="${productCost}" 
							class="w-full px-3 py-2 text-sm rounded border-gray-300 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600 bg-gray-100 dark:bg-gray-900" 
							readonly>
						<input type="hidden" name="products[${productRowIndex}][product_cost]" value="${productCost}">
					</div>
					<div>
						<label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">{{ __('Total Amount') }} <span class="text-gray-400">({{ __('Auto') }})</span></label>
						<input type="number" step="0.01" id="totalAmount_${productRowIndex}" value="0" 
							class="w-full px-3 py-2 text-sm rounded border-gray-300 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600 bg-gray-100 dark:bg-gray-900 font-semibold" 
							readonly>
					</div>
				</div>
			`;
			
			container.appendChild(card);
			productRowIndex++;
		}

		function removeProductCard(index, productId) {
			const card = document.getElementById(`productCard_${index}`);
			if (card) {
				card.remove();
				addedProductIds.delete(productId);
				
				// Show no products message if container is empty
				const container = document.getElementById('productsContainer');
				const noProductsMessage = document.getElementById('noProductsMessage');
				if (container.children.length === 0 && noProductsMessage) {
					noProductsMessage.style.display = 'block';
				}
			}
		}

		function calculateNetProfit(index) {
			const card = document.getElementById(`productCard_${index}`);
			if (!card) return;

			const revenue = parseFloat(card.querySelector('input[name*="[revenue]"]').value) || 0;
			const totalOrders = parseFloat(card.querySelector('input[name*="[total_orders]"]').value) || 0;
			const quantitySold = parseFloat(card.querySelector('input[name*="[quantity_sold]"]').value) || 0;
			const productCostInput = card.querySelector('input[name*="[product_cost]"]');
			const productCost = parseFloat(productCostInput ? productCostInput.value : 0) || 0;
			
			const deliveryFeeSelect = card.querySelector('select[name*="[delivery_fee_id]"]');
			const selectedOption = deliveryFeeSelect ? deliveryFeeSelect.options[deliveryFeeSelect.selectedIndex] : null;
			const deliveryFeePerUnit = selectedOption && selectedOption.dataset.fee ? parseFloat(selectedOption.dataset.fee) : 0;

			// Calculate: Revenue - (Total Orders × Delivery Fees) - (Quantity Sold × Cost)
			const totalDeliveryFee = totalOrders * deliveryFeePerUnit;
			const totalProductCost = quantitySold * productCost;
			const totalAmount = revenue - totalDeliveryFee - totalProductCost;
			
			const totalAmountInput = document.getElementById(`totalAmount_${index}`);
			if (totalAmountInput) {
				totalAmountInput.value = totalAmount.toFixed(2);
			}
		}

		// Recalculate on any input change
		document.addEventListener('input', function(e) {
			if (e.target.classList.contains('calculate-profit')) {
				const card = e.target.closest('[id^="productCard_"]');
				if (card) {
					const index = parseInt(card.id.replace('productCard_', ''));
					calculateNetProfit(index);
				}
			}
		});
	</script>
</x-admin-layout>
