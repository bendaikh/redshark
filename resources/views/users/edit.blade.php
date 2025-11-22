<x-admin-layout>
	<x-slot name="header">
		<div class="flex items-center gap-4">
			<a href="{{ route('users.index') }}" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100">
				<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
				</svg>
			</a>
			<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
				{{ __('Edit User') }}
			</h2>
		</div>
	</x-slot>

	<div class="py-6">
		<div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
				<form method="POST" action="{{ route('users.update', $user) }}" class="p-6 space-y-6">
					@csrf
					@method('PUT')

					<!-- Name -->
					<div>
						<label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
							{{ __('Name') }} <span class="text-red-500">*</span>
						</label>
						<input 
							type="text" 
							name="name" 
							id="name" 
							value="{{ old('name', $user->name) }}" 
							required
							class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
						>
						@error('name')
							<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
						@enderror
					</div>

					<!-- Email -->
					<div>
						<label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
							{{ __('Email') }} <span class="text-red-500">*</span>
						</label>
						<input 
							type="email" 
							name="email" 
							id="email" 
							value="{{ old('email', $user->email) }}" 
							required
							class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
						>
						@error('email')
							<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
						@enderror
					</div>

					<!-- Role -->
					<div>
						<label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
							{{ __('Role') }} <span class="text-red-500">*</span>
						</label>
						<select 
							name="role" 
							id="role" 
							required
							class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
						>
							@foreach($roles as $role)
								<option value="{{ $role->name }}" {{ old('role', $user->roles->first()?->name) == $role->name ? 'selected' : '' }}>
									{{ ucwords(str_replace('_', ' ', $role->name)) }}
								</option>
							@endforeach
						</select>
						@error('role')
							<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
						@enderror
					</div>

					<!-- Password (Optional) -->
					<div>
						<label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
							{{ __('New Password') }}
							<span class="text-gray-500 text-xs">({{ __('Leave blank to keep current password') }})</span>
						</label>
						<input 
							type="password" 
							name="password" 
							id="password" 
							class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
						>
						@error('password')
							<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
						@enderror
					</div>

					<!-- Password Confirmation (Optional) -->
					<div>
						<label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
							{{ __('Confirm New Password') }}
						</label>
						<input 
							type="password" 
							name="password_confirmation" 
							id="password_confirmation" 
							class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
						>
					</div>

					<!-- Actions -->
					<div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
						<a href="{{ route('users.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition">
							{{ __('Cancel') }}
						</a>
						<button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
							{{ __('Update User') }}
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</x-admin-layout>

