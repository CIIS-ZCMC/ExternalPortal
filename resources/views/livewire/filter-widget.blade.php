<x-filament-widgets::widget>
    <x-filament::section
        icon="heroicon-o-calendar-days"
        icon-color="primary"
        compact
    >
        <x-slot name="heading">
            Attendance Period Selector
        </x-slot>

        <x-slot name="afterHeader">
            <div style="display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem;">
                <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Active Period:</span>
                <x-filament::badge color="primary" size="sm">
                    {{ $months[$selectedMonth] ?? 'Month' }} {{ $selectedYear }}
                </x-filament::badge>
            </div>
        </x-slot>

        {{ $this->form }}
    </x-filament::section>
</x-filament-widgets::widget>
