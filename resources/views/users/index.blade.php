<x-admin-layout>
	<x-slot name="header">
		<div class="flex items-center justify-between">
			<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
				{{ __('Users Management') }}
			</h2>
			<a href="{{ route('users.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
				{{ __('Add User') }}
			</a>
		</div>
	</x-slot>

	<div class="py-6">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
			@if (session('status'))
				<div class="mb-4 px-4 py-3 bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 rounded-md">
					{{ session('status') }}
				</div>
			@endif

			@if (session('error'))
				<div class="mb-4 px-4 py-3 bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 rounded-md">
					{{ session('error') }}
				</div>
			@endif

			<!-- Search and Filter Form -->
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 mb-4">
				<form method="GET" action="{{ route('users.index') }}" class="flex gap-2 flex-wrap">
					<input 
						type="text" 
						name="q" 
						value="{{ request('q') }}" 
						placeholder="Search by name or email..." 
						class="flex-1 min-w-[200px] rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600"
					>
					<select 
						name="role" 
						class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600"
					>
						<option value="">All Roles</option>
						<option value="superadmin" {{ request('role') == 'superadmin' ? 'selected' : '' }}>Super Admin</option>
						<option value="media_buyer" {{ request('role') == 'media_buyer' ? 'selected' : '' }}>Media Buyer</option>
						<option value="country_admin" {{ request('role') == 'country_admin' ? 'selected' : '' }}>Country Admin</option>
						<option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
					</select>
					<button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
						{{ __('Search') }}
					</button>
					@if(request('q') || request('role'))
						<a href="{{ route('users.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition">
							{{ __('Clear') }}
						</a>
					@endif
				</form>
			</div>

			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
				<div class="overflow-x-auto">
					<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
						<thead class="bg-gray-50 dark:bg-gray-900">
							<tr>
								<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
									{{ __('Name') }}
								</th>
								<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
									{{ __('Email') }}
								</th>
								<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
									{{ __('Role') }}
								</th>
								<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
									{{ __('Created') }}
								</th>
								<th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
									{{ __('Actions') }}
								</th>
							</tr>
						</thead>
						<tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
							@forelse($users as $user)
								<tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
									<td class="px-6 py-4 whitespace-nowrap">
										<div class="flex items-center">
											<div class="flex-shrink-0 h-10 w-10">
												<div class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
													<span class="text-indigo-600 dark:text-indigo-300 font-semibold">
														{{ strtoupper(substr($user->name, 0, 1)) }}
													</span>
												</div>
											</div>
											<div class="ml-4">
												<div class="text-sm font-medium text-gray-900 dark:text-gray-100">
													{{ $user->name }}
												</div>
											</div>
										</div>
									</td>
									<td class="px-6 py-4 whitespace-nowrap">
										<div class="text-sm text-gray-900 dark:text-gray-100">
											{{ $user->email }}
										</div>
									</td>
									<td class="px-6 py-4 whitespace-nowrap">
										@foreach($user->roles as $role)
											<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
												@if($role->name === 'superadmin') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
												@elseif($role->name === 'media_buyer') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
												@elseif($role->name === 'country_admin') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
												@else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200
												@endif">
												{{ ucwords(str_replace('_', ' ', $role->name)) }}
											</span>
										@endforeach
									</td>
									<td class="px-6 py-4 whitespace-nowrap">
										<div class="text-sm text-gray-500 dark:text-gray-400">
											{{ $user->created_at->format('M d, Y') }}
										</div>
									</td>
									<td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
										<div class="flex items-center justify-end gap-2">
											<a href="{{ route('users.edit', $user) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
												{{ __('Edit') }}
											</a>
											@if($user->id !== auth()->id())
												<form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Are you sure you want to delete this user?');" class="inline">
													@csrf
													@method('DELETE')
													<button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">
														{{ __('Delete') }}
													</button>
												</form>
											@endif
										</div>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
										{{ __('No users found.') }}
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>

				@if($users->hasPages())
					<div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
						{{ $users->links() }}
					</div>
				@endif
			</div>
		</div>
	</div>
</x-admin-layout>

