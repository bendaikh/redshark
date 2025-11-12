<x-admin-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Ads Campaigns') }}
		</h2>
	</x-slot>
	<div class="py-6">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4">
				<form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3">
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('From') }}</label>
						<input type="date" name="from" value="{{ $from }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100">
					</div>
					<div>
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('To') }}</label>
						<input type="date" name="to" value="{{ $to }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100">
					</div>
					<div class="md:col-span-2">
						<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Country') }}</label>
						<select name="country_id" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100">
							<option value="">{{ __('All') }}</option>
							@foreach($countries as $c)
								<option value="{{ $c->id }}" {{ (string)$countryId === (string)$c->id ? 'selected' : '' }}>{{ $c->name }}</option>
							@endforeach
						</select>
					</div>
					<div class="flex items-end">
						<button class="px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-800">{{ __('Filter') }}</button>
					</div>
				</form>
			</div>
			<div class="flex items-center justify-between">
				<div class="text-gray-500">{{ __('Total Spent') }}: <span class="font-semibold text-gray-900 dark:text-gray-100">{{ number_format($totalSpent, 2) }}</span></div>
				<a href="{{ route('ads-campaigns.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">{{ __('Add Campaign') }}</a>
			</div>
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4">
				<div class="overflow-x-auto">
					<table class="min-w-full text-sm">
						<thead>
							<tr class="text-left text-gray-500">
								<th class="py-2">{{ __('Date Range') }}</th>
								<th class="py-2">{{ __('Name') }}</th>
								<th class="py-2">{{ __('Platform') }}</th>
								<th class="py-2">{{ __('Products') }}</th>
								<th class="py-2">{{ __('Total Amount') }}</th>
								<th class="py-2">{{ __('Country') }}</th>
								<th class="py-2"></th>
							</tr>
						</thead>
						<tbody class="text-gray-900 dark:text-gray-100">
							@foreach($campaigns as $a)
								<tr class="border-t border-gray-200 dark:border-gray-700">
									<td class="py-2">
										@if($a->date_from && $a->date_to)
											{{ $a->date_from->format('Y-m-d') }} - {{ $a->date_to->format('Y-m-d') }}
										@else
											-
										@endif
									</td>
									<td class="py-2">{{ $a->name }}</td>
									<td class="py-2">{{ $a->platform->name ?? '-' }}</td>
									<td class="py-2">
										@foreach($a->products as $product)
											<div class="text-xs">{{ $product->name }}: {{ number_format($product->pivot->amount_spent, 2) }}</div>
										@endforeach
									</td>
									<td class="py-2">{{ number_format($a->total_amount_spent, 2) }}</td>
									<td class="py-2">{{ $a->country->name }}</td>
									<td class="py-2 text-right space-x-2">
										<a href="{{ route('ads-campaigns.edit', $a) }}" class="text-indigo-600 hover:underline">{{ __('Edit') }}</a>
										<form action="{{ route('ads-campaigns.destroy', $a) }}" method="POST" class="inline">
											@csrf @method('DELETE')
											<button type="submit" class="text-red-600 hover:underline" onclick="return confirm('{{ __('Delete this campaign?') }}')">{{ __('Delete') }}</button>
										</form>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
				<div class="mt-4">{{ $campaigns->links() }}</div>
			</div>
		</div>
	</div>
</x-admin-layout>

