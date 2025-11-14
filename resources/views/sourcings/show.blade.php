<x-admin-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Sourcing Details') }} - {{ $sourcing->product_name }}
		</h2>
	</x-slot>
	<div class="py-6">
		<div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 space-y-4">
				@if($sourcing->product_image)
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Product Image') }}</label>
						<img src="{{ \Illuminate\Support\Facades\Storage::url($sourcing->product_image) }}" alt="{{ $sourcing->product_name }}" class="mt-1 h-40 w-40 object-cover rounded">
					</div>
				@endif
				<div>
					<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Product Name') }}</label>
					<p class="mt-1 text-gray-900 dark:text-gray-100">{{ $sourcing->product_name }}</p>
				</div>
				<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Category') }}</label>
						<p class="mt-1 text-gray-900 dark:text-gray-100">{{ $sourcing->category->name ?? __('N/A') }}</p>
					</div>
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Quantity') }}</label>
						<p class="mt-1 text-gray-900 dark:text-gray-100">{{ number_format($sourcing->quantity) }}</p>
					</div>
				</div>
				<div>
					<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Country') }}</label>
					<p class="mt-1 text-gray-900 dark:text-gray-100">{{ $sourcing->country->name ?? __('N/A') }}</p>
				</div>
				<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Unit Price') }}</label>
						<p class="mt-1 text-gray-900 dark:text-gray-100">{{ number_format($sourcing->price, 2) }}</p>
					</div>
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Price Total') }}</label>
						<p class="mt-1 text-gray-900 dark:text-gray-100">{{ number_format($sourcing->cost, 2) }}</p>
					</div>
				</div>
				<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Cost Total') }}</label>
						<p class="mt-1 text-gray-900 dark:text-gray-100 font-semibold">{{ number_format($sourcing->cost_total, 2) }}</p>
						<p class="mt-1 text-xs text-gray-500">{{ __('(Price Total + Additional Fees + Testing Fees + Shipping Cost) / Quantity') }}</p>
					</div>
				</div>
				<div>
					<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Shipping Type') }}</label>
					<p class="mt-1 text-gray-900 dark:text-gray-100">{{ $sourcing->shipping_type ? ucfirst(str_replace('_', ' ', $sourcing->shipping_type)) : __('N/A') }}</p>
				</div>
				<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Additional Fees') }}</label>
						<p class="mt-1 text-gray-900 dark:text-gray-100">{{ number_format($sourcing->additional_fees, 2) }}</p>
					</div>
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Testing Fees') }}</label>
						<p class="mt-1 text-gray-900 dark:text-gray-100">{{ number_format($sourcing->testing_fees, 2) }}</p>
					</div>
				</div>
				<div>
					<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Supplier') }}</label>
					<p class="mt-1 text-gray-900 dark:text-gray-100">{{ $sourcing->supplier->name ?? __('N/A') }}</p>
				</div>
				<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Shipping Cost') }}</label>
						<p class="mt-1 text-gray-900 dark:text-gray-100">{{ number_format($sourcing->shipping_cost, 2) }}</p>
					</div>
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Shipping Method') }}</label>
						<p class="mt-1 text-gray-900 dark:text-gray-100">{{ $sourcing->shipping_method ?? __('N/A') }}</p>
					</div>
				</div>
				<div>
					<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Sourcing Date') }}</label>
					<p class="mt-1 text-gray-900 dark:text-gray-100">{{ $sourcing->sourcing_date ? $sourcing->sourcing_date->format('Y-m-d') : __('N/A') }}</p>
				</div>
				<div>
					<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Status') }}</label>
					<p class="mt-1">
						@if($sourcing->validated)
							<span class="px-3 py-1 text-sm bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-300 rounded">{{ __('Validated') }}</span>
						@else
							<span class="px-3 py-1 text-sm bg-yellow-100 dark:bg-yellow-800 text-yellow-700 dark:text-yellow-300 rounded">{{ __('Pending Validation') }}</span>
						@endif
					</p>
				</div>
				@if($sourcing->notes)
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Notes') }}</label>
						<p class="mt-1 text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $sourcing->notes }}</p>
					</div>
				@endif
				<div class="pt-4 space-x-2">
					@if(!$sourcing->validated)
						<form action="{{ route('sourcings.validate', $sourcing) }}" method="POST" class="inline">
							@csrf
							<button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700" onclick="return confirm('{{ __('Validate this sourcing and create the product?') }}')">{{ __('Validate') }}</button>
						</form>
					@endif
					<a href="{{ route('sourcings.edit', $sourcing) }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">{{ __('Edit') }}</a>
					<a href="{{ route('sourcings.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">{{ __('Back to List') }}</a>
				</div>
			</div>
		</div>
	</div>
</x-admin-layout>

