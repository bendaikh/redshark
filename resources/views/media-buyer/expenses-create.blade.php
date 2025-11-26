<x-media-buyer-layout>
	<x-slot name="header">
		<div class="flex items-center gap-4">
			<a href="{{ route('media-buyer.expenses') }}" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100">
				<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
				</svg>
			</a>
			<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
				{{ __('Add Expense') }}
			</h2>
		</div>
	</x-slot>

	<div class="py-12">
		<div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
				<form method="POST" action="{{ route('media-buyer.expenses.store') }}" class="space-y-4">
					@csrf
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Expense Category') }}</label>
						@if($expenseCategories->isEmpty())
							<div class="mt-1 p-3 bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 rounded-md">
								<p class="text-sm text-yellow-800 dark:text-yellow-200">
									{{ __('No expense categories available. Please contact administrator.') }}
								</p>
							</div>
						@else
							<select name="expense_category_id" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700" required>
								<option value="">{{ __('Select Category') }}</option>
								@foreach($expenseCategories as $category)
									<option value="{{ $category->id }}" {{ old('expense_category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
								@endforeach
							</select>
						@endif
						@error('expense_category_id')
							<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
						@enderror
					</div>
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Country') }}</label>
						<select name="country_id" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700" required>
							<option value="">{{ __('Select Country') }}</option>
							@foreach($countries as $country)
								<option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
							@endforeach
						</select>
						@error('country_id')
							<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
						@enderror
					</div>
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Amount') }}</label>
						<input type="number" name="amount" step="0.01" min="0" value="{{ old('amount') }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700" required>
						@error('amount')
							<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
						@enderror
					</div>
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Date') }}</label>
						<input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700" required>
						@error('date')
							<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
						@enderror
					</div>
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Description') }}</label>
						<textarea name="description" rows="3" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700">{{ old('description') }}</textarea>
						@error('description')
							<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
						@enderror
					</div>
					<div class="pt-4 flex gap-3">
						<button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 {{ $expenseCategories->isEmpty() ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $expenseCategories->isEmpty() ? 'disabled' : '' }}>{{ __('Save') }}</button>
						<a href="{{ route('media-buyer.expenses') }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">{{ __('Cancel') }}</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</x-media-buyer-layout>

