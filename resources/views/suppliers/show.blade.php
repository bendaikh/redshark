<x-admin-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Supplier') }} - {{ $supplier->name }}
		</h2>
	</x-slot>
	<div class="py-6">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
				<div class="space-y-4">
					<div>
						<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Supplier Name') }}</h3>
						<p class="text-gray-600 dark:text-gray-300">{{ $supplier->name }}</p>
					</div>
					@if($supplier->contact_email)
						<div>
							<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Contact Email') }}</h3>
							<p class="text-gray-600 dark:text-gray-300">{{ $supplier->contact_email }}</p>
						</div>
					@endif
					@if($supplier->phone)
						<div>
							<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Phone') }}</h3>
							<p class="text-gray-600 dark:text-gray-300">{{ $supplier->phone }}</p>
						</div>
					@endif
					@if($supplier->country)
						<div>
							<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Country') }}</h3>
							<p class="text-gray-600 dark:text-gray-300">{{ $supplier->country->name }}</p>
						</div>
					@endif
				</div>
				<div class="pt-4">
					<a href="{{ route('suppliers.edit', $supplier) }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">{{ __('Edit') }}</a>
					<a href="{{ route('suppliers.index') }}" class="ms-2 text-gray-600 hover:underline">{{ __('Back to Suppliers') }}</a>
				</div>
			</div>
		</div>
	</div>
</x-admin-layout>

