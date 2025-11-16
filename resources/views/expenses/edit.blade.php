<x-admin-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Edit Expense') }}
		</h2>
	</x-slot>
	<div class="py-6">
		<div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
				<form method="POST" action="{{ route('expenses.update', $expense) }}" class="space-y-4">
					@csrf @method('PUT')
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Expense Category') }}</label>
						<select name="expense_category_id" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700" required>
							<option value="">{{ __('Select Category') }}</option>
							@foreach($expenseCategories as $category)
								<option value="{{ $category->id }}" {{ old('expense_category_id', $expense->expense_category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
							@endforeach
						</select>
						@error('expense_category_id')
							<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
						@enderror
					</div>
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Country') }}</label>
						<select name="country_id" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700" required>
							<option value="">{{ __('Select Country') }}</option>
							@foreach($countries as $country)
								<option value="{{ $country->id }}" {{ old('country_id', $expense->country_id) == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
							@endforeach
						</select>
						@error('country_id')
							<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
						@enderror
					</div>
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Amount') }}</label>
						<input type="number" name="amount" step="0.01" min="0" value="{{ old('amount', $expense->amount) }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700" required>
						@error('amount')
							<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
						@enderror
					</div>
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Date') }}</label>
						<input type="date" name="date" value="{{ old('date', $expense->date->format('Y-m-d')) }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700" required>
						@error('date')
							<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
						@enderror
					</div>
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Description') }}</label>
						<textarea name="description" rows="3" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700">{{ old('description', $expense->description) }}</textarea>
						@error('description')
							<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
						@enderror
					</div>
					<div class="pt-4">
						<button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">{{ __('Save') }}</button>
						<a href="{{ route('expenses.index') }}" class="ms-2 text-gray-600 hover:underline">{{ __('Cancel') }}</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</x-admin-layout>

