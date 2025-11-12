<x-admin-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Create Supplier') }}
		</h2>
	</x-slot>
	<div class="py-6">
		<div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
				<form method="POST" action="{{ route('suppliers.store') }}" class="space-y-4">
					@csrf
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Name') }}</label>
						<input name="name" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700" required>
					</div>
					<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
						<div>
							<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Contact Email') }}</label>
							<input type="email" name="contact_email" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700">
						</div>
						<div>
							<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Phone') }}</label>
							<input type="text" name="phone" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700">
						</div>
					</div>
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Country') }}</label>
						<select name="country_id" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700">
							<option value="">{{ __('None') }}</option>
							@foreach($countries as $country)
								<option value="{{ $country->id }}">{{ $country->name }}</option>
							@endforeach
						</select>
					</div>
					<div class="pt-4">
						<button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">{{ __('Save') }}</button>
						<a href="{{ route('suppliers.index') }}" class="ms-2 text-gray-600 hover:underline">{{ __('Cancel') }}</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</x-admin-layout>

