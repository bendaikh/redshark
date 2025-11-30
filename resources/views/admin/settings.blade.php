<x-admin-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-2xl text-gray-900 dark:text-gray-100 leading-tight">
			{{ __('Settings') }}
		</h2>
	</x-slot>

	<div class="py-4 sm:py-6">
		<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
			@if (session('status'))
				<div class="bg-green-100 dark:bg-green-900/50 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded-xl flex items-center gap-3" role="alert">
					<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
					<span>{{ session('status') }}</span>
				</div>
			@endif

			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl p-4 sm:p-6">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ __('Application Settings') }}</h3>
				<p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Configure your application settings here.') }}</p>
			</div>

			<!-- Delivery Fees Settings -->
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl p-4 sm:p-6">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ __('Delivery Fees Settings') }}</h3>
				<p class="text-sm text-gray-500 dark:text-gray-400 mb-6">{{ __('Manage delivery fees (per unit) that will be used as defaults in various sections of the application.') }}</p>
				
				<!-- Add New Delivery Fee Form -->
				<div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
					<h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Add New Delivery Fee') }}</h4>
					<form method="POST" action="{{ route('admin.settings.delivery-fees.store') }}" class="space-y-4">
						@csrf
						<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
							<div>
								<label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
									{{ __('Name') }} <span class="text-gray-400 text-xs">({{ __('Optional') }})</span>
								</label>
								<input 
									type="text" 
									name="name" 
									id="name"
									value="{{ old('name') }}"
									class="w-full py-2.5 px-3 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
									placeholder="{{ __('e.g., Standard Delivery') }}"
								>
								@error('name')
									<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
								@enderror
							</div>

							<div>
								<label for="fee_per_unit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
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
									class="w-full py-2.5 px-3 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
									placeholder="0.00"
								>
								@error('fee_per_unit')
									<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
								@enderror
							</div>

							<div class="flex items-end">
								<label class="flex items-center gap-2 py-2.5 cursor-pointer touch-manipulation">
									<input type="checkbox" name="active" id="active" value="1" checked class="w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
									<span class="text-sm text-gray-600 dark:text-gray-300">{{ __('Active') }}</span>
								</label>
							</div>
						</div>

						<div class="pt-2">
							<button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 active:bg-indigo-800 transition-colors touch-manipulation">{{ __('Add Delivery Fee') }}</button>
						</div>
					</form>
				</div>

				<!-- Delivery Fees List -->
				<div>
					<h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Delivery Fees List') }}</h4>
					
					@if(isset($deliveryFees) && $deliveryFees->count())
						<div class="overflow-x-auto -mx-4 sm:mx-0">
							<table class="min-w-full text-sm">
								<thead>
									<tr class="text-xs uppercase tracking-wider bg-gray-50 dark:bg-gray-900 text-gray-500 dark:text-gray-300">
										<th class="py-3 px-3 text-left">{{ __('Name') }}</th>
										<th class="py-3 px-3 text-right">{{ __('Fee per Unit') }}</th>
										<th class="py-3 px-3 text-center">{{ __('Active') }}</th>
										<th class="py-3 px-3 text-center">{{ __('Actions') }}</th>
									</tr>
								</thead>
								<tbody class="text-gray-700 dark:text-gray-200 divide-y divide-gray-100 dark:divide-gray-700">
									@foreach($deliveryFees as $fee)
										<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
											<td class="py-3 px-3">{{ $fee->name ?: __('Unnamed') }}</td>
											<td class="py-3 px-3 text-right tabular-nums font-medium">{{ number_format($fee->fee_per_unit, 2) }}</td>
											<td class="py-3 px-3 text-center">
												<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $fee->active ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
													{{ $fee->active ? __('Yes') : __('No') }}
												</span>
											</td>
											<td class="py-3 px-3">
												<div class="flex items-center justify-center gap-1">
													<button 
														type="button"
														onclick="editDeliveryFee({{ $fee->id }}, '{{ addslashes($fee->name) }}', {{ $fee->fee_per_unit }}, {{ $fee->active ? 'true' : 'false' }})"
														class="p-2 text-indigo-600 hover:text-indigo-800 hover:bg-indigo-100 dark:hover:bg-indigo-900/30 rounded-lg transition-colors touch-manipulation" 
														title="{{ __('Edit') }}"
													>
														<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
														</svg>
													</button>
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
															class="p-2 text-red-600 hover:text-red-800 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg transition-colors touch-manipulation" 
															title="{{ __('Delete') }}"
														>
															<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
																<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
															</svg>
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
						<div class="text-center py-8">
							<svg class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
							</svg>
							<p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No delivery fees yet. Add one using the form above.') }}</p>
						</div>
					@endif
				</div>
			</div>

			<!-- Edit Delivery Fee Modal -->
			<div id="editModal" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm overflow-y-auto h-full w-full z-50">
				<div class="flex items-end sm:items-center justify-center min-h-screen p-0 sm:p-4">
					<div class="relative w-full sm:max-w-md bg-white dark:bg-gray-800 rounded-t-2xl sm:rounded-2xl shadow-xl">
						<div class="p-4 sm:p-6">
							<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Edit Delivery Fee') }}</h3>
							<form id="editForm" method="POST" class="space-y-4">
								@csrf
								@method('PUT')
								<div>
									<label for="edit_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
										{{ __('Name') }} <span class="text-gray-400 text-xs">({{ __('Optional') }})</span>
									</label>
									<input 
										type="text" 
										name="name" 
										id="edit_name"
										class="w-full py-2.5 px-3 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
										placeholder="{{ __('e.g., Standard Delivery') }}"
									>
								</div>
								<div>
									<label for="edit_fee_per_unit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
										{{ __('Fee per Unit') }} <span class="text-red-500">*</span>
									</label>
									<input 
										type="number" 
										name="fee_per_unit" 
										id="edit_fee_per_unit"
										step="0.01"
										min="0"
										required
										class="w-full py-2.5 px-3 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
										placeholder="0.00"
									>
								</div>
								<div>
									<label class="flex items-center gap-2 cursor-pointer touch-manipulation">
										<input type="checkbox" name="active" id="edit_active" value="1" class="w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
										<span class="text-sm text-gray-600 dark:text-gray-300">{{ __('Active') }}</span>
									</label>
								</div>
								<div class="flex flex-col-reverse sm:flex-row gap-3 pt-4">
									<button 
										type="button" 
										onclick="closeEditModal()"
										class="flex-1 px-4 py-2.5 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-medium rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors touch-manipulation"
									>
										{{ __('Cancel') }}
									</button>
									<button 
										type="submit" 
										class="flex-1 px-4 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors touch-manipulation"
									>
										{{ __('Update') }}
									</button>
								</div>
							</form>
						</div>
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
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl p-4 sm:p-6">
				<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
					<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Add Country') }}</h3>
					<a href="{{ route('countries.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">{{ __('Manage Countries') }}</a>
				</div>

				<form method="POST" action="{{ route('countries.store') }}" class="space-y-4">
					@csrf
					<input type="hidden" name="redirect_to" value="settings">
					<div>
						<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Name') }}</label>
						<input name="name" class="w-full py-2.5 px-3 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
					</div>

					<div>
						<label class="flex items-center gap-2 cursor-pointer touch-manipulation">
							<input type="checkbox" name="active" value="1" checked class="w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
							<span class="text-sm text-gray-600 dark:text-gray-300">{{ __('Active') }}</span>
						</label>
					</div>

					<div class="pt-2">
						<button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 active:bg-indigo-800 transition-colors touch-manipulation">{{ __('Add Country') }}</button>
					</div>
				</form>
			</div>

			<!-- Countries list -->
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl p-4 sm:p-6">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Countries') }}</h3>

				@if(isset($countries) && $countries->count())
					<div class="overflow-x-auto -mx-4 sm:mx-0">
						<table class="min-w-full text-sm">
							<thead>
								<tr class="text-xs uppercase tracking-wider bg-gray-50 dark:bg-gray-900 text-gray-500 dark:text-gray-300">
									<th class="py-3 px-3 text-left">{{ __('Name') }}</th>
									<th class="py-3 px-3 text-center">{{ __('Active') }}</th>
									<th class="py-3 px-3 text-center">{{ __('Actions') }}</th>
								</tr>
							</thead>
							<tbody class="text-gray-700 dark:text-gray-200 divide-y divide-gray-100 dark:divide-gray-700">
								@foreach($countries as $c)
									<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
										<td class="py-3 px-3 font-medium">{{ $c->name }}</td>
										<td class="py-3 px-3 text-center">
											<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $c->active ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
												{{ $c->active ? __('Yes') : __('No') }}
											</span>
										</td>
										<td class="py-3 px-3">
											<div class="flex items-center justify-center">
												<form action="{{ route('countries.destroy', $c) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Delete this country?') }}')">
													@csrf
													@method('DELETE')
													<input type="hidden" name="redirect_to" value="settings">
													<button type="submit" class="p-2 text-red-600 hover:text-red-800 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg transition-colors touch-manipulation" title="{{ __('Delete') }}">
														<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
														</svg>
													</button>
												</form>
											</div>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
					@if($countries->hasPages())
						<div class="mt-4">{{ $countries->links() }}</div>
					@endif
				@else
					<div class="text-center py-8">
						<svg class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
						</svg>
						<p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No countries yet.') }}</p>
					</div>
				@endif
			</div>
		</div>
	</div>
</x-admin-layout>
