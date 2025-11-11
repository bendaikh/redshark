<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Country Dashboard') }} @if($country) - {{ $country->name }} @endif
		</h2>
	</x-slot>

	<div class="py-6">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4">
				<form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3">
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('From') }}</label>
						<input type="date" name="from" value="{{ request('from') }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100">
					</div>
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('To') }}</label>
						<input type="date" name="to" value="{{ request('to') }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100">
					</div>
					<div class="flex items-end">
						<button class="px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-800">{{ __('Apply') }}</button>
					</div>
				</form>
			</div>
			<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
				<div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
					<div class="text-gray-500 text-sm">{{ __('Total Invoices') }}</div>
					<div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalInvoices }}</div>
				</div>
				<div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
					<div class="text-gray-500 text-sm">{{ __('Stock Quantity') }}</div>
					<div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalStockQty }}</div>
				</div>
				<div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
					<div class="text-gray-500 text-sm">{{ __('Ads Spent') }}</div>
					<div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($adsSpent, 2) }}</div>
				</div>
			</div>

			<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
				<div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow">
					<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ __('Stock Value') }}</h3>
					<div class="text-3xl font-bold text-emerald-600">{{ number_format($stockValue, 2) }}</div>
				</div>
				<div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow">
					<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ __('Profit Potential') }}</h3>
					<div class="text-3xl font-bold text-indigo-600">{{ number_format($profitPotential, 2) }}</div>
				</div>
			</div>

			<div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Low Stock Alerts') }}</h3>
				@if($lowStock->isEmpty())
					<p class="text-gray-500">{{ __('No low stock items.') }}</p>
				@else
					<div class="overflow-x-auto">
						<table class="min-w-full text-sm">
							<thead>
								<tr class="text-left text-gray-500">
									<th class="py-2">{{ __('Product') }}</th>
									<th class="py-2">{{ __('Category') }}</th>
									<th class="py-2">{{ __('Qty') }}</th>
									<th class="py-2">{{ __('Threshold') }}</th>
								</tr>
							</thead>
							<tbody class="text-gray-900 dark:text-gray-100">
								@foreach($lowStock as $p)
									<tr class="border-t border-gray-200 dark:border-gray-700">
										<td class="py-2">{{ $p->name }}</td>
										<td class="py-2">{{ $p->category }}</td>
										<td class="py-2">{{ $p->quantity }}</td>
										<td class="py-2">{{ $p->low_stock_threshold }}</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				@endif
			</div>
		</div>
	</div>
</x-app-layout>

