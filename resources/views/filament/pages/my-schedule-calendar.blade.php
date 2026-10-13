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
                    <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-100 dark:border-slate-800">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Duty Shift Presets
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Drag any card onto the calendar to plot shift.
                            </p>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                            Presets
                        </span>
                    </div>

                    {{-- Draggable Presets Palette Container (Same style as calendar events) --}}
                    <div id="user-shift-palette" class="space-y-1.5">
                        {{-- Morning Shift --}}
                        <div 
                            class="fc-shift-item zcmc-shift-card zcmc-shift-morning"
                            data-title="Shift: 06:00 AM - 02:00 PM"
                            data-duration="08:00"
                            data-color="#0284c7"
                            data-border="#0369a1"
                            data-shifting="1"
                            data-first-in="06:00:00"
                            data-second-out="14:00:00"
                        >
                            <span class="truncate">Morning: 06:00 AM - 02:00 PM</span>
                            <span class="text-[9px] font-mono px-1 py-0.2 rounded bg-black/20 shrink-0 ml-1">8h</span>
                        </div>

                        {{-- Afternoon Shift --}}
                        <div 
                            class="fc-shift-item zcmc-shift-card zcmc-shift-afternoon"
                            data-title="Shift: 02:00 PM - 10:00 PM"
                            data-duration="08:00"
                            data-color="#d97706"
                            data-border="#b45309"
                            data-shifting="1"
                            data-first-in="14:00:00"
                            data-second-out="22:00:00"
                        >
                            <span class="truncate">Afternoon: 02:00 PM - 10:00 PM</span>
                            <span class="text-[9px] font-mono px-1 py-0.2 rounded bg-black/20 shrink-0 ml-1">8h</span>
                        </div>

                        {{-- Night Shift --}}
                        <div 
                            class="fc-shift-item zcmc-shift-card zcmc-shift-night"
                            data-title="Shift: 10:00 PM - 06:00 AM"
                            data-duration="08:00"
                            data-color="#4f46e5"
                            data-border="#4338ca"
                            data-shifting="1"
                            data-first-in="22:00:00"
                            data-second-out="06:00:00"
                        >
                            <span class="truncate">Night: 10:00 PM - 06:00 AM</span>
                            <span class="text-[9px] font-mono px-1 py-0.2 rounded bg-black/20 shrink-0 ml-1">Grave</span>
                        </div>

                        {{-- Regular Office Hours --}}
                        <div 
                            class="fc-shift-item zcmc-shift-card zcmc-shift-office"
                            data-title="Office Hrs: 08:00 AM - 05:00 PM"
                            data-duration="09:00"
                            data-color="#059669"
                            data-border="#047857"
                            data-shifting="0"
                            data-first-in="08:00:00"
                            data-first-out="12:00:00"
                            data-second-in="13:00:00"
                            data-second-out="17:00:00"
                        >
                            <span class="truncate">Office: 08:00 AM - 05:00 PM</span>
                            <span class="text-[9px] font-mono px-1 py-0.2 rounded bg-black/20 shrink-0 ml-1">Std</span>
                        </div>

                        {{-- Day Shift --}}
                        <div 
                            class="fc-shift-item zcmc-shift-card zcmc-shift-day"
                            data-title="Shift: 08:00 AM - 04:00 PM"
                            data-duration="08:00"
                            data-color="#0d9488"
                            data-border="#0f766e"
                            data-shifting="1"
                            data-first-in="08:00:00"
                            data-second-out="16:00:00"
                        >
                            <span class="truncate">Day: 08:00 AM - 04:00 PM</span>
                            <span class="text-[9px] font-mono px-1 py-0.2 rounded bg-black/20 shrink-0 ml-1">8h</span>
                        </div>
                    </div>

                    {{-- Quick Tip Helper --}}
                    <div class="mt-3.5 pt-3 border-t border-slate-100 dark:border-slate-800 text-[11px] text-slate-500 dark:text-slate-400 space-y-1.5">
                        <div class="flex items-start gap-1.5">
                            <svg width="14" height="14" style="width: 14px !important; height: 14px !important; min-width: 14px !important; min-height: 14px !important;" class="text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span><strong>Fast Drag:</strong> Instant drop onto any calendar day without delay.</span>
                        </div>
                        <div class="flex items-start gap-1.5">
                            <svg width="14" height="14" style="width: 14px !important; height: 14px !important; min-width: 14px !important; min-height: 14px !important;" class="text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                            </svg>
                            <span><strong>Extend Duration:</strong> Drag edge of a shift to adjust hours.</span>
                        </div>
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
