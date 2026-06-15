<x-admin-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-2xl text-gray-900 dark:text-gray-100 leading-tight">
			{{ __('Global Dashboard') }}
		</h2>
	</x-slot>

	<!-- Load ApexCharts -->
	<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

	<div class="space-y-6">
		<!-- Date Filters - Facebook Ads Style -->
		<div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl p-4" x-data="dateRangePicker()">
			<form method="GET" class="flex flex-wrap items-center gap-3">
				<!-- Date Range Dropdown Button -->
				<div class="relative">
					<button type="button" @click="showDropdown = !showDropdown" class="flex items-center gap-2 px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
						<svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
						</svg>
						<span x-text="selectedLabel">{{ $from && $to ? \Carbon\Carbon::parse($from)->format('M d, Y') . ' - ' . \Carbon\Carbon::parse($to)->format('M d, Y') : __('Select Date Range') }}</span>
						<svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
						</svg>
					</button>

					<!-- Dropdown Panel -->
					<div x-show="showDropdown" @click.outside="showDropdown = false" x-transition class="absolute top-full left-0 mt-2 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 z-50 flex">
						<!-- Preset Options -->
						<div class="w-48 border-r border-gray-200 dark:border-gray-700 py-2">
							<div class="px-3 py-1.5 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Recently used') }}</div>
							<button type="button" @click="selectPreset('today')" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" :class="{'bg-indigo-50 dark:bg-indigo-900/30 !text-indigo-600 dark:!text-indigo-400': currentPreset === 'today'}">{{ __('Today') }}</button>
							<button type="button" @click="selectPreset('yesterday')" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" :class="{'bg-indigo-50 dark:bg-indigo-900/30 !text-indigo-600 dark:!text-indigo-400': currentPreset === 'yesterday'}">{{ __('Yesterday') }}</button>
							<button type="button" @click="selectPreset('this_month')" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" :class="{'bg-indigo-50 dark:bg-indigo-900/30 !text-indigo-600 dark:!text-indigo-400': currentPreset === 'this_month'}">{{ __('This month') }}</button>
							<div class="my-2 border-t border-gray-200 dark:border-gray-700"></div>
							<button type="button" @click="selectPreset('last_7_days')" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" :class="{'bg-indigo-50 dark:bg-indigo-900/30 !text-indigo-600 dark:!text-indigo-400': currentPreset === 'last_7_days'}">{{ __('Last 7 days') }}</button>
							<button type="button" @click="selectPreset('last_14_days')" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" :class="{'bg-indigo-50 dark:bg-indigo-900/30 !text-indigo-600 dark:!text-indigo-400': currentPreset === 'last_14_days'}">{{ __('Last 14 days') }}</button>
							<button type="button" @click="selectPreset('last_28_days')" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" :class="{'bg-indigo-50 dark:bg-indigo-900/30 !text-indigo-600 dark:!text-indigo-400': currentPreset === 'last_28_days'}">{{ __('Last 28 days') }}</button>
							<button type="button" @click="selectPreset('last_30_days')" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" :class="{'bg-indigo-50 dark:bg-indigo-900/30 !text-indigo-600 dark:!text-indigo-400': currentPreset === 'last_30_days'}">{{ __('Last 30 days') }}</button>
							<div class="my-2 border-t border-gray-200 dark:border-gray-700"></div>
							<button type="button" @click="selectPreset('this_week')" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" :class="{'bg-indigo-50 dark:bg-indigo-900/30 !text-indigo-600 dark:!text-indigo-400': currentPreset === 'this_week'}">{{ __('This week') }}</button>
							<button type="button" @click="selectPreset('last_week')" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" :class="{'bg-indigo-50 dark:bg-indigo-900/30 !text-indigo-600 dark:!text-indigo-400': currentPreset === 'last_week'}">{{ __('Last week') }}</button>
							<button type="button" @click="selectPreset('last_month')" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" :class="{'bg-indigo-50 dark:bg-indigo-900/30 !text-indigo-600 dark:!text-indigo-400': currentPreset === 'last_month'}">{{ __('Last month') }}</button>
						</div>

						<!-- Calendar and Custom Date Inputs -->
						<div class="p-4 bg-white dark:bg-gray-800">
							<div class="flex items-center gap-4 mb-4">
								<div>
									<label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('From') }}</label>
									<input type="date" x-model="fromDate" class="py-2 px-3 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:[color-scheme:dark]">
								</div>
								<div class="text-gray-400 dark:text-gray-500">—</div>
								<div>
									<label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('To') }}</label>
									<input type="date" x-model="toDate" class="py-2 px-3 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:[color-scheme:dark]">
								</div>
							</div>
							<p class="text-xs text-gray-500 dark:text-gray-400 mb-4">{{ __('Dates are shown in Etc/GMT+0') }}</p>
							<div class="flex justify-end gap-2">
								<button type="button" @click="showDropdown = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">{{ __('Cancel') }}</button>
								<button type="button" @click="applyDateRange()" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">{{ __('Update') }}</button>
							</div>
						</div>
					</div>
				</div>

				<!-- Hidden inputs for form submission -->
				<input type="hidden" name="from" :value="fromDate">
				<input type="hidden" name="to" :value="toDate">
				
				<!-- Apply button visible outside dropdown -->
				<button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 active:bg-indigo-800 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors touch-manipulation">
					{{ __('Apply Filters') }}
				</button>
				
				@if($from || $to)
					<a href="{{ route('admin.dashboard') }}" class="px-4 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors">
						{{ __('Clear') }}
					</a>
				@endif
			</form>
		</div>

		<script>
			function dateRangePicker() {
				return {
					showDropdown: false,
					fromDate: '{{ $from ?? '' }}',
					toDate: '{{ $to ?? '' }}',
					currentPreset: '',
					selectedLabel: '{{ $from && $to ? \Carbon\Carbon::parse($from)->format("M d, Y") . " - " . \Carbon\Carbon::parse($to)->format("M d, Y") : __("Select Date Range") }}',
					
					selectPreset(preset) {
						const today = new Date();
						let from, to;
						
						switch(preset) {
							case 'today':
								from = to = this.formatDate(today);
								this.selectedLabel = '{{ __("Today") }}';
								break;
							case 'yesterday':
								const yesterday = new Date(today);
								yesterday.setDate(yesterday.getDate() - 1);
								from = to = this.formatDate(yesterday);
								this.selectedLabel = '{{ __("Yesterday") }}';
								break;
							case 'this_month':
								from = this.formatDate(new Date(today.getFullYear(), today.getMonth(), 1));
								to = this.formatDate(today);
								this.selectedLabel = '{{ __("This month") }}';
								break;
							case 'last_7_days':
								to = this.formatDate(today);
								const last7 = new Date(today);
								last7.setDate(last7.getDate() - 6);
								from = this.formatDate(last7);
								this.selectedLabel = '{{ __("Last 7 days") }}';
								break;
							case 'last_14_days':
								to = this.formatDate(today);
								const last14 = new Date(today);
								last14.setDate(last14.getDate() - 13);
								from = this.formatDate(last14);
								this.selectedLabel = '{{ __("Last 14 days") }}';
								break;
							case 'last_28_days':
								to = this.formatDate(today);
								const last28 = new Date(today);
								last28.setDate(last28.getDate() - 27);
								from = this.formatDate(last28);
								this.selectedLabel = '{{ __("Last 28 days") }}';
								break;
							case 'last_30_days':
								to = this.formatDate(today);
								const last30 = new Date(today);
								last30.setDate(last30.getDate() - 29);
								from = this.formatDate(last30);
								this.selectedLabel = '{{ __("Last 30 days") }}';
								break;
							case 'this_week':
								const startOfWeek = new Date(today);
								startOfWeek.setDate(today.getDate() - today.getDay());
								from = this.formatDate(startOfWeek);
								to = this.formatDate(today);
								this.selectedLabel = '{{ __("This week") }}';
								break;
							case 'last_week':
								const lastWeekEnd = new Date(today);
								lastWeekEnd.setDate(today.getDate() - today.getDay() - 1);
								const lastWeekStart = new Date(lastWeekEnd);
								lastWeekStart.setDate(lastWeekEnd.getDate() - 6);
								from = this.formatDate(lastWeekStart);
								to = this.formatDate(lastWeekEnd);
								this.selectedLabel = '{{ __("Last week") }}';
								break;
							case 'last_month':
								const lastMonthEnd = new Date(today.getFullYear(), today.getMonth(), 0);
								const lastMonthStart = new Date(today.getFullYear(), today.getMonth() - 1, 1);
								from = this.formatDate(lastMonthStart);
								to = this.formatDate(lastMonthEnd);
								this.selectedLabel = '{{ __("Last month") }}';
								break;
						}
						
						this.fromDate = from;
						this.toDate = to;
						this.currentPreset = preset;
					},
					
					formatDate(date) {
						return date.toISOString().split('T')[0];
					},
					
				applyDateRange() {
					if (this.fromDate && this.toDate) {
						const fromFormatted = new Date(this.fromDate).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
						const toFormatted = new Date(this.toDate).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
						this.selectedLabel = fromFormatted + ' - ' + toFormatted;
					}
					this.showDropdown = false;
				}
			}
		}

		function marketingDatePicker() {
			return {
				showDropdown: false,
				fromDate: '{{ $marketingFrom ?? '' }}',
				toDate: '{{ $marketingTo ?? '' }}',
				currentPreset: '',
				selectedLabel: '{{ $marketingFrom && $marketingTo ? \Carbon\Carbon::parse($marketingFrom)->format("M d, Y") . " - " . \Carbon\Carbon::parse($marketingTo)->format("M d, Y") : __("Select Date Range") }}',
				
				selectPreset(preset) {
					const today = new Date();
					let from, to;
					
					switch(preset) {
						case 'today':
							from = to = this.formatDate(today);
							this.selectedLabel = '{{ __("Today") }}';
							break;
						case 'yesterday':
							const yesterday = new Date(today);
							yesterday.setDate(yesterday.getDate() - 1);
							from = to = this.formatDate(yesterday);
							this.selectedLabel = '{{ __("Yesterday") }}';
							break;
						case 'this_month':
							from = this.formatDate(new Date(today.getFullYear(), today.getMonth(), 1));
							to = this.formatDate(today);
							this.selectedLabel = '{{ __("This month") }}';
							break;
						case 'last_7_days':
							to = this.formatDate(today);
							const last7 = new Date(today);
							last7.setDate(last7.getDate() - 6);
							from = this.formatDate(last7);
							this.selectedLabel = '{{ __("Last 7 days") }}';
							break;
						case 'last_14_days':
							to = this.formatDate(today);
							const last14 = new Date(today);
							last14.setDate(last14.getDate() - 13);
							from = this.formatDate(last14);
							this.selectedLabel = '{{ __("Last 14 days") }}';
							break;
						case 'last_30_days':
							to = this.formatDate(today);
							const last30 = new Date(today);
							last30.setDate(last30.getDate() - 29);
							from = this.formatDate(last30);
							this.selectedLabel = '{{ __("Last 30 days") }}';
							break;
						case 'this_week':
							const startOfWeek = new Date(today);
							startOfWeek.setDate(today.getDate() - today.getDay());
							from = this.formatDate(startOfWeek);
							to = this.formatDate(today);
							this.selectedLabel = '{{ __("This week") }}';
							break;
						case 'last_week':
							const lastWeekEnd = new Date(today);
							lastWeekEnd.setDate(today.getDate() - today.getDay() - 1);
							const lastWeekStart = new Date(lastWeekEnd);
							lastWeekStart.setDate(lastWeekEnd.getDate() - 6);
							from = this.formatDate(lastWeekStart);
							to = this.formatDate(lastWeekEnd);
							this.selectedLabel = '{{ __("Last week") }}';
							break;
						case 'last_month':
							const lastMonthEnd = new Date(today.getFullYear(), today.getMonth(), 0);
							const lastMonthStart = new Date(today.getFullYear(), today.getMonth() - 1, 1);
							from = this.formatDate(lastMonthStart);
							to = this.formatDate(lastMonthEnd);
							this.selectedLabel = '{{ __("Last month") }}';
							break;
					}
					
					this.fromDate = from;
					this.toDate = to;
					this.currentPreset = preset;
				},
				
				formatDate(date) {
					return date.toISOString().split('T')[0];
				},
				
				applyDateRange() {
					if (this.fromDate && this.toDate) {
						const fromFormatted = new Date(this.fromDate).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
						const toFormatted = new Date(this.toDate).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
						this.selectedLabel = fromFormatted + ' - ' + toFormatted;
					}
					this.showDropdown = false;
				}
			}
		}
	</script>

		<!-- Revenue Trends Section -->
		<div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl overflow-hidden">
			<div class="p-4 sm:p-6">
				<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
					<div>
						<h3 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-gray-100">
							<span class="inline-flex items-center gap-2">
								<svg class="w-6 h-6 sm:w-7 sm:h-7 text-green-600 dark:text-green-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
								</svg>
								<span>{{ __('ACCOUNTING BALANCE') }}</span>
							</span>
						</h3>
						<p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
							{{ number_format($accountingOrders ?? 0) }} {{ __('ORDERS') }}
						</p>
						@if(!$from && !$to)
							<p class="text-xs text-orange-600 dark:text-orange-400 mt-1">
								{{ __('Showing last 30 days') }}
							</p>
						@endif
					</div>
					<div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-3 sm:p-4 text-center sm:text-right">
						<p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">{{ __('Total Balance') }}</p>
						<p class="text-xl sm:text-2xl font-bold text-green-600 dark:text-green-400">
							${{ number_format(array_sum($chartRevenues ?? []), 2) }}
						</p>
					</div>
				</div>
				<div class="text-sm text-gray-600 dark:text-gray-400 mb-3">
					{{ __('Balance trends by day') }}
				</div>
				<div id="revenueChart" class="w-full -mx-2 sm:mx-0" style="height: 220px; min-height: 180px;"></div>
			</div>
		</div>

		<!-- Accounting Data Section -->
		<div class="mb-6">
			<h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3 sm:mb-4">{{ __('Accounting Data') }}</h3>
			<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
				<div class="p-5 bg-white dark:bg-gray-800 rounded-lg shadow-sm border-l-4 border-green-500">
					<div class="flex items-center justify-between">
						<div>
							<div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Total Balance') }}</div>
							<div class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">
								${{ number_format($accountingBalance, 2) }}
							</div>
						</div>
						<div class="h-12 w-12 rounded-md bg-green-50 text-green-600 dark:bg-green-900/40 dark:text-green-300 flex items-center justify-center">
							<svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
								<path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
							</svg>
						</div>
					</div>
				</div>
				<div class="p-5 bg-white dark:bg-gray-800 rounded-lg shadow-sm border-l-4 border-red-500">
					<div class="flex items-center justify-between">
						<div>
							<div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Total Expenses') }}</div>
							<div class="mt-2 text-3xl font-bold text-red-600 dark:text-red-400">
								${{ number_format($accountingExpenses, 2) }}
							</div>
						</div>
						<div class="h-12 w-12 rounded-md bg-red-50 text-red-600 dark:bg-red-900/40 dark:text-red-300 flex items-center justify-center">
							<svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
								<path d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
							</svg>
						</div>
					</div>
				</div>
				<div class="p-5 bg-white dark:bg-gray-800 rounded-lg shadow-sm border-l-4 {{ $accountingNetProfit >= 0 ? 'border-green-500' : 'border-red-500' }}">
					<div class="flex items-center justify-between">
						<div>
							<div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Net Profit Balance') }}</div>
							<div class="mt-2 text-3xl font-bold {{ $accountingNetProfit >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
								${{ number_format($accountingNetProfit, 2) }}
							</div>
							<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
								{{ __('Total Balance - All Expenses') }}
							</p>
						</div>
						<div class="h-12 w-12 rounded-md {{ $accountingNetProfit >= 0 ? 'bg-green-50 text-green-600 dark:bg-green-900/40 dark:text-green-300' : 'bg-red-50 text-red-600 dark:bg-red-900/40 dark:text-red-300' }} flex items-center justify-center">
							<svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
								<path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
							</svg>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Marketing Performance Section -->
		<div class="mb-6 overflow-visible" x-data="marketingDatePicker()">
			<div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl p-4 mb-4 overflow-visible">
				<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 overflow-visible">
					<h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Marketing Performance') }}</h3>
					<form method="GET" class="flex flex-wrap items-center gap-3 overflow-visible">
						<!-- Preserve main date filters -->
						@if($from)
							<input type="hidden" name="from" value="{{ $from }}">
						@endif
						@if($to)
							<input type="hidden" name="to" value="{{ $to }}">
						@endif
						
						<!-- Marketing Date Range Dropdown Button -->
						<div class="relative overflow-visible">
							<button type="button" @click="showDropdown = !showDropdown" class="flex items-center gap-2 px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
								<svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
								</svg>
								<span x-text="selectedLabel">{{ $marketingFrom && $marketingTo ? \Carbon\Carbon::parse($marketingFrom)->format('M d, Y') . ' - ' . \Carbon\Carbon::parse($marketingTo)->format('M d, Y') : __('Select Date Range') }}</span>
								<svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
								</svg>
							</button>

							<!-- Dropdown Panel - always position from right edge so it expands leftward -->
							<div x-show="showDropdown" @click.outside="showDropdown = false" x-transition class="absolute top-full mt-2 right-0 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 z-50 flex flex-col sm:flex-row">
								<!-- Preset Options -->
								<div class="w-48 border-r border-gray-200 dark:border-gray-700 py-2">
									<div class="px-3 py-1.5 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Quick select') }}</div>
									<button type="button" @click="selectPreset('today')" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" :class="{'bg-indigo-50 dark:bg-indigo-900/30 !text-indigo-600 dark:!text-indigo-400': currentPreset === 'today'}">{{ __('Today') }}</button>
									<button type="button" @click="selectPreset('yesterday')" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" :class="{'bg-indigo-50 dark:bg-indigo-900/30 !text-indigo-600 dark:!text-indigo-400': currentPreset === 'yesterday'}">{{ __('Yesterday') }}</button>
									<button type="button" @click="selectPreset('this_month')" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" :class="{'bg-indigo-50 dark:bg-indigo-900/30 !text-indigo-600 dark:!text-indigo-400': currentPreset === 'this_month'}">{{ __('This month') }}</button>
									<div class="my-2 border-t border-gray-200 dark:border-gray-700"></div>
									<button type="button" @click="selectPreset('last_7_days')" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" :class="{'bg-indigo-50 dark:bg-indigo-900/30 !text-indigo-600 dark:!text-indigo-400': currentPreset === 'last_7_days'}">{{ __('Last 7 days') }}</button>
									<button type="button" @click="selectPreset('last_14_days')" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" :class="{'bg-indigo-50 dark:bg-indigo-900/30 !text-indigo-600 dark:!text-indigo-400': currentPreset === 'last_14_days'}">{{ __('Last 14 days') }}</button>
									<button type="button" @click="selectPreset('last_30_days')" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" :class="{'bg-indigo-50 dark:bg-indigo-900/30 !text-indigo-600 dark:!text-indigo-400': currentPreset === 'last_30_days'}">{{ __('Last 30 days') }}</button>
									<div class="my-2 border-t border-gray-200 dark:border-gray-700"></div>
									<button type="button" @click="selectPreset('this_week')" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" :class="{'bg-indigo-50 dark:bg-indigo-900/30 !text-indigo-600 dark:!text-indigo-400': currentPreset === 'this_week'}">{{ __('This week') }}</button>
									<button type="button" @click="selectPreset('last_week')" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" :class="{'bg-indigo-50 dark:bg-indigo-900/30 !text-indigo-600 dark:!text-indigo-400': currentPreset === 'last_week'}">{{ __('Last week') }}</button>
									<button type="button" @click="selectPreset('last_month')" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" :class="{'bg-indigo-50 dark:bg-indigo-900/30 !text-indigo-600 dark:!text-indigo-400': currentPreset === 'last_month'}">{{ __('Last month') }}</button>
								</div>

								<!-- Custom Date Inputs -->
								<div class="p-4 bg-white dark:bg-gray-800">
									<div class="flex items-center gap-4 mb-4">
										<div>
											<label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('From') }}</label>
											<input type="date" x-model="fromDate" class="py-2 px-3 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:[color-scheme:dark]">
										</div>
										<div class="text-gray-400 dark:text-gray-500">—</div>
										<div>
											<label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('To') }}</label>
											<input type="date" x-model="toDate" class="py-2 px-3 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:[color-scheme:dark]">
										</div>
									</div>
									<p class="text-xs text-gray-500 dark:text-gray-400 mb-4">{{ __('Dates are shown in Etc/GMT+0') }}</p>
									<div class="flex justify-end gap-2">
										<button type="button" @click="showDropdown = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">{{ __('Cancel') }}</button>
										<button type="button" @click="applyDateRange()" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">{{ __('Update') }}</button>
									</div>
								</div>
							</div>
						</div>

						<!-- Hidden inputs for form submission -->
						<input type="hidden" name="marketing_from" :value="fromDate">
						<input type="hidden" name="marketing_to" :value="toDate">
						
						<select name="product_id" class="py-2.5 px-3 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
							<option value="">{{ __('All Products') }}</option>
							@foreach($allProducts as $product)
								<option value="{{ $product->id }}" {{ $productId == $product->id ? 'selected' : '' }}>
									{{ $product->name }}
								</option>
							@endforeach
						</select>
						<button type="submit" class="px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
							{{ __('Filter') }}
						</button>
					</form>
				</div>
			</div>
			
			@if($selectedProduct)
				<div class="mb-3 p-3 bg-indigo-50 dark:bg-indigo-900/20 border-l-4 border-indigo-500 rounded">
					<p class="text-sm text-indigo-800 dark:text-indigo-300">
						<span class="font-semibold">{{ __('Filtered by Product:') }}</span> {{ $selectedProduct->name }}
						<a href="{{ route('admin.dashboard', array_merge(request()->only(['from', 'to']))) }}" class="ml-2 text-indigo-600 dark:text-indigo-400 hover:underline">
							{{ __('Clear Filter') }}
						</a>
					</p>
				</div>
			@endif
			
			<div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
				<div class="p-4 sm:p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border-l-4 border-indigo-500">
					<p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">{{ __('Total Leads') }}</p>
					<p class="mt-1 sm:mt-2 text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($totalLeads ?? 0) }}</p>
				</div>
				<div class="p-4 sm:p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border-l-4 border-red-500">
					<p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">{{ __('Total Ads Spend') }}</p>
					<p class="mt-1 sm:mt-2 text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-gray-100">${{ number_format($totalAdsSpent ?? 0, 2) }}</p>
				</div>
				<div class="p-4 sm:p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border-l-4 border-amber-500">
					<p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">{{ __('Cost Per Lead') }}</p>
					<p class="mt-1 sm:mt-2 text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-gray-100">
						{{ $costPerLead !== null ? '$' . number_format($costPerLead, 2) : __('N/A') }}
					</p>
				</div>
				<div class="p-4 sm:p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border-l-4 border-blue-500">
					<p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">{{ __('Total Orders') }}</p>
					<p class="mt-1 sm:mt-2 text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($totalOrders ?? 0) }}</p>
				</div>
				<div class="p-4 sm:p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border-l-4 border-green-500">
					<p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 truncate">{{ __('Cost Per Delivered') }}</p>
					<p class="mt-1 sm:mt-2 text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-gray-100">
						{{ $costPerDelivered !== null ? '$' . number_format($costPerDelivered, 2) : __('N/A') }}
					</p>
				</div>
				<div class="p-4 sm:p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border-l-4 border-purple-500">
					<p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">{{ __('Delivery Rate') }}</p>
					<p class="mt-1 sm:mt-2 text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-gray-100">
						{{ $deliveryRate !== null ? number_format($deliveryRate, 2) . '%' : __('N/A') }}
					</p>
				</div>
			</div>

			<!-- Business KPIs - Moved BEFORE Total Ads Spend by Platform -->
			<div class="mt-6 sm:mt-8">
				<h4 class="text-sm sm:text-base font-semibold text-gray-900 dark:text-gray-100 mb-3">{{ __('Business KPIs') }}</h4>
				<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
					<div class="p-5 bg-white dark:bg-gray-800 rounded-lg shadow-sm border-l-4 border-green-500">
						<div class="flex items-center justify-between">
							<div>
								<div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Total Profits') }}</div>
								<div class="mt-2 text-2xl sm:text-3xl font-bold text-gray-900 dark:text-gray-100">${{ number_format($totalProfits, 2) }}</div>
							</div>
							<div class="h-12 w-12 rounded-md bg-green-50 text-green-600 dark:bg-green-900/40 dark:text-green-300 flex items-center justify-center">
								<svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
							</div>
						</div>
					</div>
					<div class="p-5 bg-white dark:bg-gray-800 rounded-lg shadow-sm border-l-4 border-indigo-500">
						<div class="flex items-center justify-between">
							<div>
								<div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Initial Quantity') }}</div>
								<div class="mt-2 text-2xl sm:text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($initialQuantity ?? 0) }}</div>
							</div>
							<div class="h-12 w-12 rounded-md bg-indigo-50 text-indigo-600 dark:bg-indigo-900/40 dark:text-indigo-300 flex items-center justify-center">
								<svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 14H8v-2h4v2zm4-4H8v-2h8v2zm0-4H8V7h8v2z"/></svg>
							</div>
						</div>
					</div>
					<div class="p-5 bg-white dark:bg-gray-800 rounded-lg shadow-sm border-l-4 border-blue-500">
						<div class="flex items-center justify-between">
							<div>
								<div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Sold Quantity') }}</div>
								<div class="mt-2 text-2xl sm:text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($soldQuantity ?? 0) }}</div>
							</div>
							<div class="h-12 w-12 rounded-md bg-blue-50 text-blue-600 dark:bg-blue-900/40 dark:text-blue-300 flex items-center justify-center">
								<svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg>
							</div>
						</div>
					</div>
					<div class="p-5 bg-white dark:bg-gray-800 rounded-lg shadow-sm border-l-4 border-purple-500">
						<div class="flex items-center justify-between">
							<div>
								<div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Sold Rate') }}</div>
								<div class="mt-2 text-2xl sm:text-3xl font-bold {{ ($soldRate ?? 0) >= 50 ? 'text-green-600 dark:text-green-400' : 'text-amber-600 dark:text-amber-400' }}">{{ number_format($soldRate ?? 0, 1) }}%</div>
								<p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ __('Sold / Initial') }}</p>
							</div>
							<div class="h-12 w-12 rounded-md bg-purple-50 text-purple-600 dark:bg-purple-900/40 dark:text-purple-300 flex items-center justify-center">
								<svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 14c-3.31 0-6-2.69-6-6s2.69-6 6-6 6 2.69 6 6-2.69 6-6 6z"/><path d="M12 8v4l3 3"/></svg>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Total Ads Spend by Platform - Now AFTER Business KPIs -->
			<div class="mt-6">
				<h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ __('Total Ads Spend by Platform') }}</h4>
				<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
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
											${{ number_format($platformStat->total_spend ?? 0, 2) }}
										</p>
									</div>
								@endforeach
							</div>
						@endif
					</div>
				</div>
			</div>
		</div>

		<!-- Management Cards -->
		<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
			<!-- Stock Management -->
			<div class="p-4 sm:p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm">
				<h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">{{ __('Stock Management') }}</h3>
				<div class="space-y-2">
					<div class="flex justify-between">
						<span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Total Stock Value') }}</span>
						<span class="text-sm font-semibold text-gray-900 dark:text-gray-100">${{ number_format($totalStockValue, 2) }}</span>
					</div>
					<div class="flex justify-between">
						<span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Stock Recovery Sold') }}</span>
						<span class="text-sm font-semibold text-green-600 dark:text-green-400">${{ number_format($stockRecoverySold ?? 0, 2) }}</span>
					</div>
					<div class="flex justify-between">
						<span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Low Stock Items') }}</span>
						<span class="text-sm font-semibold text-red-600">{{ $lowStockProducts->count() }}</span>
					</div>
				</div>
			</div>

			<!-- Sourcing Management -->
			<div class="p-4 sm:p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm">
				<h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">{{ __('Sourcing Management') }}</h3>
				<div class="space-y-2">
					<div class="flex justify-between">
						<span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Total Sourcings') }}</span>
						<span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $totalSourcings }}</span>
					</div>
					<div class="flex justify-between">
						<span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Validated') }}</span>
						<span class="text-sm font-semibold text-green-600">{{ $validatedSourcings }}</span>
					</div>
					<div class="flex justify-between">
						<span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Pending') }}</span>
						<span class="text-sm font-semibold text-orange-600">{{ $pendingSourcings }}</span>
					</div>
				</div>
			</div>

			<!-- Ads Management -->
			<div class="p-4 sm:p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm sm:col-span-2 lg:col-span-1">
				<h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">{{ __('Ads Management') }}</h3>
				<div class="space-y-2">
					<div class="flex justify-between">
						<span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Total Campaigns') }}</span>
						<span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $totalAdsCampaigns }}</span>
					</div>
					<div class="flex justify-between">
						<span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Total Spent') }}</span>
						<span class="text-sm font-semibold text-gray-900 dark:text-gray-100">${{ number_format($totalAdsSpent, 2) }}</span>
					</div>
				</div>
			</div>
		</div>

		<!-- Business Overview Chart -->
		<div class="p-4 sm:p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
			<h3 class="text-lg sm:text-xl lg:text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ __('Business Overview') }}</h3>
			<p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mb-4 sm:mb-6">
				{{ __('Comprehensive analytics showing your business performance over time') }}
				@if(!$from || !$to)
					<span class="block sm:inline mt-1 sm:mt-0 text-xs text-orange-600 dark:text-orange-400">({{ __('Showing all-time data. Use date filters for specific periods.') }})</span>
				@endif
			</p>
			<div id="businessOverviewChart" class="-mx-2 sm:mx-0" style="min-height: 300px; height: 350px;">
				@if(empty($chartData) || (isset($chartData['profits']) && $chartData['profits']->isEmpty()))
					<div class="flex items-center justify-center h-96 text-gray-500 dark:text-gray-400">
						<div class="text-center">
							<svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
							</svg>
							<p class="mt-4">{{ __('No data available for the selected period.') }}</p>
							<p class="text-sm mt-2">{{ __('Add invoices and ads campaigns to see analytics.') }}</p>
						</div>
					</div>
				@endif
			</div>
		</div>

		<!-- Charts Section -->
		@if(!empty($chartData))
		<div class="grid grid-cols-1 lg:grid-cols-2 gap-3 sm:gap-4">
			<!-- Profits Over Time -->
			<div class="p-4 sm:p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
				<h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3 sm:mb-4">{{ __('Net Profit Over Time') }}</h3>
				<div id="profitsChart" class="-mx-2 sm:mx-0" style="min-height: 250px; height: 280px;"></div>
			</div>

			<!-- Ads Spending Over Time -->
			<div class="p-4 sm:p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
				<h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3 sm:mb-4">{{ __('Ads Spending Over Time') }}</h3>
				<div id="adsChart" class="-mx-2 sm:mx-0" style="min-height: 250px; height: 280px;"></div>
			</div>

			<!-- Invoices Over Time -->
			<div class="p-4 sm:p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
				<h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3 sm:mb-4">{{ __('Invoices Over Time') }}</h3>
				<div id="invoicesChart" class="-mx-2 sm:mx-0" style="min-height: 250px; height: 280px;"></div>
			</div>

			<!-- Revenue vs Ads Cost -->
			<div class="p-4 sm:p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
				<h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3 sm:mb-4">{{ __('Revenue vs Ads Cost') }}</h3>
				<div id="revenueVsAdsChart" class="-mx-2 sm:mx-0" style="min-height: 250px; height: 280px;"></div>
			</div>
		</div>
		@endif

		<!-- Profitable Products -->
		<div class="p-4 sm:p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm">
			<h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3 sm:mb-4">{{ __('Top Profitable Products') }}</h3>
			@if($profitableProducts->isEmpty())
				<p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No profitable products found.') }}</p>
			@else
				<div class="overflow-x-auto -mx-4 sm:mx-0 px-4 sm:px-0">
					<table class="min-w-full text-xs sm:text-sm">
						<thead>
							<tr class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
								<th class="py-3 pe-2">{{ __('Product') }}</th>
								<th class="py-3 pe-2">{{ __('Total Amount') }}</th>
								<th class="py-3 pe-2">{{ __('Ads Cost') }}</th>
								<th class="py-3 pe-2">{{ __('Net Profit') }}</th>
							</tr>
						</thead>
						<tbody class="text-gray-900 dark:text-gray-100">
							@foreach($profitableProducts as $product)
								<tr class="border-b border-gray-100 dark:border-gray-700/60">
									<td class="py-3 pe-2 font-medium">{{ $product->name }}</td>
									<td class="py-3 pe-2">${{ number_format($product->total_amount, 2) }}</td>
									<td class="py-3 pe-2">${{ number_format($product->total_ads_cost, 2) }}</td>
									<td class="py-3 pe-2 font-semibold text-green-600">${{ number_format($product->net_profit, 2) }}</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			@endif
		</div>

		<!-- Low Stock Alerts -->
		@if($lowStockProducts->isNotEmpty())
		<div class="p-4 sm:p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm">
			<h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3 sm:mb-4">{{ __('Low Stock Alerts') }}</h3>
			<div class="overflow-x-auto -mx-4 sm:mx-0 px-4 sm:px-0">
				<table class="min-w-full text-xs sm:text-sm">
					<thead>
						<tr class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
							<th class="py-3 pe-2">{{ __('Product') }}</th>
							<th class="py-3 pe-2">{{ __('Category') }}</th>
							<th class="py-3 pe-2">{{ __('Current Qty') }}</th>
							<th class="py-3 pe-2">{{ __('Threshold') }}</th>
						</tr>
					</thead>
					<tbody class="text-gray-900 dark:text-gray-100">
						@foreach($lowStockProducts as $product)
							<tr class="border-b border-gray-100 dark:border-gray-700/60">
								<td class="py-3 pe-2 font-medium">{{ $product->name }}</td>
								<td class="py-3 pe-2">{{ $product->category?->name ?? __('None') }}</td>
								<td class="py-3 pe-2 text-red-600 font-semibold">{{ $product->quantity }}</td>
								<td class="py-3 pe-2">{{ $product->low_stock_threshold }}</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
		@endif

		<!-- By Country Overview -->
		<div class="p-4 sm:p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm">
			<h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">{{ __('By Country Overview') }}</h3>
			<p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mb-3 sm:mb-4">{{ __('Breakdown of key metrics by country.') }}</p>

			<div class="overflow-x-auto -mx-4 sm:mx-0 px-4 sm:px-0">
				<table class="min-w-full text-xs sm:text-sm">
					<thead>
						<tr class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
							<th class="py-3 pe-2">{{ __('Country') }}</th>
							<th class="py-3 pe-2">{{ __('Products') }}</th>
							<th class="py-3 pe-2">{{ __('Invoices') }}</th>
							<th class="py-3 pe-2">{{ __('Total Value') }}</th>
						</tr>
					</thead>
					<tbody class="text-gray-900 dark:text-gray-100">
						@foreach($byCountry as $row)
							<tr class="border-b border-gray-100 dark:border-gray-700/60">
								<td class="py-3 pe-2 font-medium">{{ $row->name }}</td>
								<td class="py-3 pe-2">{{ number_format($row->products_count ?? 0) }}</td>
								<td class="py-3 pe-2">{{ number_format($row->invoices_count ?? 0) }}</td>
								<td class="py-3 pe-2">${{ number_format($row->stock_value ?? 0, 2) }}</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<script>
		document.addEventListener('DOMContentLoaded', function() {
			const isDark = document.documentElement.classList.contains('dark');
			const chartTheme = { mode: isDark ? 'dark' : 'light' };

			// Revenue Trends Chart
			const chartDates = @json($chartDates ?? []);
			const chartRevenues = @json($chartRevenues ?? []);

			if (chartDates.length > 0) {
				const revenueChart = new ApexCharts(document.querySelector("#revenueChart"), {
					chart: {
						type: 'area',
						height: 250,
						toolbar: {
							show: false
						},
						sparkline: {
							enabled: false
						},
						zoom: {
							enabled: false
						}
					},
					series: [{
						name: 'Balance',
						data: chartRevenues
					}],
					xaxis: {
						categories: chartDates,
						labels: {
							style: {
								colors: isDark ? '#9ca3af' : '#6b7280',
								fontSize: '11px'
							},
							rotate: -45,
							rotateAlways: false,
							hideOverlappingLabels: true,
							showDuplicates: false,
							trim: false
						},
						axisBorder: {
							show: false
						},
						axisTicks: {
							show: false
						}
					},
					yaxis: {
						labels: {
							style: {
								colors: isDark ? '#9ca3af' : '#6b7280',
								fontSize: '12px'
							},
							formatter: function(value) {
								return '$' + value.toFixed(2);
							}
						}
					},
					stroke: {
						curve: 'smooth',
						width: 3,
						colors: ['#10b981']
					},
					fill: {
						type: 'gradient',
						gradient: {
							shadeIntensity: 1,
							opacityFrom: 0.5,
							opacityTo: 0.1,
							stops: [0, 90, 100]
						},
						colors: ['#10b981']
					},
					dataLabels: {
						enabled: false
					},
					grid: {
						borderColor: isDark ? '#374151' : '#e5e7eb',
						strokeDashArray: 4,
						xaxis: {
							lines: {
								show: false
							}
						},
						yaxis: {
							lines: {
								show: true
							}
						}
					},
					tooltip: {
						enabled: true,
						theme: isDark ? 'dark' : 'light',
						y: {
							formatter: function(value) {
								return '$' + value.toFixed(2);
							}
						}
					},
					markers: {
						size: 0,
						hover: {
							size: 5,
							sizeOffset: 3
						}
					}
				});
				revenueChart.render();
			}

			// Declare chart data variables once (if available)
			@if(!empty($chartData) && isset($chartData['profits']) && !$chartData['profits']->isEmpty())
			const profitsData = @json($chartData['profits']);
			@endif
			@if(!empty($chartData) && isset($chartData['invoices']) && !$chartData['invoices']->isEmpty())
			const invoicesData = @json($chartData['invoices']);
			@endif
			@if(!empty($chartData) && isset($chartData['ads']) && !$chartData['ads']->isEmpty())
			const adsData = @json($chartData['ads']);
			@endif

			// Business Overview Chart - Comprehensive Analytics
			@if(!empty($chartData) && isset($chartData['profits']) && isset($chartData['invoices']) && !$chartData['profits']->isEmpty())
			const businessOverviewData = profitsData;
			
			// Combine data for comprehensive view
			const months = businessOverviewData.map(item => item.month);
			const totalAmounts = businessOverviewData.map(item => parseFloat(item.total_amount) || 0);
			const adsCosts = businessOverviewData.map(item => parseFloat(item.ads_cost) || 0);
			const netProfits = businessOverviewData.map(item => parseFloat(item.net_profit) || 0);
			const invoiceCounts = months.map(month => {
				const invoice = invoicesData.find(inv => inv.month === month);
				return invoice ? parseInt(invoice.count) || 0 : 0;
			});
			const invoiceTotals = months.map(month => {
				const invoice = invoicesData.find(inv => inv.month === month);
				return invoice ? parseFloat(invoice.total) || 0 : 0;
			});

			const businessOverviewChart = new ApexCharts(document.querySelector("#businessOverviewChart"), {
				series: [
					{
						name: '{{ __('Total Amount') }}',
						type: 'column',
						data: totalAmounts
					},
					{
						name: '{{ __('Ads Cost') }}',
						type: 'column',
						data: adsCosts
					},
					{
						name: '{{ __('Net Profit') }}',
						type: 'line',
						data: netProfits
					},
					{
						name: '{{ __('Invoices Count') }}',
						type: 'line',
						data: invoiceCounts
					}
				],
				chart: { 
					height: 400, 
					type: 'line',
					toolbar: { show: true },
					zoom: { enabled: false }
				},
				stroke: { 
					width: [0, 0, 3, 3],
					curve: 'smooth'
				},
				xaxis: { 
					categories: months,
					title: { text: '{{ __('Month') }}' }
				},
				yaxis: [
					{
						title: { text: '{{ __('Amount ($)') }}' },
						labels: { formatter: (value) => '$' + value.toFixed(2) }
					},
					{
						opposite: true,
						title: { text: '{{ __('Count') }}' },
						labels: { formatter: (value) => Math.round(value) }
					}
				],
				colors: ['#F58220', '#ef4444', '#10b981', '#f59e0b'],
				theme: chartTheme,
				dataLabels: { 
					enabled: false 
				},
				legend: {
					position: 'top',
					horizontalAlign: 'center'
				},
				tooltip: {
					shared: true,
					intersect: false,
					y: {
						formatter: function (val, opts) {
							if (opts.seriesIndex === 2 || opts.seriesIndex === 0 || opts.seriesIndex === 1) {
								return '$' + val.toFixed(2);
							}
							return val;
						}
					}
				},
				plotOptions: {
					bar: {
						columnWidth: '50%'
					}
				}
			});
			businessOverviewChart.render();
			@endif

			// Profits Chart
			@if(!empty($chartData) && isset($chartData['profits']) && !$chartData['profits']->isEmpty())
			const profitsChart = new ApexCharts(document.querySelector("#profitsChart"), {
				series: [{
					name: '{{ __('Net Profit') }}',
					data: profitsData.map(item => parseFloat(item.net_profit) || 0)
				}],
				chart: { type: 'area', height: 300, toolbar: { show: false }, zoom: { enabled: false } },
				xaxis: { categories: profitsData.map(item => item.month) },
				stroke: { curve: 'smooth', width: 2 },
				colors: ['#10b981'],
				theme: chartTheme,
				dataLabels: { enabled: false },
				yaxis: { labels: { formatter: (value) => '$' + value.toFixed(2) } }
			});
			profitsChart.render();
			@endif

			// Ads Chart
			@if(!empty($chartData) && isset($chartData['ads']) && !$chartData['ads']->isEmpty())
			const adsChart = new ApexCharts(document.querySelector("#adsChart"), {
				series: [{
					name: '{{ __('Ads Spent') }}',
					data: adsData.map(item => parseFloat(item.spent) || 0)
				}],
				chart: { type: 'bar', height: 300, toolbar: { show: false }, zoom: { enabled: false } },
				xaxis: { categories: adsData.map(item => item.month) },
				colors: ['#ef4444'],
				theme: chartTheme,
				dataLabels: { enabled: false },
				yaxis: { labels: { formatter: (value) => '$' + value.toFixed(2) } }
			});
			adsChart.render();
			@endif

			// Invoices Chart
			@if(!empty($chartData) && isset($chartData['invoices']) && !$chartData['invoices']->isEmpty())
			const invoicesChart = new ApexCharts(document.querySelector("#invoicesChart"), {
				series: [{
					name: '{{ __('Invoices') }}',
					data: invoicesData.map(item => parseInt(item.count) || 0)
				}],
				chart: { type: 'line', height: 300, toolbar: { show: false }, zoom: { enabled: false } },
				xaxis: { categories: invoicesData.map(item => item.month) },
				stroke: { curve: 'smooth', width: 2 },
				colors: ['#F58220'],
				theme: chartTheme,
				dataLabels: { enabled: false }
			});
			invoicesChart.render();
			@endif

			// Revenue vs Ads Chart
			@if(!empty($chartData) && isset($chartData['profits']) && isset($chartData['ads']) && !$chartData['profits']->isEmpty())
			const revenueVsAdsChart = new ApexCharts(document.querySelector("#revenueVsAdsChart"), {
				series: [{
					name: '{{ __('Total Amount') }}',
					data: profitsData.map(item => parseFloat(item.total_amount) || 0)
				}, {
					name: '{{ __('Ads Cost') }}',
					data: profitsData.map(item => parseFloat(item.ads_cost) || 0)
				}],
				chart: { type: 'line', height: 300, toolbar: { show: false }, zoom: { enabled: false } },
				xaxis: { categories: profitsData.map(item => item.month) },
				stroke: { curve: 'smooth', width: 2 },
				colors: ['#10b981', '#ef4444'],
				theme: chartTheme,
				dataLabels: { enabled: false },
				yaxis: { labels: { formatter: (value) => '$' + value.toFixed(2) } }
			});
			revenueVsAdsChart.render();
			@endif
		});
	</script>
</x-admin-layout>
