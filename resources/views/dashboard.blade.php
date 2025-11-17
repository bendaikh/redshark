<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Accounting Data Section -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Accounting Data') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Total Balance') }}</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">
                                        {{ number_format($totalBalance, 2) }}
                                    </p>
                                </div>
                                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                                    <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Total Expenses') }}</p>
                                    <p class="text-2xl font-bold text-red-600 dark:text-red-400 mt-2">
                                        {{ number_format($totalExpenses, 2) }}
                                    </p>
                                </div>
                                <div class="p-3 bg-red-100 dark:bg-red-900 rounded-full">
                                    <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Net Profit Balance') }}</p>
                                    <p class="text-2xl font-bold {{ $netProfitBalance >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} mt-2">
                                        {{ number_format($netProfitBalance, 2) }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ __('Total Balance - All Expenses') }}
                                    </p>
                                </div>
                                <div class="p-3 {{ $netProfitBalance >= 0 ? 'bg-green-100 dark:bg-green-900' : 'bg-red-100 dark:bg-red-900' }} rounded-full">
                                    <svg class="w-8 h-8 {{ $netProfitBalance >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Marketing Performance Section -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Marketing Performance') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Total Leads') }}</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">
                                {{ number_format($totalLeads ?? 0) }}
                            </p>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Total Ads Spend') }}</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">
                                {{ number_format($totalAdsSpend ?? 0, 2) }}
                            </p>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Cost Per Lead') }}</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">
                                {{ $costPerLead !== null ? number_format($costPerLead, 2) : __('N/A') }}
                            </p>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Total Orders') }}</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">
                                {{ number_format($totalOrders ?? 0) }}
                            </p>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Cost Per Delivered Order') }}</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">
                                {{ $costPerDelivered !== null ? number_format($costPerDelivered, 2) : __('N/A') }}
                            </p>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Delivery Rate') }}</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">
                                {{ $deliveryRate !== null ? number_format($deliveryRate, 2) . '%' : __('N/A') }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Total Orders / Total Leads') }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ __('Total Ads Spend by Platform') }}</h4>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            @if(($totalSpendByPlatform ?? collect())->isEmpty())
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No ads spend data available yet.') }}</p>
                            @else
                                <div class="space-y-3">
                                    @foreach($totalSpendByPlatform as $platformStat)
                                        <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-2 last:border-0 last:pb-0">
                                            <div>
                                                <p class="text-sm font-medium text-gray-700 dark:text-gray-200">
                                                    {{ $platformStat->platform ?? __('Unknown Platform') }}
                                                </p>
                                            </div>
                                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                {{ number_format($platformStat->total_spend ?? 0, 2) }}
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
