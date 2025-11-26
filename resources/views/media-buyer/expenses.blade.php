<x-media-buyer-layout>
	<x-slot name="header">
		<div class="flex items-center justify-between">
			<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
				{{ __('My Expenses') }}
			</h2>
			<a href="{{ route('media-buyer.expenses.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
				<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
				</svg>
				{{ __('Add Expense') }}
			</a>
		</div>
	</x-slot>

	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
			@if (session('status'))
				<div class="mb-4 px-4 py-3 bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 rounded-md">
					{{ session('status') }}
				</div>
			@endif

			@if($expenses->isEmpty())
				<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-8 text-center">
					<svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
					</svg>
					<h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('No expenses yet') }}</h3>
					<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Start by adding your first expense.') }}</p>
					<div class="mt-6">
						<a href="{{ route('media-buyer.expenses.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
							{{ __('Add Expense') }}
						</a>
					</div>
				</div>
			@else
				<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
					<div class="overflow-x-auto">
						<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
							<thead class="bg-gray-50 dark:bg-gray-900">
								<tr>
									<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
										{{ __('Date') }}
									</th>
									<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
										{{ __('Category') }}
									</th>
									<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
										{{ __('Country') }}
									</th>
									<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
										{{ __('Amount') }}
									</th>
									<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
										{{ __('Description') }}
									</th>
									<th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
										{{ __('Actions') }}
									</th>
								</tr>
							</thead>
							<tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
								@foreach($expenses as $expense)
									<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
										<td class="px-6 py-4 whitespace-nowrap">
											<div class="text-sm text-gray-900 dark:text-gray-100">
												{{ $expense->date->format('Y-m-d') }}
											</div>
										</td>
										<td class="px-6 py-4 whitespace-nowrap">
											<div class="text-sm font-medium text-gray-900 dark:text-gray-100">
												{{ $expense->expenseCategory?->name ?? '-' }}
											</div>
										</td>
										<td class="px-6 py-4 whitespace-nowrap">
											<div class="text-sm text-gray-500 dark:text-gray-400">
												{{ $expense->country?->name ?? '-' }}
											</div>
										</td>
										<td class="px-6 py-4 whitespace-nowrap">
											<div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
												{{ number_format($expense->amount, 2) }}
											</div>
										</td>
										<td class="px-6 py-4">
											<div class="text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate">
												{{ $expense->description ?? '-' }}
											</div>
										</td>
										<td class="px-6 py-4 whitespace-nowrap text-center">
											<form action="{{ route('media-buyer.expenses.destroy', $expense) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Delete this expense?') }}')">
												@csrf @method('DELETE')
												<button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300" title="{{ __('Delete') }}">
													<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
													</svg>
												</button>
											</form>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>

				@if($expenses->hasPages())
					<div class="mt-6">
						{{ $expenses->links() }}
					</div>
				@endif
			@endif
		</div>
	</div>
</x-media-buyer-layout>

