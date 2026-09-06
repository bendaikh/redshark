<x-admin-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Ads Campaigns') }}
		</h2>
	</x-slot>
	<div class="py-4 sm:py-6" x-data="campaignBulkActions()">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
			@if (session('status'))
				<div class="rounded-lg bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 px-4 py-3 text-sm text-green-800 dark:text-green-200">
					{{ session('status') }}
				</div>
			@endif

			@if ($errors->any())
				<div class="rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 px-4 py-3 text-sm text-red-800 dark:text-red-200">
					<ul class="list-disc list-inside space-y-1">
						@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
			@endif

			@php
				$importedIds = request()->filled('imported')
					? array_filter(array_map('intval', explode(',', request('imported'))))
					: [];
			@endphp

			<!-- Header with Add / Import buttons and Total -->
			<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
				<div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
					<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 hidden sm:block">{{ __('Campaigns') }}</h3>
					<div class="px-3 py-1.5 bg-gray-100 dark:bg-gray-700 rounded-lg text-sm">
						{{ __('Total Spent') }}: <span class="font-semibold text-gray-900 dark:text-gray-100">{{ number_format($totalSpent, 2) }}</span>
					</div>
				</div>
				<div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
					<button type="button" @click="showImport = true" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors touch-manipulation w-full sm:w-auto">
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
						{{ __('Import') }}
					</button>
					<a href="{{ route('ads-campaigns.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 active:bg-indigo-800 transition-colors touch-manipulation w-full sm:w-auto">
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
						{{ __('Add Campaign') }}
					</a>
				</div>
			</div>

			<!-- Import Modal -->
			<div x-show="showImport" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
				<div class="absolute inset-0 bg-black/50" @click="showImport = false"></div>
				<div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6">
					<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ __('Import Campaigns') }}</h3>
					<p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
						{{ __('Upload an Excel or CSV file with columns: Campaign name, Amount spent (USD), Results. All rows are imported into one campaign. Products are matched using the code before the hyphen in each campaign name (e.g. "ug01" from "ug01- Antifuite") against the product Import ID.') }}
						@if($currentCountry ?? null)
							<span class="block mt-2 font-medium text-gray-800 dark:text-gray-200">
								{{ __('Country') }}: {{ $currentCountry->name }}
							</span>
						@else
							<span class="block mt-2 text-amber-600 dark:text-amber-400">
								{{ __('Please select a country from the top-right dropdown before importing.') }}
							</span>
						@endif
					</p>
					<form method="POST" action="{{ route('ads-campaigns.import') }}" enctype="multipart/form-data" class="space-y-4">
						@csrf
						<div>
							<input type="file" name="file" accept=".xlsx,.xls,.csv" required class="block w-full text-sm text-gray-600 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-900/30 dark:file:text-indigo-300">
						</div>
						<div class="flex justify-end gap-2">
							<button type="button" @click="showImport = false" class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
								{{ __('Cancel') }}
							</button>
							<button type="submit" @if(!($currentCountry ?? null)) disabled @endif class="px-4 py-2 text-sm rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed">
								{{ __('Import') }}
							</button>
						</div>
					</form>
				</div>
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

			<!-- Bulk Actions -->
			<form method="POST" action="{{ route('ads-campaigns.bulk-update') }}" x-ref="bulkForm">
				@csrf
				<input type="hidden" name="from" value="{{ $from }}">
				<input type="hidden" name="to" value="{{ $to }}">
				<input type="hidden" name="country_id" value="{{ $countryId }}">

				<div x-show="selectedCount > 0" x-cloak class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-xl p-4 flex flex-col lg:flex-row lg:items-end gap-3" style="display: none;">
					<div class="text-sm font-medium text-indigo-900 dark:text-indigo-200 lg:pt-2">
						<span x-text="selectedCount"></span> {{ __('selected') }}
					</div>
					<div class="flex-1 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
						<div>
							<label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">{{ __('Platform') }}</label>
							<select name="platform_id" class="w-full py-2 px-3 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm">
								<option value="">{{ __('Keep unchanged') }}</option>
								@foreach($platforms as $platform)
									<option value="{{ $platform->id }}">{{ $platform->name }}</option>
								@endforeach
							</select>
						</div>
						<div>
							<label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">{{ __('Date From') }}</label>
							<input type="date" name="date_from" class="w-full py-2 px-3 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm">
						</div>
						<div>
							<label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">{{ __('Date To') }}</label>
							<input type="date" name="date_to" class="w-full py-2 px-3 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm">
						</div>
						<div class="flex items-end">
							<button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
								{{ __('Apply to selected') }}
							</button>
						</div>
					</div>
				</div>

				<!-- Table -->
				<div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl mt-4">
					<div class="overflow-x-auto">
						<table class="min-w-full text-sm">
							<thead>
								<tr class="text-xs uppercase tracking-wider bg-gray-50 dark:bg-gray-900 text-gray-500 dark:text-gray-300">
									<th class="py-3 px-3 text-left w-10">
										<input type="checkbox" @change="toggleAll($event)" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500">
									</th>
									<th class="py-3 px-3 text-left whitespace-nowrap">{{ __('Date Range') }}</th>
									<th class="py-3 px-3 text-left whitespace-nowrap">{{ __('Name') }}</th>
									<th class="py-3 px-3 text-left whitespace-nowrap hidden sm:table-cell">{{ __('Platform') }}</th>
									<th class="py-3 px-3 text-left whitespace-nowrap hidden md:table-cell">{{ __('Products') }}</th>
									<th class="py-3 px-3 text-right whitespace-nowrap">{{ __('Amount') }}</th>
									<th class="py-3 px-3 text-right whitespace-nowrap hidden lg:table-cell">{{ __('Results') }}</th>
									<th class="py-3 px-3 text-left whitespace-nowrap hidden lg:table-cell">{{ __('Country') }}</th>
									<th class="py-3 px-3 text-center whitespace-nowrap">{{ __('Actions') }}</th>
								</tr>
							</thead>
							<tbody class="text-gray-700 dark:text-gray-200 divide-y divide-gray-100 dark:divide-gray-700">
								@forelse($campaigns as $a)
									@php
										$isImported = in_array($a->id, $importedIds, true);
										$needsSetup = ! $a->platform_id || ! $a->date_from || ! $a->date_to;
									@endphp
									<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors {{ $isImported ? 'bg-amber-50/80 dark:bg-amber-900/10' : '' }} {{ $needsSetup ? 'ring-1 ring-inset ring-amber-200 dark:ring-amber-800/50' : '' }}">
										<td class="py-3 px-3">
											<input
												type="checkbox"
												name="campaign_ids[]"
												value="{{ $a->id }}"
												@if($isImported) checked @endif
												@change="updateSelected()"
												class="campaign-checkbox rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500"
											>
										</td>
										<td class="py-3 px-3 whitespace-nowrap">
											@if($a->date_from && $a->date_to)
												<span class="text-sm">{{ $a->date_from->format('M d') }}</span>
												<span class="text-gray-400 mx-1">→</span>
												<span class="text-sm">{{ $a->date_to->format('M d') }}</span>
											@else
												<span class="text-amber-600 dark:text-amber-400 text-xs">{{ __('Not set') }}</span>
											@endif
										</td>
										<td class="py-3 px-3">
											<div class="font-medium">{{ $a->name }}</div>
											<div class="text-xs text-gray-500 dark:text-gray-400 sm:hidden">{{ $a->platform->name ?? __('Not set') }}</div>
										</td>
										<td class="py-3 px-3 hidden sm:table-cell">
											@if($a->platform)
												{{ $a->platform->name }}
											@else
												<span class="text-amber-600 dark:text-amber-400 text-xs">{{ __('Not set') }}</span>
											@endif
										</td>
										<td class="py-3 px-3 hidden md:table-cell">
											<div class="max-w-xs">
												@if($a->products->isNotEmpty())
													@foreach($a->products->take(2) as $product)
														<div class="text-xs text-gray-600 dark:text-gray-400 truncate">
															{{ $product->name }}: {{ number_format($product->pivot->amount_spent, 2) }}
														</div>
													@endforeach
													@if($a->products->count() > 2)
														<div class="text-xs text-gray-400">+{{ $a->products->count() - 2 }} {{ __('more') }}</div>
													@endif
												@else
													<span class="text-xs text-gray-400">-</span>
												@endif
											</div>
										</td>
										<td class="py-3 px-3 text-right tabular-nums font-medium">{{ number_format($a->total_amount_spent, 2) }}</td>
										<td class="py-3 px-3 text-right tabular-nums hidden lg:table-cell">{{ number_format($a->total_leads) }}</td>
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
										<td colspan="9" class="px-4 py-12 text-center">
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
			</form>
		</div>
	</div>

	<script>
		function campaignBulkActions() {
			return {
				showImport: false,
				selectedCount: 0,
				updateSelected() {
					this.selectedCount = document.querySelectorAll('.campaign-checkbox:checked').length;
				},
				toggleAll(event) {
					document.querySelectorAll('.campaign-checkbox').forEach((checkbox) => {
						checkbox.checked = event.target.checked;
					});
					this.updateSelected();
				},
				init() {
					this.updateSelected();
				},
			};
		}
	</script>
</x-admin-layout>
