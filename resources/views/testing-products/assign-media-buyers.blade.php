<x-admin-layout>
	<x-slot name="header">
		<div class="flex items-center gap-4">
			<a href="{{ route('testing-products.index') }}" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100">
				<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
				</svg>
			</a>
			<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
				{{ __('Assign Media Buyers') }}
			</h2>
		</div>
	</x-slot>

	<div class="py-6">
		<div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
				<div class="p-6 border-b border-gray-200 dark:border-gray-700">
					<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
						{{ $testingProduct->product_name }}
					</h3>
					<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
						{{ __('Select media buyers who will have access to this testing product') }}
					</p>
				</div>

				<form method="POST" action="{{ route('testing-products.update-media-buyers', $testingProduct) }}" class="p-6 space-y-6">
					@csrf
					@method('PUT')

					<div>
						<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
							{{ __('Media Buyers') }}
						</label>
						
						@if($mediaBuyers->isEmpty())
							<div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 rounded-md p-4">
								<p class="text-sm text-yellow-800 dark:text-yellow-200">
									{{ __('No media buyers found. Please create media buyer users first.') }}
								</p>
							</div>
						@else
							<div class="space-y-2 max-h-96 overflow-y-auto border border-gray-200 dark:border-gray-700 rounded-md p-4">
								@foreach($mediaBuyers as $mediaBuyer)
									<label class="flex items-center gap-3 p-3 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition">
										<input 
											type="checkbox" 
											name="media_buyers[]" 
											value="{{ $mediaBuyer->id }}"
											{{ in_array($mediaBuyer->id, $assignedMediaBuyers) ? 'checked' : '' }}
											class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:bg-gray-900"
										>
										<div class="flex-1">
											<div class="flex items-center gap-2">
												<div class="h-8 w-8 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
													<span class="text-blue-600 dark:text-blue-300 font-semibold text-sm">
														{{ strtoupper(substr($mediaBuyer->name, 0, 1)) }}
													</span>
												</div>
												<div>
													<div class="text-sm font-medium text-gray-900 dark:text-gray-100">
														{{ $mediaBuyer->name }}
													</div>
													<div class="text-xs text-gray-500 dark:text-gray-400">
														{{ $mediaBuyer->email }}
													</div>
												</div>
											</div>
										</div>
									</label>
								@endforeach
							</div>
						@endif
						
						@error('media_buyers')
							<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
						@enderror
					</div>

					<!-- Actions -->
					<div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
						<a href="{{ route('testing-products.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition">
							{{ __('Cancel') }}
						</a>
						<button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
							{{ __('Save Assignments') }}
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</x-admin-layout>

