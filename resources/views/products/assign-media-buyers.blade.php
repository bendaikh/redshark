<x-admin-layout>
	<x-slot name="header">
		<div class="flex items-center gap-4">
			<a href="{{ route('products.index') }}" class="p-2 -ml-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors touch-manipulation">
				<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
				</svg>
			</a>
			<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
				{{ __('Assign Media Buyers') }}
			</h2>
		</div>
	</x-slot>

	<div class="py-4 sm:py-6">
		<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl overflow-hidden">
				<div class="p-4 sm:p-6 border-b border-gray-200 dark:border-gray-700">
					<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
						{{ $product->name }}
					</h3>
					<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
						{{ __('Select media buyers who will have access to this product') }}
					</p>
				</div>

				<form method="POST" action="{{ route('products.update-media-buyers', $product) }}" class="p-4 sm:p-6 space-y-6">
					@csrf
					@method('PUT')

					<div>
						<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
							{{ __('Media Buyers') }}
						</label>
						
						@if($mediaBuyers->isEmpty())
							<div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 rounded-xl p-4 flex items-start gap-3">
								<svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
								</svg>
								<p class="text-sm text-yellow-800 dark:text-yellow-200">
									{{ __('No media buyers found. Please create media buyer users first.') }}
								</p>
							</div>
						@else
							<div class="space-y-3 max-h-96 overflow-y-auto border border-gray-200 dark:border-gray-700 rounded-xl p-3 sm:p-4">
								@foreach($mediaBuyers as $mediaBuyer)
									<div class="p-3 sm:p-4 rounded-xl border border-gray-200 dark:border-gray-600 {{ in_array($mediaBuyer->id, $assignedMediaBuyers) ? 'bg-indigo-50 dark:bg-indigo-900/20 border-indigo-200 dark:border-indigo-700' : 'bg-gray-50 dark:bg-gray-700/50' }} transition-colors">
										<label class="flex items-start gap-3 cursor-pointer touch-manipulation" for="checkbox_{{ $mediaBuyer->id }}">
											<input 
												type="checkbox" 
												name="media_buyers[]" 
												value="{{ $mediaBuyer->id }}"
												{{ in_array($mediaBuyer->id, $assignedMediaBuyers) ? 'checked' : '' }}
												class="mt-1 w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:bg-gray-800"
												onchange="toggleCostInput({{ $mediaBuyer->id }})"
												id="checkbox_{{ $mediaBuyer->id }}"
											>
											<div class="flex-1 min-w-0">
												<div class="flex items-center gap-3 mb-2">
													<div class="h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center flex-shrink-0">
														<span class="text-blue-600 dark:text-blue-300 font-semibold">
															{{ strtoupper(substr($mediaBuyer->name, 0, 1)) }}
														</span>
													</div>
													<div class="min-w-0">
														<div class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
															{{ $mediaBuyer->name }}
														</div>
														<div class="text-xs text-gray-500 dark:text-gray-400 truncate">
															{{ $mediaBuyer->email }}
														</div>
													</div>
												</div>
											</div>
										</label>
										<div id="cost_input_{{ $mediaBuyer->id }}" class="mt-3 ml-8 {{ in_array($mediaBuyer->id, $assignedMediaBuyers) ? '' : 'hidden' }}">
											<label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
												{{ __('Cost Total for Media Buyer') }}
											</label>
											<input 
												type="number" 
												name="cost_totals[{{ $mediaBuyer->id }}]" 
												value="{{ $mediaBuyerData[$mediaBuyer->id]['cost_total'] ?? '' }}"
												step="0.01"
												min="0"
												placeholder="0.00"
												class="w-full py-2.5 px-3 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
											>
										</div>
									</div>
								@endforeach
							</div>
							
							<script>
								function toggleCostInput(userId) {
									const checkbox = document.getElementById('checkbox_' + userId);
									const costInput = document.getElementById('cost_input_' + userId);
									
									if (checkbox.checked) {
										costInput.classList.remove('hidden');
									} else {
										costInput.classList.add('hidden');
									}
								}
							</script>
						@endif
						
						@error('media_buyers')
							<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
						@enderror
					</div>

					<!-- Actions -->
					<div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
						<a href="{{ route('products.index') }}" class="w-full sm:w-auto px-6 py-2.5 text-center text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors touch-manipulation">
							{{ __('Cancel') }}
						</a>
						<button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 active:bg-indigo-800 transition-colors touch-manipulation">
							{{ __('Save Assignments') }}
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</x-admin-layout>
