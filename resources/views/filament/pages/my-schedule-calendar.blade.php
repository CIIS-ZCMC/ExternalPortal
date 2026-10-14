<x-filament-panels::page>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="space-y-6">
        {{-- Employee Header Profile Card with Modern Scratch Theme GUI --}}
        <div class="zcmc-calendar-card zcmc-profile-banner flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                {{-- Scratch Gradient Avatar --}}
                <div class="relative flex-shrink-0">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-emerald-600 via-teal-600 to-cyan-400 p-[2px] shadow-sm">
                        <div class="w-full h-full rounded-[14px] bg-white dark:bg-slate-900 flex items-center justify-center text-emerald-600 dark:text-emerald-400 font-black text-xl tracking-tight select-none">
                            {{ $employee ? strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)) : 'ME' }}
                        </div>
                    </div>
                    <span class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-2 border-white dark:border-slate-900 rounded-full" title="Active Duty"></span>
                </div>

                {{-- Staff Info and Badges --}}
                <div class="space-y-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">
                            {{ $employee ? $employee->name : 'My Profile' }}
                        </h1>
                        @if ($employee)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-emerald-50 dark:bg-emerald-950/70 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/80 shadow-2xs">
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

            {{-- Table View Link Button --}}
            <div class="flex items-center">
                <a 
                    href="{{ \App\Filament\Resources\Schedules\ScheduleResource::getUrl('index') }}"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-bold text-slate-700 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white bg-slate-100 hover:bg-slate-200/80 dark:bg-slate-800 dark:hover:bg-slate-700 border border-slate-200/80 dark:border-slate-700 shadow-2xs transition"
                >
                    <svg class="w-4 h-4 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 4h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Switch to Table View
                </a>
            </div>
        </div>

        {{-- How to Plot Schedule Instruction Banner (ZCMC Scratch Professional UI) --}}
        <div x-data="{ expanded: false }" class="zcmc-guide-card" style="margin-top:10px">
            <div class="zcmc-guide-header">
                <div class="zcmc-guide-intro">
                    <div class="zcmc-guide-icon-box">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="zcmc-guide-title">
                            <span>How to Plot Your Duty Schedule Correctly</span>
                            <span class="zcmc-guide-badge">Guide &amp; Tips</span>
                        </div>
                        <div class="zcmc-guide-desc">
                            Follow these simple steps using drag-and-drop or clicking to assign and manage your duty shifts accurately.
                        </div>
                    </div>
                </div>

                <div>
                    <button 
                        type="button" 
                        @click="expanded = !expanded"
                        class="zcmc-guide-toggle-btn"
                    >
                        <span x-text="expanded ? 'Hide Steps' : 'View Instructions'"></span>
                        <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Collapsible Step-by-Step Instructions --}}
            <div x-show="expanded" x-collapse x-cloak>
                <div class="zcmc-steps-grid">
                    {{-- Step 1 --}}
                    <div class="zcmc-step-card">
                        <div>
                            <div class="zcmc-step-header">
                                <span class="zcmc-step-num">1</span>
                                <h3 class="zcmc-step-title">Drag from Presets</h3>
                            </div>
                            <p class="zcmc-step-body">
                                Pick a preset from the <strong>Shift Presets</strong> sidebar on the right, then <strong>drag &amp; drop</strong> it onto your target date on the calendar.
                            </p>
                        </div>
                        <div class="zcmc-step-footer">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11" /></svg>
                            <span>Single-day assignment</span>
                        </div>
                    </div>

                    {{-- Step 2 --}}
                    <div class="zcmc-step-card">
                        <div>
                            <div class="zcmc-step-header">
                                <span class="zcmc-step-num">2</span>
                                <h3 class="zcmc-step-title">Click or Drag Range</h3>
                            </div>
                            <p class="zcmc-step-body">
                                <strong>Click</strong> an empty date or <strong>drag across consecutive dates</strong> to open the quick plotter popup. Select a preset or specify custom hours.
                            </p>
                        </div>
                        <div class="zcmc-step-footer" style="color:#0d9488 !important;">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            <span>Multi-day date range</span>
                        </div>
                    </div>

                    {{-- Step 3 --}}
                    <div class="zcmc-step-card">
                        <div>
                            <div class="zcmc-step-header">
                                <span class="zcmc-step-num">3</span>
                                <h3 class="zcmc-step-title">Move or Resize</h3>
                            </div>
                            <p class="zcmc-step-body">
                                Move shifts by <strong>dragging</strong> to another day. <strong>Drag the edges</strong> of a shift bar horizontally to extend or reduce consecutive duty days.
                            </p>
                        </div>
                        <div class="zcmc-step-footer" style="color:#0284c7 !important;">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" /></svg>
                            <span>Drag &amp; resize handles</span>
                        </div>
                    </div>

                    {{-- Step 4 --}}
                    <div class="zcmc-step-card">
                        <div>
                            <div class="zcmc-step-header">
                                <span class="zcmc-step-num">4</span>
                                <h3 class="zcmc-step-title">Edit or Delete</h3>
                            </div>
                            <p class="zcmc-step-body">
                                <strong>Click any plotted shift</strong> to modify times or remove it. For continuous spans, choose to cut an individual date or delete the entire span.
                            </p>
                        </div>
                        <div class="zcmc-step-footer" style="color:#e11d48 !important;">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            <span>Targeted edit or delete</span>
                        </div>
                    </div>
                </div>

                {{-- Important Notice / Best Practices Callout --}}
                <div class="zcmc-guide-note">
                    <svg class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <strong>Important:</strong> Verify that night shifts (spillover hours) and shifting designations match your department assignment. Use the <strong>Clear All</strong> button on the calendar toolbar if you need to clear the current month's view or reset your plotted schedule.
                    </div>
                </div>
            </div>
        </div>

        {{-- 8 - 4 Grid Layout (8 cols Calendar, 4 cols Shifting Cards) --}}
        <div class="zcmc-grid-layout">
            {{-- Column 8: Interactive FullCalendar --}}
            <div class="zcmc-col-8">
                <div class="zcmc-calendar-card">
                    <div id="user-calendar" class="fc-theme-standard min-h-[720px]"></div>
                </div>
            </div>

            {{-- Column 4: Scratch Shifting Palette Sidebar --}}
            <div class="zcmc-col-4 space-y-4">
                <div class="zcmc-calendar-card">
                    @php
                        try {
                            $userShiftPresetsList = \App\Models\ShiftPreset::active()->get();
                        } catch (\Throwable $e) {
                            $userShiftPresetsList = collect();
                        }
                    @endphp
                        <div class="flex items-center justify-between gap-2 pb-2.5 mb-2.5 border-b border-slate-200/80 dark:border-slate-800">
                            <div class="flex items-center gap-1.5 min-w-0">
                                <svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-100 tracking-tight truncate">
                                    Shift Presets
                                </span>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold font-mono bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200/70 dark:border-emerald-900/60">
                                    {{ $userShiftPresetsList->count() }}
                                </span>
                            </div>
                        </div>

                    {{-- Draggable Presets Palette Container (Same style as calendar events) --}}
                    <div id="user-shift-palette" class="space-y-1.5">
                        @forelse ($userShiftPresetsList as $preset)
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
                function runInit() {
                    const calendarEl = document.getElementById('user-calendar');
                    if (!calendarEl) return;

                    const employeeId = {{ $employee ? $employee->id : 'null' }};
                    if (!employeeId) return;

                    if (typeof window.initEmployeeScheduleCalendar === 'function') {
                        window.calendarInstance_user_calendar = window.initEmployeeScheduleCalendar({
                            elementId: 'user-calendar',
                            externalPaletteId: 'user-shift-palette',
                            employeeId: employeeId,
                            canEdit: true,
                            shiftPresets: @json($userShiftPresetsList),
                            eventsUrl: '{{ route("portal.schedules.events") }}',
                            storeUrl: '{{ route("portal.schedules.store") }}',
                            updateDropResizeUrl: '{{ route("portal.schedules.updateDropResize") }}',
                            updateUrl: '{{ route("portal.schedules.update", ":id") }}',
                            destroyUrl: '{{ route("portal.schedules.destroy", ":id") }}',
                            clearAllUrl: '{{ route("portal.schedules.clearAll") }}',
                            csrfToken: '{{ csrf_token() }}',
                        });
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
    </div>
</x-filament-panels::page>
