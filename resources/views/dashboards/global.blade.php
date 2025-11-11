<x-admin-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-2xl text-gray-900 dark:text-gray-100 leading-tight">
			{{ __('Global Dashboard') }}
		</h2>
	</x-slot>

	<div class="space-y-6">
		<!-- Filters -->
		<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4">
			<form method="GET" class="grid grid-cols-1 md:grid-cols-[1fr_1fr_auto] gap-3 items-end">
				<div>
					<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('From') }}</label>
					<div class="mt-1 relative">
						<input type="date" name="from" value="{{ request('from') }}" class="w-full rounded-md border-gray-300 pl-3 pr-10 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
						<div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
							<svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M7 10h10M7 14h6M7 2h10a2 2 0 0 1 2 2v3H5V4a2 2 0 0 1 2-2zM5 9h14v9a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2z"/></svg>
						</div>
					</div>
				</div>
				<div>
					<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('To') }}</label>
					<div class="mt-1 relative">
						<input type="date" name="to" value="{{ request('to') }}" class="w-full rounded-md border-gray-300 pl-3 pr-10 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
						<div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
							<svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M7 10h10M7 14h6M7 2h10a2 2 0 0 1 2 2v3H5V4a2 2 0 0 1 2-2zM5 9h14v9a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2z"/></svg>
						</div>
					</div>
				</div>
				<div class="flex md:justify-end">
					<button class="px-4 py-2 h-10 md:h-auto bg-gray-900 text-white rounded-md hover:bg-gray-800 dark:bg-indigo-600 dark:hover:bg-indigo-500">{{ __('Apply') }}</button>
				</div>
			</form>
		</div>

		<!-- KPI Cards -->
		<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
			<div class="p-5 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
				<div class="flex items-center justify-between">
					<div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Total Countries') }}</div>
					<div class="h-8 w-8 rounded-md bg-indigo-50 text-indigo-600 dark:bg-indigo-900/40 dark:text-indigo-300 flex items-center justify-center">
						<svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 1 0 10 10A10.012 10.012 0 0 0 12 2zm1 17.93A8.001 8.001 0 0 0 20 12h-3a5 5 0 0 1-4 4.9zM4 12a8.001 8.001 0 0 0 7 7.93V16a4 4 0 0 1-4-4z"/></svg>
					</div>
				</div>
				<div class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $totalCountries }}</div>
			</div>
			<div class="p-5 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
				<div class="flex items-center justify-between">
					<div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Total Invoices') }}</div>
					<div class="h-8 w-8 rounded-md bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-300 flex items-center justify-center">
						<svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M8 2h8a2 2 0 0 1 2 2v18l-6-3-6 3V4a2 2 0 0 1 2-2z"/></svg>
					</div>
				</div>
				<div class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $totalInvoices }}</div>
			</div>
			<div class="p-5 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
				<div class="flex items-center justify-between">
					<div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Total Expenses') }}</div>
					<div class="h-8 w-8 rounded-md bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-300 flex items-center justify-center">
						<svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 1l3 7h7l-5.5 4.2L18 21l-6-4-6 4 1.5-8.8L2 8h7z"/></svg>
					</div>
				</div>
				<div class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">${{ number_format($totalExpenses, 2) }}</div>
			</div>
			<div class="p-5 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
				<div class="flex items-center justify-between">
					<div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Total Stock Value') }}</div>
					<div class="h-8 w-8 rounded-md bg-sky-50 text-sky-600 dark:bg-sky-900/30 dark:text-sky-300 flex items-center justify-center">
						<svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3a9 9 0 1 0 9 9h-2A7 7 0 1 1 12 5V3z"/><path d="M12 7h8v2h-8z"/></svg>
					</div>
				</div>
				<div class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">${{ number_format($totalStockValue, 0) }}</div>
			</div>
		</div>

		<!-- Table -->
		<div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
			<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">{{ __('By Country Overview') }}</h3>
			<p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ __('Breakdown of key metrics by country.') }}</p>

			<div class="overflow-x-auto">
				<table class="min-w-full text-sm">
					<thead>
						<tr class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
							<th class="py-3 pe-2">{{ __('Country') }}</th>
							<th class="py-3 pe-2">{{ __('Products') }}</th>
							<th class="py-3 pe-2">{{ __('Invoices') }}</th>
							<th class="py-3 pe-2">{{ __('Total Value') }}</th>
						</tr>
					</thead>
					<tbody class="text-gray-900 dark:text-gray-100">
						@foreach($byCountry as $row)
							<tr class="border-b border-gray-100 dark:border-gray-700/60">
								<td class="py-3 pe-2 font-medium">{{ $row->name }}</td>
								<td class="py-3 pe-2">{{ number_format($row->products_count ?? 0) }}</td>
								<td class="py-3 pe-2">{{ number_format($row->invoices_count ?? 0) }}</td>
								<td class="py-3 pe-2">${{ number_format($row->stock_value ?? 0, 2) }}</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</x-admin-layout>

