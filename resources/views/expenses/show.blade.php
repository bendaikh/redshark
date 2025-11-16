<x-admin-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Expense Details') }}
		</h2>
	</x-slot>
	<div class="py-6">
		<div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
				<div class="space-y-4">
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Expense Category') }}</label>
						<p class="mt-1 text-gray-900 dark:text-gray-100">{{ $expense->expenseCategory->name }}</p>
					</div>
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Country') }}</label>
						<p class="mt-1 text-gray-900 dark:text-gray-100">{{ $expense->country->name }}</p>
					</div>
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Amount') }}</label>
						<p class="mt-1 text-gray-900 dark:text-gray-100 font-medium">{{ number_format($expense->amount, 2) }}</p>
					</div>
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Date') }}</label>
						<p class="mt-1 text-gray-900 dark:text-gray-100">{{ $expense->date->format('Y-m-d') }}</p>
					</div>
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Description') }}</label>
						<p class="mt-1 text-gray-900 dark:text-gray-100">{{ $expense->description ?? '-' }}</p>
					</div>
					<div class="pt-4">
						<a href="{{ route('expenses.edit', $expense) }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">{{ __('Edit') }}</a>
						<a href="{{ route('expenses.index') }}" class="ms-2 text-gray-600 hover:underline">{{ __('Back') }}</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</x-admin-layout>

