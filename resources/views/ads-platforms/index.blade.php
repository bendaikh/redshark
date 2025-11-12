<x-admin-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Ads Platforms') }}
		</h2>
	</x-slot>
	<div class="py-6">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
			<div class="flex items-center justify-between">
				<div class="text-gray-500">{{ __('Total Platforms') }}: <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $platforms->total() }}</span></div>
				<a href="{{ route('ads-platforms.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">{{ __('Add Platform') }}</a>
			</div>
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4">
				<div class="overflow-x-auto">
					<table class="min-w-full text-sm">
						<thead>
							<tr class="text-left text-gray-500">
								<th class="py-2">{{ __('Name') }}</th>
								<th class="py-2">{{ __('Description') }}</th>
								<th class="py-2">{{ __('Status') }}</th>
								<th class="py-2"></th>
							</tr>
						</thead>
						<tbody class="text-gray-900 dark:text-gray-100">
							@foreach($platforms as $platform)
								<tr class="border-t border-gray-200 dark:border-gray-700">
									<td class="py-2">{{ $platform->name }}</td>
									<td class="py-2">{{ $platform->description ?? '-' }}</td>
									<td class="py-2">
										@if($platform->is_active)
											<span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">{{ __('Active') }}</span>
										@else
											<span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">{{ __('Inactive') }}</span>
										@endif
									</td>
									<td class="py-2 text-right space-x-2">
										<a href="{{ route('ads-platforms.edit', $platform) }}" class="text-indigo-600 hover:underline">{{ __('Edit') }}</a>
										<form action="{{ route('ads-platforms.destroy', $platform) }}" method="POST" class="inline">
											@csrf @method('DELETE')
											<button type="submit" class="text-red-600 hover:underline" onclick="return confirm('{{ __('Delete this platform?') }}')">{{ __('Delete') }}</button>
										</form>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
				<div class="mt-4">{{ $platforms->links() }}</div>
			</div>
		</div>
	</div>
</x-admin-layout>

