<x-admin-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Edit Sourcing') }} - {{ $sourcing->product->name }}
		</h2>
	</x-slot>
	<div class="py-6">
		<div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
				<form method="POST" action="{{ route('sourcings.update', $sourcing) }}" class="space-y-4">
					@csrf @method('PUT')
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Product') }}</label>
						<select name="product_id" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700" required>
							@foreach($products as $product)
								<option value="{{ $product->id }}" {{ $sourcing->product_id === $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
							@endforeach
						</select>
					</div>
					<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
						<div>
							<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Shipping Cost') }}</label>
							<input type="number" step="0.01" name="shipping_cost" min="0" value="{{ old('shipping_cost', $sourcing->shipping_cost) }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700" required>
						</div>
						<div>
							<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Shipping Method') }}</label>
							<select name="shipping_method" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700">
								<option value="">{{ __('Select method') }}</option>
								@foreach($shippingMethods as $method)
									<option value="{{ $method->name }}" {{ $sourcing->shipping_method === $method->name ? 'selected' : '' }}>{{ $method->name }}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Sourcing Date') }}</label>
						<input type="date" name="sourcing_date" value="{{ old('sourcing_date', $sourcing->sourcing_date?->format('Y-m-d')) }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700">
					</div>
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Notes') }}</label>
						<textarea name="notes" rows="3" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700">{{ old('notes', $sourcing->notes) }}</textarea>
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

