<x-admin-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Sourcing Details') }} - {{ $sourcing->product->name }}
		</h2>
	</x-slot>
	<div class="py-6">
		<div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 space-y-4">
				<div>
					<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Product') }}</label>
					<p class="mt-1 text-gray-900 dark:text-gray-100">{{ $sourcing->product->name }}</p>
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
				@if($sourcing->notes)
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Notes') }}</label>
						<p class="mt-1 text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $sourcing->notes }}</p>
					</div>
				@endif
				<div class="pt-4">
					<a href="{{ route('sourcings.edit', $sourcing) }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">{{ __('Edit') }}</a>
					<a href="{{ route('sourcings.index') }}" class="ms-2 text-gray-600 hover:underline">{{ __('Back to List') }}</a>
				</div>
			</div>
		</div>
	</div>
</x-admin-layout>

