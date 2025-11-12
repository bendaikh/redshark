<x-admin-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Edit Shipping Method') }} - {{ $shippingMethod->name }}
		</h2>
	</x-slot>
	<div class="py-6">
		<div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
				<form method="POST" action="{{ route('shipping-methods.update', $shippingMethod) }}" class="space-y-4">
					@csrf @method('PUT')
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Name') }}</label>
						<input name="name" value="{{ old('name', $shippingMethod->name) }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700" required>
						@error('name')
							<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
						@enderror
					</div>
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Description') }}</label>
						<textarea name="description" rows="3" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700">{{ old('description', $shippingMethod->description) }}</textarea>
					</div>
					<div>
						<label class="flex items-center">
							<input type="checkbox" name="active" value="1" {{ old('active', $shippingMethod->active) ? 'checked' : '' }} class="rounded border-gray-300 dark:bg-gray-900 dark:border-gray-700">
							<span class="ms-2 text-sm text-gray-600 dark:text-gray-300">{{ __('Active') }}</span>
						</label>
					</div>
					<div class="pt-4">
						<button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">{{ __('Save') }}</button>
						<a href="{{ route('shipping-methods.index') }}" class="ms-2 text-gray-600 hover:underline">{{ __('Cancel') }}</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</x-admin-layout>

