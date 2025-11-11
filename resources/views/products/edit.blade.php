<x-admin-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Edit Product') }} - {{ $product->name }}
		</h2>
	</x-slot>
	<div class="py-6">
		<div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
				<form method="POST" action="{{ route('products.update', $product) }}" class="space-y-4">
					@csrf @method('PUT')
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Name') }}</label>
						<input name="name" value="{{ old('name', $product->name) }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100" required>
					</div>
					<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
						<div>
							<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Category') }}</label>
							<input name="category" value="{{ old('category', $product->category) }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100">
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
							<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Selling Price') }}</label>
							<input type="number" step="0.01" name="selling_price" min="0" value="{{ old('selling_price', $product->selling_price) }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100" required>
						</div>
					</div>
					<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
						<div>
							<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Supplier') }}</label>
							<select name="supplier_id" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100">
								<option value="">{{ __('None') }}</option>
								@foreach(\App\Models\Supplier::orderBy('name')->get() as $s)
									<option value="{{ $s->id }}" {{ $product->supplier_id === $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
								@endforeach
							</select>
						</div>
						<div>
							<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Country') }}</label>
							<select name="country_id" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100" required>
								@foreach(\App\Models\Country::orderBy('name')->get() as $c)
									<option value="{{ $c->id }}" {{ $product->country_id === $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
								@endforeach
							</select>
						</div>
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

