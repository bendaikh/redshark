<x-admin-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Expense Categories') }}
		</h2>
	</x-slot>
	<div class="py-6">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
			<div class="mb-4">
				<a href="{{ route('expense-categories.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">{{ __('Add Expense Category') }}</a>
			</div>
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4">
				<div class="overflow-x-auto">
					<table class="min-w-full text-sm">
						<thead>
							<tr class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
								<th class="py-2">{{ __('Name') }}</th>
								<th class="py-2">{{ __('Description') }}</th>
								<th class="py-2">{{ __('Public') }}</th>
								<th class="py-2"></th>
							</tr>
						</thead>
						<tbody class="text-gray-900 dark:text-gray-100">
							@foreach($expenseCategories as $category)
								<tr class="border-t border-gray-200 dark:border-gray-700">
									<td class="py-2">{{ $category->name }}</td>
									<td class="py-2">{{ $category->description ?? '-' }}</td>
									<td class="py-2">
										<form action="{{ route('expense-categories.toggle-public', $category) }}" method="POST" class="inline">
											@csrf
											@method('PATCH')
											<button type="submit" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $category->is_public ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }} hover:opacity-80 transition">
												@if($category->is_public)
													<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
													</svg>
													{{ __('Public') }}
												@else
													<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
													</svg>
													{{ __('Private') }}
												@endif
											</button>
										</form>
									</td>
									<td class="py-2 text-right space-x-2">
										<a href="{{ route('expense-categories.edit', $category) }}" class="text-indigo-600 hover:underline">{{ __('Edit') }}</a>
										<form action="{{ route('expense-categories.destroy', $category) }}" method="POST" class="inline">
											@csrf @method('DELETE')
											<button type="submit" class="text-red-600 hover:underline" onclick="return confirm('{{ __('Delete this expense category?') }}')">{{ __('Delete') }}</button>
										</form>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
				<div class="mt-4">{{ $expenseCategories->links() }}</div>
			</div>
		</div>
	</div>
</x-admin-layout>

