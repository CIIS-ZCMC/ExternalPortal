<x-filament-panels::page>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="space-y-6">
        {{-- Employee Header Profile Card with Modern Scratch Theme GUI --}}
        <div class="zcmc-calendar-card zcmc-profile-banner flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                {{-- Scratch Gradient Avatar --}}
                <div class="relative flex-shrink-0">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-sky-400 p-[2px] shadow-sm">
                        <div class="w-full h-full rounded-[14px] bg-white dark:bg-slate-900 flex items-center justify-center text-blue-600 dark:text-blue-400 font-black text-xl tracking-tight select-none">
                            {{ $employee ? strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)) : 'EP' }}
                        </div>
                    </div>
                    <span class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-2 border-white dark:border-slate-900 rounded-full" title="Active Staff"></span>
                </div>

                {{-- Staff Info and Badges --}}
                <div class="space-y-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">
                            {{ $employee ? $employee->name : 'External Personnel' }}
                        </h1>
                        @if ($employee)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-blue-50 dark:bg-blue-950/70 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800/80 shadow-2xs">
                                PIN: {{ $employee->biometric_id }}
                            </span>
                        @endif
                    </div>
                    <div class="flex flex-wrap items-center gap-x-2.5 gap-y-1 text-xs text-slate-500 dark:text-slate-400">
                        <span class="inline-flex items-center gap-1 font-medium text-slate-700 dark:text-slate-300">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            {{ $employee ? ($employee->position ?: 'External Staff') : 'Duty Personnel' }}
                        </span>
                        <span class="text-slate-300 dark:text-slate-600">•</span>
                        <span class="inline-flex items-center gap-1 font-medium text-slate-600 dark:text-slate-400">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            {{ $employee ? $employee->agency : 'ZCMC External' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Tab View Segmented Control (Scratch Theme Pill Switcher) --}}
            <div class="zcmc-tab-switcher inline-flex items-center p-1.5 rounded-xl bg-slate-100 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700 select-none shadow-inner">
                <button 
                    type="button" 
                    wire:click="setTab('calendar')"
                    class="zcmc-tab-btn inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-bold transition-all duration-150 {{ $activeTab === 'calendar' ? 'zcmc-tab-btn-active bg-white dark:bg-slate-900 text-blue-600 dark:text-blue-400 shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}"
                >
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Interactive Calendar
                </button>
                <button 
                    type="button" 
                    wire:click="setTab('table')"
                    class="zcmc-tab-btn inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-bold transition-all duration-150 {{ $activeTab === 'table' ? 'zcmc-tab-btn-active bg-white dark:bg-slate-900 text-blue-600 dark:text-blue-400 shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}"
                >
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 4h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Table Grid View
                </button>
            </div>
        </div>

        @if ($activeTab === 'calendar')
            {{-- 8 - 4 Grid Layout (8 cols for Calendar, 4 cols for Shifting Cards) --}}
            <div class="zcmc-grid-layout">
                {{-- Column 8: Interactive FullCalendar --}}
                <div class="zcmc-col-8">
                    <div class="zcmc-calendar-card">
                        <div id="admin-schedule-calendar" class="fc-theme-standard min-h-[720px]"></div>
                    </div>
                </div>

                {{-- Column 4: Scratch Shifting Palette Sidebar --}}
                @php
                    $isSuperAdmin = auth('administrator')->user()?->role === 1;
                    try {
                        $shiftPresetsList = \App\Models\ShiftPreset::active()->get();
                    } catch (\Throwable $e) {
                        $shiftPresetsList = collect();
                    }
                @endphp
                <div class="zcmc-col-4 space-y-4">
                    <div class="zcmc-calendar-card">
                        <div class="flex items-center justify-between gap-2 pb-2.5 mb-2.5 border-b border-slate-200/80 dark:border-slate-800">
                            <div class="flex items-center gap-1.5 min-w-0">
                                <svg class="w-3.5 h-3.5 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-100 tracking-tight truncate">
                                    Shift Presets
                                </span>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold font-mono bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200/70 dark:border-blue-900/60">
                                    {{ $shiftPresetsList->count() }}
                                </span>
                                @if ($isSuperAdmin)
                                    <a 
                                        href="{{ route('filament.administratorPanel.resources.shift-presets.index') }}" 
                                        title="Manage Presets"
                                        class="inline-flex items-center justify-center w-5 h-5 rounded text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-slate-800 transition-colors"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </div>

                        {{-- Draggable Presets Palette Container (Dynamic from ShiftPreset model) --}}
                        <div id="external-shift-palette" class="space-y-1.5">
                            @forelse ($shiftPresetsList as $preset)
                                <div 
                                    class="fc-shift-item zcmc-shift-card"
                                    style="background-color: {{ $preset->color }}; border-color: {{ $preset->border_color ?: $preset->color }};"
                                    data-title="{{ $preset->name }}"
                                    data-duration="{{ sprintf('%02d:00', (int)($preset->total_hours ?: 8)) }}"
                                    data-color="{{ $preset->color }}"
                                    data-border="{{ $preset->border_color ?: $preset->color }}"
                                    data-shifting="{{ $preset->is_shifting ? '1' : '0' }}"
                                    data-first-in="{{ $preset->first_in }}"
                                    data-first-out="{{ $preset->first_out }}"
                                    data-second-in="{{ $preset->second_in }}"
                                    data-second-out="{{ $preset->second_out }}"
                                >
                                    <span class="truncate">{{ $preset->name }}</span>
                                    @if ($preset->badge_label)
                                        <span class="text-[9px] font-mono px-1 py-0.2 rounded bg-black/20 shrink-0 ml-1">{{ $preset->badge_label }}</span>
                                    @endif
                                </div>
                            @empty
                                <div class="text-xs text-slate-400 py-3 text-center">
                                    No presets available.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            @vite(['resources/js/schedule-calendar.js'])

            <script>
                (function() {
                    let initialized = false;

                    function runInit() {
                        const calendarEl = document.getElementById('admin-schedule-calendar');
                        if (!calendarEl) {
                            initialized = false;
                            return;
                        }
                        const employeeId = {{ $employee ? $employee->id : 'null' }};
                        if (!employeeId) return;

                        if (typeof window.initEmployeeScheduleCalendar === 'function') {
                            window.calendarInstance_admin = window.initEmployeeScheduleCalendar({
                                elementId: 'admin-schedule-calendar',
                                externalPaletteId: 'external-shift-palette',
                                employeeId: employeeId,
                                canEdit: true,
                                shiftPresets: @json($shiftPresetsList),
                                eventsUrl: '{{ route("portal.schedules.events") }}',
                                storeUrl: '{{ route("portal.schedules.store") }}',
                                updateDropResizeUrl: '{{ route("portal.schedules.updateDropResize") }}',
                                updateUrl: '{{ route("portal.schedules.update", ":id") }}',
                                destroyUrl: '{{ route("portal.schedules.destroy", ":id") }}',
                                clearAllUrl: '{{ route("portal.schedules.clearAll") }}',
                                csrfToken: '{{ csrf_token() }}',
                            });
                            initialized = true;
                        } else {
                            setTimeout(runInit, 100);
                        }
                    }

                    if (document.readyState === 'complete' || document.readyState === 'interactive') {
                        runInit();
                    } else {
                        document.addEventListener('DOMContentLoaded', runInit, { once: true });
                    }

                    document.addEventListener('livewire:navigated', () => {
                        runInit();
                    });
                })();
            </script>
        @endif
    </div>
</x-filament-panels::page>
