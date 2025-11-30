<x-admin-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Suppliers') }}
		</h2>
	</x-slot>
	<div class="py-4 sm:py-6">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
			<!-- Header with Add button -->
			<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 hidden sm:block">{{ __('Suppliers') }}</h3>
				<a href="{{ route('suppliers.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 active:bg-indigo-800 transition-colors touch-manipulation w-full sm:w-auto">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
					{{ __('Add Supplier') }}
				</a>
			</div>
			
			<!-- Table -->
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl">
				<div class="overflow-x-auto">
					<table class="min-w-full text-sm">
						<thead>
							<tr class="text-xs uppercase tracking-wider bg-gray-50 dark:bg-gray-900 text-gray-500 dark:text-gray-300">
								<th class="py-3 px-3 text-left whitespace-nowrap">{{ __('Name') }}</th>
								<th class="py-3 px-3 text-left whitespace-nowrap hidden sm:table-cell">{{ __('Email') }}</th>
								<th class="py-3 px-3 text-left whitespace-nowrap hidden md:table-cell">{{ __('Phone') }}</th>
								<th class="py-3 px-3 text-left whitespace-nowrap hidden lg:table-cell">{{ __('Country') }}</th>
								<th class="py-3 px-3 text-center whitespace-nowrap">{{ __('Actions') }}</th>
							</tr>
						</thead>
						<tbody class="text-gray-700 dark:text-gray-200 divide-y divide-gray-100 dark:divide-gray-700">
							@forelse($suppliers as $supplier)
								<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
									<td class="py-3 px-3">
										<div class="min-w-0">
											<div class="font-medium text-gray-900 dark:text-gray-100">{{ $supplier->name }}</div>
											<div class="text-xs text-gray-500 dark:text-gray-400 sm:hidden">{{ $supplier->contact_email ?? '-' }}</div>
										</div>
									</td>
									<td class="py-3 px-3 hidden sm:table-cell">
										<span class="text-gray-600 dark:text-gray-400">{{ $supplier->contact_email ?? '-' }}</span>
									</td>
									<td class="py-3 px-3 hidden md:table-cell">
										<span class="text-gray-600 dark:text-gray-400">{{ $supplier->phone ?? '-' }}</span>
									</td>
									<td class="py-3 px-3 hidden lg:table-cell">
										<span class="text-gray-600 dark:text-gray-400">{{ $supplier->country?->name ?? '-' }}</span>
									</td>
									<td class="py-3 px-3">
										<div class="flex items-center justify-center gap-1">
											<a href="{{ route('suppliers.edit', $supplier) }}" class="p-2 text-indigo-600 hover:text-indigo-800 hover:bg-indigo-100 dark:hover:bg-indigo-900/30 rounded-lg transition-colors touch-manipulation" title="{{ __('Edit') }}">
												<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
												</svg>
											</a>
											<form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="inline">
												@csrf @method('DELETE')
												<button type="submit" class="p-2 text-red-600 hover:text-red-800 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg transition-colors touch-manipulation" onclick="return confirm('{{ __('Delete this supplier?') }}')" title="{{ __('Delete') }}">
													<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
													</svg>
												</button>
											</form>
										</div>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="5" class="px-4 py-12 text-center">
										<div class="text-gray-400 dark:text-gray-500">
											<svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
											</svg>
											<p class="text-gray-500 dark:text-gray-400">{{ __('No suppliers found.') }}</p>
										</div>
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>
				@if($suppliers->hasPages())
					<div class="px-4 py-4 border-t border-gray-100 dark:border-gray-700">
						{{ $suppliers->links() }}
					</div>
				@endif
			</div>
		</div>
	</div>
</x-admin-layout>
