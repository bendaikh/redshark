<x-admin-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Category') }} - {{ $category->name }}
		</h2>
	</x-slot>
	<div class="py-6">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
				<div class="mb-4">
					<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Category Name') }}</h3>
					<p class="text-gray-600 dark:text-gray-300">{{ $category->name }}</p>
				</div>
				<div class="pt-4">
					<a href="{{ route('categories.edit', $category) }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">{{ __('Edit') }}</a>
					<a href="{{ route('categories.index') }}" class="ms-2 text-gray-600 hover:underline">{{ __('Back to Categories') }}</a>
				</div>
			</div>
		</div>
	</div>
</x-admin-layout>

