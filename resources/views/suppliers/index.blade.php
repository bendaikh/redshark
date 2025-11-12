<x-admin-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Suppliers') }}
		</h2>
	</x-slot>
	<div class="py-6">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
			<div class="mb-4">
				<a href="{{ route('suppliers.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">{{ __('Add Supplier') }}</a>
			</div>
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4">
				<div class="overflow-x-auto">
					<table class="min-w-full text-sm">
						<thead>
							<tr class="text-left text-gray-500">
								<th class="py-2">{{ __('Name') }}</th>
								<th class="py-2">{{ __('Contact Email') }}</th>
								<th class="py-2">{{ __('Phone') }}</th>
								<th class="py-2">{{ __('Country') }}</th>
								<th class="py-2"></th>
							</tr>
						</thead>
						<tbody class="text-gray-900 dark:text-gray-100">
							@foreach($suppliers as $supplier)
								<tr class="border-t border-gray-200 dark:border-gray-700">
									<td class="py-2">{{ $supplier->name }}</td>
									<td class="py-2">{{ $supplier->contact_email ?? __('N/A') }}</td>
									<td class="py-2">{{ $supplier->phone ?? __('N/A') }}</td>
									<td class="py-2">{{ $supplier->country?->name ?? __('N/A') }}</td>
									<td class="py-2 text-right space-x-2">
										<a href="{{ route('suppliers.edit', $supplier) }}" class="text-indigo-600 hover:underline">{{ __('Edit') }}</a>
										<form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="inline">
											@csrf @method('DELETE')
											<button type="submit" class="text-red-600 hover:underline" onclick="return confirm('{{ __('Delete this supplier?') }}')">{{ __('Delete') }}</button>
										</form>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
				<div class="mt-4">{{ $suppliers->links() }}</div>
			</div>
		</div>
	</div>
</x-admin-layout>

