<x-admin-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Ads Campaigns') }}
		</h2>
	</x-slot>
	<div class="py-4 sm:py-6">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
			<!-- Header with Add button and Total -->
			<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
				<div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
					<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 hidden sm:block">{{ __('Campaigns') }}</h3>
					<div class="px-3 py-1.5 bg-gray-100 dark:bg-gray-700 rounded-lg text-sm">
						{{ __('Total Spent') }}: <span class="font-semibold text-gray-900 dark:text-gray-100">{{ number_format($totalSpent, 2) }}</span>
					</div>
				</div>
				<a href="{{ route('ads-campaigns.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 active:bg-indigo-800 transition-colors touch-manipulation w-full sm:w-auto">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
					{{ __('Add Campaign') }}
				</a>
			</div>
			
			<!-- Filters -->
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl p-4">
				<form method="GET" class="space-y-3 sm:space-y-0 sm:grid sm:grid-cols-2 md:grid-cols-5 sm:gap-3">
					<div>
						<label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1.5">{{ __('From') }}</label>
						<input type="date" name="from" value="{{ $from }}" class="w-full py-2.5 px-3 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
					</div>
					<div>
						<label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1.5">{{ __('To') }}</label>
						<input type="date" name="to" value="{{ $to }}" class="w-full py-2.5 px-3 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
					</div>
					<div class="md:col-span-2">
						<label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1.5">{{ __('Country') }}</label>
						<select name="country_id" class="w-full py-2.5 px-3 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
							<option value="">{{ __('All') }}</option>
							@foreach($countries as $c)
								<option value="{{ $c->id }}" {{ (string)$countryId === (string)$c->id ? 'selected' : '' }}>{{ $c->name }}</option>
							@endforeach
						</select>
					</div>
					<div class="flex items-end">
						<button class="w-full sm:w-auto px-6 py-2.5 bg-gray-800 dark:bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-900 dark:hover:bg-gray-500 active:bg-gray-950 transition-colors touch-manipulation">{{ __('Filter') }}</button>
					</div>
				</form>
			</div>
			
			<!-- Table -->
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl">
				<div class="overflow-x-auto">
					<table class="min-w-full text-sm">
						<thead>
							<tr class="text-xs uppercase tracking-wider bg-gray-50 dark:bg-gray-900 text-gray-500 dark:text-gray-300">
								<th class="py-3 px-3 text-left whitespace-nowrap">{{ __('Date Range') }}</th>
								<th class="py-3 px-3 text-left whitespace-nowrap">{{ __('Name') }}</th>
								<th class="py-3 px-3 text-left whitespace-nowrap hidden sm:table-cell">{{ __('Platform') }}</th>
								<th class="py-3 px-3 text-left whitespace-nowrap hidden md:table-cell">{{ __('Products') }}</th>
								<th class="py-3 px-3 text-right whitespace-nowrap">{{ __('Amount') }}</th>
								<th class="py-3 px-3 text-left whitespace-nowrap hidden lg:table-cell">{{ __('Country') }}</th>
								<th class="py-3 px-3 text-center whitespace-nowrap">{{ __('Actions') }}</th>
							</tr>
						</thead>
						<tbody class="text-gray-700 dark:text-gray-200 divide-y divide-gray-100 dark:divide-gray-700">
							@forelse($campaigns as $a)
								<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
									<td class="py-3 px-3 whitespace-nowrap">
										@if($a->date_from && $a->date_to)
											<span class="text-sm">{{ $a->date_from->format('M d') }}</span>
											<span class="text-gray-400 mx-1">→</span>
											<span class="text-sm">{{ $a->date_to->format('M d') }}</span>
										@else
											<span class="text-gray-400">-</span>
										@endif
									</td>
									<td class="py-3 px-3">
										<div class="font-medium">{{ $a->name }}</div>
										<div class="text-xs text-gray-500 dark:text-gray-400 sm:hidden">{{ $a->platform->name ?? '-' }}</div>
									</td>
									<td class="py-3 px-3 hidden sm:table-cell">{{ $a->platform->name ?? '-' }}</td>
									<td class="py-3 px-3 hidden md:table-cell">
										<div class="max-w-xs">
											@foreach($a->products->take(2) as $product)
												<div class="text-xs text-gray-600 dark:text-gray-400 truncate">
													{{ $product->name }}: {{ number_format($product->pivot->amount_spent, 2) }}
												</div>
											@endforeach
											@if($a->products->count() > 2)
												<div class="text-xs text-gray-400">+{{ $a->products->count() - 2 }} {{ __('more') }}</div>
											@endif
										</div>
									</td>
									<td class="py-3 px-3 text-right tabular-nums font-medium">{{ number_format($a->total_amount_spent, 2) }}</td>
									<td class="py-3 px-3 hidden lg:table-cell">{{ $a->country->name }}</td>
									<td class="py-3 px-3">
										<div class="flex items-center justify-center gap-1">
											<a href="{{ route('ads-campaigns.edit', $a) }}" class="p-2 text-indigo-600 hover:text-indigo-800 hover:bg-indigo-100 dark:hover:bg-indigo-900/30 rounded-lg transition-colors touch-manipulation" title="{{ __('Edit') }}">
												<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
												</svg>
											</a>
											<form action="{{ route('ads-campaigns.destroy', $a) }}" method="POST" class="inline">
												@csrf @method('DELETE')
												<button type="submit" class="p-2 text-red-600 hover:text-red-800 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg transition-colors touch-manipulation" onclick="return confirm('{{ __('Delete this campaign?') }}')" title="{{ __('Delete') }}">
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
									<td colspan="7" class="px-4 py-12 text-center">
										<div class="text-gray-400 dark:text-gray-500">
											<svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
											</svg>
											<p class="text-gray-500 dark:text-gray-400">{{ __('No campaigns found.') }}</p>
										</div>
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>
				@if($campaigns->hasPages())
					<div class="px-4 py-4 border-t border-gray-100 dark:border-gray-700">
						{{ $campaigns->links() }}
					</div>
				@endif
			</div>
		</div>
	</div>
</x-admin-layout>
