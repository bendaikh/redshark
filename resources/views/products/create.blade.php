<x-admin-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Create Product') }}
		</h2>
	</x-slot>
	<div class="py-6">
		<div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
				<form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" class="space-y-4">
					@csrf
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Name') }}</label>
						<input name="name" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100" required>
					</div>
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Product Image') }}</label>
						<input type="file" name="image" accept="image/*" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100">
						<p class="mt-1 text-xs text-gray-500">{{ __('Max size: 2MB. Formats: JPEG, PNG, JPG, GIF') }}</p>
					</div>
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Category') }}</label>
						<select name="category_id" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100">
							<option value="">{{ __('None') }}</option>
							@foreach($categories as $category)
								<option value="{{ $category->id }}">{{ $category->name }}</option>
							@endforeach
						</select>
					</div>
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Country') }}</label>
						<select name="country_id" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100" required>
							@foreach($countries as $country)
								<option value="{{ $country->id }}" {{ ($currentCountry?->id === $country->id) ? 'selected' : '' }}>{{ $country->name }}</option>
							@endforeach
						</select>
					</div>
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Low Stock Threshold') }}</label>
						<input type="number" name="low_stock_threshold" min="0" value="5" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100">
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

