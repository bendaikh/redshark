<x-admin-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-2xl text-gray-900 dark:text-gray-100 leading-tight">
			{{ __('Settings') }}
		</h2>
	</x-slot>

	<div class="space-y-6">
		@if (session('status'))
			<div class="bg-green-100 dark:bg-green-900 border border-green-400 text-green-700 dark:text-green-300 px-4 py-3 rounded relative" role="alert">
				<span class="block sm:inline">{{ session('status') }}</span>
			</div>
		@endif

		<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
			<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Application Settings') }}</h3>
			<p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Configure your application settings here.') }}</p>
		</div>

		<!-- Delivery Fees Settings -->
		<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
			<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Delivery Fees Settings') }}</h3>
			<p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ __('Manage delivery fees (per unit) that will be used as defaults in various sections of the application.') }}</p>
			
			<!-- Add New Delivery Fee Form -->
			<div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
				<h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-3">{{ __('Add New Delivery Fee') }}</h4>
				<form method="POST" action="{{ route('admin.settings.delivery-fees.store') }}" class="space-y-4">
					@csrf
					<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
						<div>
							<label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
								{{ __('Name') }} <span class="text-gray-400 text-xs">({{ __('Optional') }})</span>
							</label>
							<input 
								type="text" 
								name="name" 
								id="name"
								value="{{ old('name') }}"
								class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700"
								placeholder="{{ __('e.g., Standard Delivery') }}"
							>
							@error('name')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>

						<div>
							<label for="fee_per_unit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
								{{ __('Fee per Unit') }} <span class="text-red-500">*</span>
							</label>
							<input 
								type="number" 
								name="fee_per_unit" 
								id="fee_per_unit"
								value="{{ old('fee_per_unit') }}"
								step="0.01"
								min="0"
								required
								class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700"
								placeholder="0.00"
							>
							@error('fee_per_unit')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>

						<div class="flex items-end">
							<div class="flex items-center w-full">
								<input 
									type="checkbox" 
									name="active" 
									id="active"
									value="1" 
									checked 
									class="me-2"
								>
								<label for="active" class="text-sm text-gray-600 dark:text-gray-300">{{ __('Active') }}</label>
							</div>
						</div>
					</div>

					<div class="pt-2">
						<button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">{{ __('Add Delivery Fee') }}</button>
					</div>
				</form>
			</div>

			<!-- Delivery Fees List -->
			<div>
				<h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-3">{{ __('Delivery Fees List') }}</h4>
				
				@if(isset($deliveryFees) && $deliveryFees->count())
					<div class="overflow-x-auto">
						<table class="min-w-full text-sm">
							<thead>
								<tr class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
									<th class="py-2 pe-2">{{ __('Name') }}</th>
									<th class="py-2 pe-2">{{ __('Fee per Unit') }}</th>
									<th class="py-2 pe-2">{{ __('Active') }}</th>
									<th class="py-2 pe-2 text-right">{{ __('Actions') }}</th>
								</tr>
							</thead>
							<tbody class="text-gray-900 dark:text-gray-100">
								@foreach($deliveryFees as $fee)
									<tr class="border-b border-gray-100 dark:border-gray-700/60">
										<td class="py-2 pe-2">{{ $fee->name ?: __('Unnamed') }}</td>
										<td class="py-2 pe-2 font-medium">{{ number_format($fee->fee_per_unit, 2) }}</td>
										<td class="py-2 pe-2">
											<span class="px-2 py-1 rounded text-xs {{ $fee->active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-200 text-gray-700' }}">{{ $fee->active ? __('Yes') : __('No') }}</span>
										</td>
										<td class="py-2 pe-2">
											<div class="flex items-center justify-end gap-2">
												<!-- Edit Button -->
												<button 
													type="button"
													onclick="editDeliveryFee({{ $fee->id }}, '{{ addslashes($fee->name) }}', {{ $fee->fee_per_unit }}, {{ $fee->active ? 'true' : 'false' }})"
													class="inline-flex items-center p-1.5 rounded hover:bg-blue-50 dark:hover:bg-blue-900/20 text-blue-600 dark:text-blue-400" 
													title="{{ __('Edit') }}"
												>
													<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
														<path d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 0 0 0-3.712ZM19.513 8.929l-3.712-3.712-8.4 8.4a5.25 5.25 0 0 0-1.32 2.214l-.8 2.685a.75.75 0 0 0 .933.933l2.685-.8a5.25 5.25 0 0 0 2.214-1.32l8.4-8.4Z" />
														<path d="M5.25 5.25a3 3 0 0 0-3 3v10.5a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3V13.5a.75.75 0 0 0-1.5 0v5.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5V8.25a1.5 1.5 0 0 1 1.5-1.5h5.25a.75.75 0 0 0 0-1.5H5.25Z" />
													</svg>
													<span class="sr-only">{{ __('Edit') }}</span>
												</button>
												<!-- Delete Button -->
												<form 
													action="{{ route('admin.settings.delivery-fees.destroy', $fee) }}" 
													method="POST" 
													class="inline" 
													onsubmit="return confirm('{{ __('Delete this delivery fee?') }}')"
												>
													@csrf
													@method('DELETE')
													<button 
														type="submit" 
														class="inline-flex items-center p-1.5 rounded hover:bg-red-50 dark:hover:bg-red-900/20 text-red-600 dark:text-red-400" 
														title="{{ __('Delete') }}"
													>
														<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
															<path fill-rule="evenodd" d="M9 3.75A2.25 2.25 0 0 1 11.25 1.5h1.5A2.25 2.25 0 0 1 15 3.75V4.5h3.75a.75.75 0 0 1 0 1.5h-.42l-1.07 13.367A2.25 2.25 0 0 1 15.02 22.5H8.98a2.25 2.25 0 0 1-2.241-2.133L5.67 6h-.42a.75.75 0 0 1 0-1.5H9V3.75Zm1.5.75h3V3.75a.75.75 0 0 0-.75-.75h-1.5a.75.75 0 0 0-.75.75V4.5ZM7.173 6l1.06 13.25a.75.75 0 0 0 .747.7h6.048a.75.75 0 0 0 .747-.7L16.827 6H7.173ZM9.75 8.25a.75.75 0 0 1 .75.75v8.25a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm4.5 0A.75.75 0 0 1 15 9v8.25a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Z" clip-rule="evenodd" />
														</svg>
														<span class="sr-only">{{ __('Delete') }}</span>
													</button>
												</form>
											</div>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				@else
					<p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No delivery fees yet. Add one using the form above.') }}</p>
				@endif
			</div>
		</div>

		<!-- Edit Delivery Fee Modal -->
		<div id="editModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
			<div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
				<div class="mt-3">
					<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Edit Delivery Fee') }}</h3>
					<form id="editForm" method="POST" class="space-y-4">
						@csrf
						@method('PUT')
						<div>
							<label for="edit_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
								{{ __('Name') }} <span class="text-gray-400 text-xs">({{ __('Optional') }})</span>
							</label>
							<input 
								type="text" 
								name="name" 
								id="edit_name"
								class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700"
								placeholder="{{ __('e.g., Standard Delivery') }}"
							>
						</div>
						<div>
							<label for="edit_fee_per_unit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
								{{ __('Fee per Unit') }} <span class="text-red-500">*</span>
							</label>
							<input 
								type="number" 
								name="fee_per_unit" 
								id="edit_fee_per_unit"
								step="0.01"
								min="0"
								required
								class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700"
								placeholder="0.00"
							>
						</div>
						<div class="flex items-center">
							<input 
								type="checkbox" 
								name="active" 
								id="edit_active"
								value="1" 
								class="me-2"
							>
							<label for="edit_active" class="text-sm text-gray-600 dark:text-gray-300">{{ __('Active') }}</label>
						</div>
						<div class="flex justify-end gap-2 pt-2">
							<button 
								type="button" 
								onclick="closeEditModal()"
								class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-gray-500"
							>
								{{ __('Cancel') }}
							</button>
							<button 
								type="submit" 
								class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700"
							>
								{{ __('Update') }}
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<script>
			function editDeliveryFee(id, name, feePerUnit, active) {
				document.getElementById('editForm').action = '{{ route("admin.settings.delivery-fees.update", ":id") }}'.replace(':id', id);
				document.getElementById('edit_name').value = name || '';
				document.getElementById('edit_fee_per_unit').value = feePerUnit;
				document.getElementById('edit_active').checked = active;
				document.getElementById('editModal').classList.remove('hidden');
			}

			function closeEditModal() {
				document.getElementById('editModal').classList.add('hidden');
			}

			// Close modal when clicking outside
			document.getElementById('editModal').addEventListener('click', function(e) {
				if (e.target === this) {
					closeEditModal();
				}
			});
		</script>

		<!-- Quick add: Country -->
		<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
			<div class="flex items-center justify-between mb-4">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Add Country') }}</h3>
				<a href="{{ route('countries.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">{{ __('Manage Countries') }}</a>
			</div>

			<form method="POST" action="{{ route('countries.store') }}" class="space-y-4">
				@csrf
				<input type="hidden" name="redirect_to" value="settings">
				<div>
					<label class="block text-sm text-gray-600 dark:text-gray-300">{{ __('Name') }}</label>
					<input name="name" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700" required>
				</div>

				<div class="flex items-center">
					<input type="checkbox" name="active" value="1" checked class="me-2">
					<label class="text-sm text-gray-600 dark:text-gray-300">{{ __('Active') }}</label>
				</div>

				<div class="pt-2">
					<button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">{{ __('Add Country') }}</button>
				</div>
			</form>
		</div>

		<!-- Countries list -->
		<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
			<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Countries') }}</h3>

			@if(isset($countries) && $countries->count())
				<div class="overflow-x-auto">
					<table class="min-w-full text-sm">
						<thead>
							<tr class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
								<th class="py-2 pe-2">{{ __('Name') }}</th>
								<th class="py-2 pe-2">{{ __('Active') }}</th>
								<th class="py-2 pe-2 text-right">{{ __('Actions') }}</th>
							</tr>
						</thead>
						<tbody class="text-gray-900 dark:text-gray-100">
							@foreach($countries as $c)
								<tr class="border-b border-gray-100 dark:border-gray-700/60">
									<td class="py-2 pe-2">{{ $c->name }}</td>
									<td class="py-2 pe-2">
										<span class="px-2 py-1 rounded text-xs {{ $c->active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-200 text-gray-700' }}">{{ $c->active ? __('Yes') : __('No') }}</span>
									</td>
									<td class="py-2 pe-2">
										<form action="{{ route('countries.destroy', $c) }}" method="POST" class="inline float-right" onsubmit="return confirm('{{ __('Delete this country?') }}')">
											@csrf
											@method('DELETE')
											<input type="hidden" name="redirect_to" value="settings">
											<button type="submit" class="inline-flex items-center p-1.5 rounded hover:bg-red-50 dark:hover:bg-red-900/20 text-red-600 dark:text-red-400" title="{{ __('Delete') }}">
												<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
													<path fill-rule="evenodd" d="M9 3.75A2.25 2.25 0 0 1 11.25 1.5h1.5A2.25 2.25 0 0 1 15 3.75V4.5h3.75a.75.75 0 0 1 0 1.5h-.42l-1.07 13.367A2.25 2.25 0 0 1 15.02 22.5H8.98a2.25 2.25 0 0 1-2.241-2.133L5.67 6h-.42a.75.75 0 0 1 0-1.5H9V3.75Zm1.5.75h3V3.75a.75.75 0 0 0-.75-.75h-1.5a.75.75 0 0 0-.75.75V4.5ZM7.173 6l1.06 13.25a.75.75 0 0 0 .747.7h6.048a.75.75 0 0 0 .747-.7L16.827 6H7.173ZM9.75 8.25a.75.75 0 0 1 .75.75v8.25a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm4.5 0A.75.75 0 0 1 15 9v8.25a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Z" clip-rule="evenodd" />
												</svg>
												<span class="sr-only">{{ __('Delete') }}</span>
											</button>
										</form>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
				<div class="mt-4">{{ $countries->links() }}</div>
			@else
				<p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No countries yet.') }}</p>
			@endif
		</div>
	</div>
</x-admin-layout>

