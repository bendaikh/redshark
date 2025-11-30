<x-admin-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Users Management') }}
		</h2>
	</x-slot>

	<div class="py-4 sm:py-6">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
			<!-- Header with Add button -->
			<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 hidden sm:block">{{ __('Users') }}</h3>
				<a href="{{ route('users.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 active:bg-indigo-800 transition-colors touch-manipulation w-full sm:w-auto">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
					{{ __('Add User') }}
				</a>
			</div>

			@if (session('status'))
				<div class="px-4 py-3 bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 rounded-xl flex items-center gap-3">
					<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
					{{ session('status') }}
				</div>
			@endif

			@if (session('error'))
				<div class="px-4 py-3 bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 rounded-xl flex items-center gap-3">
					<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
					{{ session('error') }}
				</div>
			@endif

			<!-- Search and Filter Form -->
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl p-4">
				<form method="GET" action="{{ route('users.index') }}" class="space-y-3 sm:space-y-0 sm:flex sm:flex-wrap sm:items-end sm:gap-3">
					<div class="flex-1 min-w-0 sm:min-w-[200px] sm:max-w-sm">
						<label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1.5">{{ __('Search') }}</label>
						<input 
							type="text" 
							name="q" 
							value="{{ request('q') }}" 
							placeholder="{{ __('Search by name or email...') }}" 
							class="w-full py-2.5 px-3 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
						>
					</div>
					<div class="sm:w-48">
						<label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1.5">{{ __('Role') }}</label>
						<select 
							name="role" 
							class="w-full py-2.5 px-3 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
						>
							<option value="">{{ __('All Roles') }}</option>
							<option value="superadmin" {{ request('role') == 'superadmin' ? 'selected' : '' }}>{{ __('Super Admin') }}</option>
							<option value="media_buyer" {{ request('role') == 'media_buyer' ? 'selected' : '' }}>{{ __('Media Buyer') }}</option>
							<option value="country_admin" {{ request('role') == 'country_admin' ? 'selected' : '' }}>{{ __('Country Admin') }}</option>
							<option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>{{ __('User') }}</option>
						</select>
					</div>
					<div class="flex gap-2 sm:flex-shrink-0">
						<button type="submit" class="flex-1 sm:flex-none px-6 py-2.5 bg-gray-800 dark:bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-900 dark:hover:bg-gray-500 active:bg-gray-950 transition-colors touch-manipulation">
							{{ __('Search') }}
						</button>
						@if(request('q') || request('role'))
							<a href="{{ route('users.index') }}" class="flex-1 sm:flex-none px-4 py-2.5 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors touch-manipulation text-center">
								{{ __('Clear') }}
							</a>
						@endif
					</div>
				</form>
			</div>

			<!-- Table -->
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl">
				<div class="overflow-x-auto">
					<table class="min-w-full text-sm">
						<thead>
							<tr class="text-xs uppercase tracking-wider bg-gray-50 dark:bg-gray-900 text-gray-500 dark:text-gray-300">
								<th class="py-3 px-3 text-left whitespace-nowrap">{{ __('Name') }}</th>
								<th class="py-3 px-3 text-left whitespace-nowrap hidden sm:table-cell">{{ __('Email') }}</th>
								<th class="py-3 px-3 text-left whitespace-nowrap">{{ __('Role') }}</th>
								<th class="py-3 px-3 text-left whitespace-nowrap hidden md:table-cell">{{ __('Created') }}</th>
								<th class="py-3 px-3 text-center whitespace-nowrap">{{ __('Actions') }}</th>
							</tr>
						</thead>
						<tbody class="text-gray-700 dark:text-gray-200 divide-y divide-gray-100 dark:divide-gray-700">
							@forelse($users as $user)
								<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
									<td class="py-3 px-3">
										<div class="flex items-center gap-3">
											<div class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center">
												<span class="text-indigo-600 dark:text-indigo-300 font-semibold text-sm">
													{{ strtoupper(substr($user->name, 0, 1)) }}
												</span>
											</div>
											<div class="min-w-0">
												<div class="font-medium text-gray-900 dark:text-gray-100 truncate">{{ $user->name }}</div>
												<div class="text-xs text-gray-500 dark:text-gray-400 sm:hidden truncate">{{ $user->email }}</div>
											</div>
										</div>
									</td>
									<td class="py-3 px-3 hidden sm:table-cell">
										<span class="text-gray-600 dark:text-gray-400">{{ $user->email }}</span>
									</td>
									<td class="py-3 px-3">
										@foreach($user->roles as $role)
											<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
												@if($role->name === 'superadmin') bg-purple-100 text-purple-700 dark:bg-purple-900/50 dark:text-purple-300
												@elseif($role->name === 'media_buyer') bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300
												@elseif($role->name === 'country_admin') bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300
												@else bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300
												@endif">
												{{ ucwords(str_replace('_', ' ', $role->name)) }}
											</span>
										@endforeach
									</td>
									<td class="py-3 px-3 hidden md:table-cell">
										<span class="text-gray-500 dark:text-gray-400 text-sm">{{ $user->created_at->format('M d, Y') }}</span>
									</td>
									<td class="py-3 px-3">
										<div class="flex items-center justify-center gap-1">
											<a href="{{ route('users.edit', $user) }}" class="p-2 text-indigo-600 hover:text-indigo-800 hover:bg-indigo-100 dark:hover:bg-indigo-900/30 rounded-lg transition-colors touch-manipulation" title="{{ __('Edit') }}">
												<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
												</svg>
											</a>
											@if($user->id !== auth()->id())
												<form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('{{ __('Are you sure you want to delete this user?') }}');" class="inline">
													@csrf
													@method('DELETE')
													<button type="submit" class="p-2 text-red-600 hover:text-red-800 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg transition-colors touch-manipulation" title="{{ __('Delete') }}">
														<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
														</svg>
													</button>
												</form>
											@endif
										</div>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="5" class="px-6 py-12 text-center">
										<div class="text-gray-400 dark:text-gray-500">
											<svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
											</svg>
											<p class="text-gray-500 dark:text-gray-400">{{ __('No users found.') }}</p>
										</div>
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>

				@if($users->hasPages())
					<div class="px-4 py-4 border-t border-gray-100 dark:border-gray-700">
						{{ $users->links() }}
					</div>
				@endif
			</div>
		</div>
	</div>
</x-admin-layout>
