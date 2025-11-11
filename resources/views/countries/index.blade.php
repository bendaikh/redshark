<x-admin-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Countries') }}
		</h2>
	</x-slot>
	<div class="py-6">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
			<div class="mb-4">
				<a href="{{ route('countries.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">{{ __('Add Country') }}</a>
			</div>
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4">
				<div class="overflow-x-auto">
					<table class="min-w-full text-sm">
						<thead>
							<tr class="text-left text-gray-500">
								<th class="py-2">{{ __('Name') }}</th>
								<th class="py-2">{{ __('Code') }}</th>
								<th class="py-2">{{ __('Currency') }}</th>
								<th class="py-2">{{ __('Active') }}</th>
								<th class="py-2"></th>
							</tr>
						</thead>
						<tbody class="text-gray-900 dark:text-gray-100">
							@foreach($countries as $c)
								<tr class="border-t border-gray-200 dark:border-gray-700">
									<td class="py-2">{{ $c->name }}</td>
									<td class="py-2">{{ $c->code }}</td>
									<td class="py-2">{{ $c->currency }}</td>
									<td class="py-2">
										<span class="px-2 py-1 rounded text-xs {{ $c->active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-200 text-gray-700' }}">{{ $c->active ? __('Yes') : __('No') }}</span>
									</td>
									<td class="py-2 text-right space-x-2">
										<a href="{{ route('countries.edit', $c) }}" class="text-indigo-600 hover:underline">{{ __('Edit') }}</a>
										<form action="{{ route('countries.destroy', $c) }}" method="POST" class="inline">
											@csrf @method('DELETE')
											<button type="submit" class="text-red-600 hover:underline" onclick="return confirm('{{ __('Delete this country?') }}')">{{ __('Delete') }}</button>
										</form>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
				<div class="mt-4">{{ $countries->links() }}</div>
			</div>
		</div>
	</div>
</x-admin-layout>

