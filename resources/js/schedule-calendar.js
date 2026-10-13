import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin, { Draggable } from '@fullcalendar/interaction';
import Swal from 'sweetalert2';

window.FullCalendarBundle = {
    Calendar,
    dayGridPlugin,
    timeGridPlugin,
    interactionPlugin,
    Draggable,
    Swal,
};

window.initEmployeeScheduleCalendar = function (config) {
    const calendarEl = document.getElementById(config.elementId);
    if (!calendarEl) return;

    // Check if an existing calendar instance is attached to avoid duplicate event listeners & duplicate toast notifications
    const instanceKey = `calendarInstance_${config.elementId}`;
    if (window[instanceKey] && typeof window[instanceKey].destroy === 'function') {
        window[instanceKey].destroy();
        window[instanceKey] = null;
    }
    if (calendarEl._fcDraggableInstance && typeof calendarEl._fcDraggableInstance.destroy === 'function') {
        calendarEl._fcDraggableInstance.destroy();
        calendarEl._fcDraggableInstance = null;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
        || config.csrfToken 
        || '';

    const employeeId = config.employeeId;
    const canEdit = config.canEdit !== undefined ? config.canEdit : true;

    // Toast alert helper using SweetAlert2
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    // Track active drag target day highlighting
    let currentHoveredCell = null;
    function setHighlightCell(cell) {
        if (currentHoveredCell === cell) return;
        if (currentHoveredCell) {
            currentHoveredCell.classList.remove('fc-drag-target-day');
        }
        currentHoveredCell = cell;
        if (currentHoveredCell) {
            currentHoveredCell.classList.add('fc-drag-target-day');
        }
    }
    function clearHighlightCell() {
        if (currentHoveredCell) {
            currentHoveredCell.classList.remove('fc-drag-target-day');
            currentHoveredCell = null;
        }
    }

    // Pointer move listener while dragging to highlight target landing day
    let isDraggingItem = false;
    document.addEventListener('pointermove', function (e) {
        if (!isDraggingItem) return;
        const elem = document.elementFromPoint(e.clientX, e.clientY);
        if (!elem) {
            clearHighlightCell();
            return;
        }
        const cell = elem.closest('.fc-daygrid-day, .fc-timegrid-col, .fc-timegrid-slot');
        if (cell && calendarEl.contains(cell)) {
            setHighlightCell(cell);
        } else {
            clearHighlightCell();
        }
    });

    document.addEventListener('pointerup', function () {
        if (isDraggingItem) {
            isDraggingItem = false;
            clearHighlightCell();
        }
    });

    // Initialize external draggable elements if available
    const externalContainerEl = document.getElementById(config.externalPaletteId);
    if (externalContainerEl && canEdit) {
        externalContainerEl.addEventListener('pointerdown', function (e) {
            const shiftItem = e.target.closest('.fc-shift-item');
            if (shiftItem) {
                isDraggingItem = true;
            }
        });

        calendarEl._fcDraggableInstance = new Draggable(externalContainerEl, {
            itemSelector: '.fc-shift-item',
            appendTo: document.body,
            minDistance: 0,
            eventData: function (eventEl) {
                return {
                    title: eventEl.getAttribute('data-title') || 'Shift',
                    duration: eventEl.getAttribute('data-duration') || '08:00',
                    backgroundColor: eventEl.getAttribute('data-color') || '#0ea5e9',
                    borderColor: eventEl.getAttribute('data-border') || '#0284c7',
                    textColor: '#ffffff',
                    extendedProps: {
                        is_shifting: eventEl.getAttribute('data-shifting') === '1',
                        first_in: eventEl.getAttribute('data-first-in') || '08:00:00',
                        first_out: eventEl.getAttribute('data-first-out') || null,
                        second_in: eventEl.getAttribute('data-second-in') || null,
                        second_out: eventEl.getAttribute('data-second-out') || '16:00:00',
                    }
                };
            }
        });
    }

    // Helper to format local date without UTC timezone shift (e.g., YYYY-MM-DD)
    function formatLocalDate(date) {
        if (!date) return '';
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    // Helper to format local time HH:mm:ss
    function formatLocalTime(date) {
        if (!date) return '00:00:00';
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        const seconds = String(date.getSeconds()).padStart(2, '0');
        return `${hours}:${minutes}:${seconds}`;
    }

    // Drop from external shift presets
    let lastDroppedDateStr = null;

    // Helper to prompt and clear schedules
    function triggerClearAllSchedules() {
        if (!canEdit) return;

        const currentView = calendar.view;
        const viewTitle = currentView ? currentView.title : 'Visible Period';
        const viewStart = currentView ? formatLocalDate(currentView.activeStart) : null;
        const viewEnd = currentView ? formatLocalDate(new Date(currentView.activeEnd.getTime() - 24 * 3600 * 1000)) : null;

        Swal.fire({
            title: 'Clear Duty Schedules',
            html: `
                <div style="text-align: left; font-size: 0.9rem;" class="space-y-3">
                    <p class="text-slate-600 dark:text-slate-300">
                        Choose the scope of schedules you want to permanently remove:
                    </p>
                    <div style="display:flex; flex-direction:column; gap:10px; margin-top:8px;">
                        <label style="display:flex; align-items:flex-start; gap:8px; padding:10px; border:1px solid #e2e8f0; border-radius:8px; cursor:pointer;">
                            <input type="radio" name="clear-scope" value="period" checked style="margin-top:2px;">
                            <div>
                                <strong style="display:block; color:#0f172a;">Visible View Only (${viewTitle})</strong>
                                <span style="font-size:0.8rem; color:#64748b;">Deletes schedules within this view range (${viewStart} to ${viewEnd}).</span>
                            </div>
                        </label>
                        <label style="display:flex; align-items:flex-start; gap:8px; padding:10px; border:1px solid #fecaca; background-color:#fff1f2; border-radius:8px; cursor:pointer;">
                            <input type="radio" name="clear-scope" value="all" style="margin-top:2px;">
                            <div>
                                <strong style="display:block; color:#991b1b;">Entire Schedule History (All Dates)</strong>
                                <span style="font-size:0.8rem; color:#b91c1c;">Permanently deletes ALL duty schedules plotted for this staff member.</span>
                            </div>
                        </label>
                    </div>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Clear Schedules',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
        }).then((result) => {
            if (!result.isConfirmed) return;

            const selectedScope = document.querySelector('input[name="clear-scope"]:checked')?.value || 'period';
            const clearUrl = config.clearAllUrl || (config.storeUrl.replace('/store', '/clear-all'));

            const payload = {
                employee_id: employeeId,
            };

            if (selectedScope === 'period' && viewStart && viewEnd) {
                payload.start = viewStart;
                payload.end = viewEnd;
            }

            fetch(clearUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(payload),
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    Toast.fire({ icon: 'success', title: res.message || 'Schedules cleared successfully.' });
                    calendar.refetchEvents();
                } else {
                    Toast.fire({ icon: 'error', title: res.error || 'Failed to clear schedules.' });
                }
            })
            .catch(err => {
                console.error(err);
                Toast.fire({ icon: 'error', title: 'Network error while clearing schedules.' });
            });
        });
    }

    const calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
        initialView: 'dayGridMonth',
        customButtons: {
            clearAllBtn: {
                text: 'Clear All',
                hint: 'Remove all plotted schedules',
                click: function () {
                    triggerClearAllSchedules();
                }
            }
        },
        headerToolbar: {
            left: 'prev,next today clearAllBtn',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        height: 'auto',
        aspectRatio: 1.5,
        editable: canEdit,
        droppable: canEdit,
        selectable: canEdit,
        selectMirror: true,
        dayMaxEvents: 3,
        nowIndicator: true,
        eventDisplay: 'block',
        eventDurationEditable: canEdit,
        eventStartEditable: canEdit,
        eventResizableFromStart: true,
        snapDuration: '00:15:00',
        slotDuration: '00:30:00',

        eventDragStart: function () {
            isDraggingItem = true;
        },
        eventDragStop: function () {
            isDraggingItem = false;
            clearHighlightCell();
        },
        eventResizeStart: function () {
            isDraggingItem = true;
        },
        eventResizeStop: function () {
            isDraggingItem = false;
            clearHighlightCell();
        },

        // Fetch events from backend API
        events: function (fetchInfo, successCallback, failureCallback) {
            const url = new URL(config.eventsUrl, window.location.origin);
            url.searchParams.append('employee_id', employeeId);
            url.searchParams.append('start', fetchInfo.startStr);
            url.searchParams.append('end', fetchInfo.endStr);

            fetch(url.toString(), {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => {
                if (!res.ok) throw new Error('Failed to fetch events');
                return res.json();
            })
            .then(data => {
                successCallback(data);
            })
            .catch(err => {
                console.error(err);
                Toast.fire({ icon: 'error', title: 'Could not load duty schedules.' });
                failureCallback(err);
            });
        },

        // Drop from external shift presets
        drop: function (info) {
            isDraggingItem = false;
            clearHighlightCell();
            // info.dateStr gives exact date string (e.g. "2026-10-14")
            lastDroppedDateStr = info.dateStr ? info.dateStr.split('T')[0] : formatLocalDate(info.date);
        },

        eventReceive: function (info) {
            if (!canEdit) return;

            const ext = info.event.extendedProps || {};
            // Prefer the exact drop dateStr if available, otherwise local date
            const dtrDate = lastDroppedDateStr || formatLocalDate(info.event.start);
            lastDroppedDateStr = null;

            // Calculate formatted time
            const firstIn = ext.first_in || '08:00:00';
            const secondOut = ext.second_out || '16:00:00';

            const payload = {
                employee_id: employeeId,
                dtr_date: dtrDate,
                is_shifting: ext.is_shifting ? 1 : 0,
                first_in: firstIn,
                first_out: ext.first_out || null,
                second_in: ext.second_in || null,
                second_out: secondOut,
            };

            fetch(config.storeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(payload),
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    Toast.fire({ icon: 'success', title: 'Shift plotted for ' + dtrDate });
                    calendar.refetchEvents();
                } else {
                    info.revert();
                    Toast.fire({ icon: 'error', title: res.error || 'Failed to save schedule.' });
                }
            })
            .catch(err => {
                console.error(err);
                info.revert();
                Toast.fire({ icon: 'error', title: 'Network error occurred.' });
            });
        },

        // Drag to move across days or time slots
        eventDrop: function (info) {
            if (!canEdit) return;

            const event = info.event;
            const scheduleId = event.extendedProps.schedule_id || event.id;
            const startDate = event.start;
            const endDate = event.end || new Date(startDate.getTime() + 8 * 3600 * 1000);

            // Format date YYYY-MM-DD locally to prevent UTC date shift
            const dtrDate = formatLocalDate(startDate);
            const firstIn = formatLocalTime(startDate);
            const secondOut = formatLocalTime(endDate);

            fetch(config.updateDropResizeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    id: scheduleId,
                    employee_id: employeeId,
                    dtr_date: dtrDate,
                    first_in: firstIn,
                    second_out: secondOut,
                }),
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    Toast.fire({ icon: 'success', title: 'Schedule moved to ' + dtrDate });
                    calendar.refetchEvents();
                } else {
                    info.revert();
                    Toast.fire({ icon: 'error', title: res.error || 'Update failed.' });
                }
            })
            .catch(err => {
                console.error(err);
                info.revert();
                Toast.fire({ icon: 'error', title: 'Network error.' });
            });
        },

        // Extend or shorten shift duration / replicate across days
        eventResize: function (info) {
            if (!canEdit) return;

            const event = info.event;
            const ext = event.extendedProps || {};
            const scheduleId = ext.schedule_id || event.id;
            const startDate = event.start;
            const endDate = event.end;

            const dtrDate = formatLocalDate(startDate);
            const firstIn = ext.first_in || formatLocalTime(startDate);
            const secondOut = ext.second_out || (endDate ? formatLocalTime(endDate) : '16:00:00');

            // Calculate inclusive end date when extending across multiple calendar days
            let endDateStr = null;
            if (endDate) {
                // In FullCalendar dayGrid, endDate is exclusive midnight of the next day.
                // If dragged to day 8, endDate is 2026-10-09T00:00:00.
                const endCalDate = new Date(endDate.getTime());
                if (endCalDate.getHours() === 0 && endCalDate.getMinutes() === 0) {
                    endCalDate.setDate(endCalDate.getDate() - 1);
                }
                const formattedInclusiveEnd = formatLocalDate(endCalDate);
                if (formattedInclusiveEnd > dtrDate) {
                    endDateStr = formattedInclusiveEnd;
                }
            }

            fetch(config.updateDropResizeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    id: scheduleId,
                    employee_id: employeeId,
                    dtr_date: dtrDate,
                    end_date: endDateStr,
                    first_in: firstIn,
                    second_out: secondOut,
                }),
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    const msg = endDateStr 
                        ? `Shift extended from ${dtrDate} to ${endDateStr}.`
                        : `Shift duration updated for ${dtrDate}.`;
                    Toast.fire({ icon: 'success', title: msg });
                    calendar.refetchEvents();
                } else {
                    info.revert();
                    Toast.fire({ icon: 'error', title: res.error || 'Resize failed.' });
                }
            })
            .catch(err => {
                console.error(err);
                info.revert();
                Toast.fire({ icon: 'error', title: 'Network error.' });
            });
        },

        // Click on an event to inspect / edit / delete
        eventClick: function (info) {
            const ext = info.event.extendedProps || {};
            const scheduleId = ext.schedule_id || info.event.id;
            const startDateStr = ext.dtr_date || info.event.startStr.split('T')[0];
            const endDateStr = ext.end_date || startDateStr;
            const isMultiDay = endDateStr && endDateStr > startDateStr;

            // Generate list of all individual dates covered by this shift
            const dateList = [];
            let curr = new Date(startDateStr + 'T00:00:00');
            const endLimit = new Date(endDateStr + 'T00:00:00');
            while (curr <= endLimit) {
                dateList.push(formatLocalDate(curr));
                curr.setDate(curr.getDate() + 1);
            }

            // Determine which specific day of the bar was clicked
            let clickedDateStr = startDateStr;
            if (info.jsEvent && info.jsEvent.clientX) {
                const elements = document.elementsFromPoint(info.jsEvent.clientX, info.jsEvent.clientY);
                for (const el of elements) {
                    const dayCell = el.closest('[data-date]');
                    if (dayCell && dayCell.getAttribute('data-date')) {
                        const cellDate = dayCell.getAttribute('data-date');
                        if (dateList.includes(cellDate)) {
                            clickedDateStr = cellDate;
                            break;
                        }
                    }
                }
            }

            const isShifting = ext.is_shifting ? '1' : '0';
            const firstIn = (ext.first_in || '08:00:00').substring(0, 5);
            const secondOut = (ext.second_out || '17:00:00').substring(0, 5);

            if (!canEdit) {
                Swal.fire({
                    title: info.event.title,
                    html: `
                        <div class="text-left space-y-2 text-sm">
                            <p><strong>Date Span:</strong> ${startDateStr}${isMultiDay ? ' to ' + endDateStr : ''}</p>
                            <p><strong>Selected Day:</strong> ${clickedDateStr}</p>
                            <p><strong>Type:</strong> ${ext.is_shifting ? 'Shifting Duty' : 'Office Hours'}</p>
                            <p><strong>Time In:</strong> ${ext.first_in || '--'}</p>
                            <p><strong>Time Out:</strong> ${ext.second_out || '--'}</p>
                        </div>
                    `,
                    icon: 'info',
                    confirmButtonText: 'Close',
                    confirmButtonColor: '#3b82f6',
                });
                return;
            }

            // Build Date Selection (Dropdown for multi-day, or date input)
            let dateSelectionHtml = '';
            if (isMultiDay) {
                const optionsHtml = dateList.map(d => `<option value="${d}" ${d === clickedDateStr ? 'selected' : ''}>Day ${d.split('-')[2]} (${d})</option>`).join('');
                dateSelectionHtml = `
                    <div style="background-color:#f0fdf4; border:1px solid #bbf7d0; border-radius:6px; padding:8px 10px; margin-bottom:12px; font-size:0.82rem; color:#166534;">
                        <strong>Continuous Span:</strong> ${startDateStr} to ${endDateStr}<br>
                        Select a target date to cut/update, or choose to remove the entire span.
                    </div>
                    <div>
                        <label style="display:block; font-weight:600; margin-bottom:4px; font-size:0.85rem;">Target Date in Span</label>
                        <select id="swal-date" class="swal2-select" style="width:100%; margin:0; font-size:0.9rem;">
                            ${optionsHtml}
                        </select>
                    </div>
                    <div style="margin-top:10px; padding:8px 10px; background-color:#f8fafc; border:1px solid #e2e8f0; border-radius:6px;">
                        <span style="display:block; font-weight:600; font-size:0.82rem; margin-bottom:4px; color:#475569;">Deletion Action:</span>
                        <div style="display:flex; flex-direction:column; gap:6px; font-size:0.82rem;">
                            <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
                                <input type="radio" name="swal-delete-scope" value="single" checked style="width:16px; height:16px;">
                                <span><strong>Cut / Remove Selected Day Only</strong> (creates a gap)</span>
                            </label>
                            <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
                                <input type="radio" name="swal-delete-scope" value="all_span" style="width:16px; height:16px;">
                                <span style="color:#dc2626;"><strong>Remove All in Span</strong> (${startDateStr} to ${endDateStr})</span>
                            </label>
                        </div>
                    </div>
                `;
            } else {
                dateSelectionHtml = `
                    <div>
                        <label style="display:block; font-weight:600; margin-bottom:4px;">Date</label>
                        <input id="swal-date" type="date" value="${clickedDateStr}" class="swal2-input" style="width:100%; margin:0;">
                    </div>
                `;
            }

            // Variables to capture input values before modal closes
            let capturedTargetDate = clickedDateStr;
            let capturedDeleteScope = 'single';

            Swal.fire({
                title: 'Manage Shift',
                html: `
                    <div style="text-align: left; font-size: 0.9rem;" class="space-y-3">
                        ${dateSelectionHtml}
                        <div style="margin-top:10px;">
                            <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                                <input id="swal-shifting" type="checkbox" ${ext.is_shifting ? 'checked' : ''} style="width:18px; height:18px;">
                                <span style="font-weight:600;">Is Shifting Shift</span>
                            </label>
                        </div>
                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px; margin-top:10px;">
                            <div>
                                <label style="display:block; font-weight:600; margin-bottom:4px;">Shift In</label>
                                <input id="swal-in" type="time" value="${firstIn}" class="swal2-input" style="width:100%; margin:0;">
                            </div>
                            <div>
                                <label style="display:block; font-weight:600; margin-bottom:4px;">Shift Out</label>
                                <input id="swal-out" type="time" value="${secondOut}" class="swal2-input" style="width:100%; margin:0;">
                            </div>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonText: 'Save Changes',
                denyButtonText: isMultiDay ? 'Delete Selected' : 'Delete Shift',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#2563eb',
                denyButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                didOpen: () => {
                    const dateEl = document.getElementById('swal-date');
                    if (dateEl) {
                        dateEl.addEventListener('change', () => {
                            capturedTargetDate = dateEl.value;
                        });
                    }
                    const radioEls = document.querySelectorAll('input[name="swal-delete-scope"]');
                    radioEls.forEach(r => {
                        r.addEventListener('change', () => {
                            capturedDeleteScope = r.value;
                            const denyBtn = Swal.getDenyButton();
                            if (denyBtn) {
                                denyBtn.innerText = r.value === 'all_span' ? 'Remove All in Span' : 'Cut / Remove Selected Day';
                            }
                        });
                    });
                },
                preDeny: () => {
                    const dateEl = document.getElementById('swal-date');
                    if (dateEl && dateEl.value) {
                        capturedTargetDate = dateEl.value;
                    }
                    const checkedRadio = document.querySelector('input[name="swal-delete-scope"]:checked');
                    if (checkedRadio) {
                        capturedDeleteScope = checkedRadio.value;
                    }
                    return true;
                },
                preConfirm: () => {
                    const date = document.getElementById('swal-date')?.value || clickedDateStr;
                    const shifting = document.getElementById('swal-shifting').checked;
                    const inTime = document.getElementById('swal-in').value;
                    const outTime = document.getElementById('swal-out').value;

                    if (!date || !inTime || !outTime) {
                        Swal.showValidationMessage('Date, Time In, and Time Out are required');
                        return false;
                    }
                    return {
                        dtr_date: date,
                        is_shifting: shifting ? 1 : 0,
                        first_in: inTime + ':00',
                        second_out: outTime + ':00',
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const data = result.value;
                    const updateUrl = config.updateUrl.replace(':id', scheduleId);

                    fetch(updateUrl, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({
                            employee_id: employeeId,
                            dtr_date: data.dtr_date,
                            is_shifting: data.is_shifting,
                            first_in: data.first_in,
                            second_out: data.second_out,
                            first_out: null,
                            second_in: null,
                        }),
                    })
                    .then(r => r.json())
                    .then(res => {
                        if (res.success) {
                            Toast.fire({ icon: 'success', title: 'Shift updated successfully.' });
                            calendar.refetchEvents();
                        } else {
                            Toast.fire({ icon: 'error', title: res.error || 'Failed to update shift.' });
                        }
                    });
                } else if (result.isDenied) {
                    // Check if deleting the entire span or just single target date
                    if (capturedDeleteScope === 'all_span') {
                        // Clear all days in this span
                        const clearUrl = config.clearAllUrl || (config.storeUrl.replace('/store', '/clear-all'));
                        fetch(clearUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: JSON.stringify({
                                employee_id: employeeId,
                                start: startDateStr,
                                end: endDateStr,
                            }),
                        })
                        .then(r => r.json())
                        .then(res => {
                            if (res.success) {
                                Toast.fire({ icon: 'success', title: `All shifts from ${startDateStr} to ${endDateStr} removed.` });
                                calendar.refetchEvents();
                            } else {
                                Toast.fire({ icon: 'error', title: res.error || 'Delete failed.' });
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            Toast.fire({ icon: 'error', title: 'Failed to delete schedule span.' });
                        });
                    } else {
                        // Delete specific target date (creates clean cut in continuous bar)
                        const targetDate = capturedTargetDate || clickedDateStr;
                        const safeScheduleId = scheduleId || 0;
                        const deleteUrl = config.destroyUrl.replace(':id', safeScheduleId) + '?employee_id=' + employeeId + '&dtr_date=' + targetDate;
                        
                        fetch(deleteUrl, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest',
                            }
                        })
                        .then(r => r.json())
                        .then(res => {
                            if (res.success) {
                                Toast.fire({ icon: 'success', title: `Schedule on ${targetDate} deleted (cut created).` });
                                calendar.refetchEvents();
                            } else {
                                Toast.fire({ icon: 'error', title: res.error || 'Delete failed.' });
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            Toast.fire({ icon: 'error', title: 'Failed to delete schedule.' });
                        });
                    }
                }
            });
        },

        // Click or drag across multiple dates to plot shifts
        select: function (info) {
            if (!canEdit) return;

            const startDate = info.startStr.split('T')[0];
            // info.end is exclusive in FullCalendar, convert to inclusive end
            const inclusiveEndDate = new Date(info.end.getTime());
            inclusiveEndDate.setDate(inclusiveEndDate.getDate() - 1);
            const endDate = formatLocalDate(inclusiveEndDate);

            const isMultiDay = endDate > startDate;
            const promptTitle = isMultiDay 
                ? `Plot Shift from ${startDate} to ${endDate}` 
                : `Plot Shift on ${startDate}`;

            Swal.fire({
                title: promptTitle,
                html: `
                    <div style="text-align: left; font-size: 0.9rem;" class="space-y-3">
                        <div style="display:flex; flex-direction:column; gap:6px;">
                            <label style="font-weight:600;">Shift Preset</label>
                            <select id="swal-quick-shift" class="swal2-select" style="width:100%; margin:0;">
                                <option value="22:00:00|06:00:00|1" selected>Night Shift (10:00 PM - 06:00 AM)</option>
                                <option value="06:00:00|14:00:00|1">Morning Shift (06:00 AM - 02:00 PM)</option>
                                <option value="14:00:00|22:00:00|1">Afternoon Shift (02:00 PM - 10:00 PM)</option>
                                <option value="08:00:00|17:00:00|0">Regular Office Hours (08:00 AM - 05:00 PM)</option>
                                <option value="08:00:00|16:00:00|1">Day Shift (08:00 AM - 04:00 PM)</option>
                                <option value="custom">Custom Hours...</option>
                            </select>
                        </div>
                        <div id="swal-custom-times" style="display:none; grid-template-columns:1fr 1fr; gap:10px; margin-top:10px;">
                            <div>
                                <label style="display:block; font-weight:600; margin-bottom:4px;">Time In</label>
                                <input id="swal-new-in" type="time" value="22:00" class="swal2-input" style="width:100%; margin:0;">
                            </div>
                            <div>
                                <label style="display:block; font-weight:600; margin-bottom:4px;">Time Out</label>
                                <input id="swal-new-out" type="time" value="06:00" class="swal2-input" style="width:100%; margin:0;">
                            </div>
                        </div>
                    </div>
                `,
                didOpen: () => {
                    const sel = document.getElementById('swal-quick-shift');
                    const customDiv = document.getElementById('swal-custom-times');
                    sel.addEventListener('change', () => {
                        customDiv.style.display = sel.value === 'custom' ? 'grid' : 'none';
                    });
                },
                showCancelButton: true,
                confirmButtonText: 'Plot Shift',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#64748b',
                preConfirm: () => {
                    const sel = document.getElementById('swal-quick-shift').value;
                    let firstIn = '22:00:00';
                    let secondOut = '06:00:00';
                    let isShifting = 1;

                    if (sel === 'custom') {
                        const inVal = document.getElementById('swal-new-in').value;
                        const outVal = document.getElementById('swal-new-out').value;
                        if (!inVal || !outVal) {
                            Swal.showValidationMessage('Please provide both Time In and Time Out');
                            return false;
                        }
                        firstIn = inVal + ':00';
                        secondOut = outVal + ':00';
                        isShifting = 1;
                    } else {
                        const parts = sel.split('|');
                        firstIn = parts[0];
                        secondOut = parts[1];
                        isShifting = parseInt(parts[2], 10);
                    }

                    return {
                        employee_id: employeeId,
                        dtr_date: startDate,
                        end_date: isMultiDay ? endDate : null,
                        is_shifting: isShifting,
                        first_in: firstIn,
                        second_out: secondOut,
                        first_out: isShifting ? null : '12:00:00',
                        second_in: isShifting ? null : '13:00:00',
                    };
                }
            }).then((result) => {
                calendar.unselect();
                if (result.isConfirmed) {
                    fetch(config.storeUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify(result.value),
                    })
                    .then(r => r.json())
                    .then(res => {
                        if (res.success) {
                            Toast.fire({ icon: 'success', title: isMultiDay ? `Shifts plotted from ${startDate} to ${endDate}` : `Shift plotted for ${startDate}` });
                            calendar.refetchEvents();
                        } else {
                            Toast.fire({ icon: 'error', title: res.error || 'Failed to plot shift.' });
                        }
                    });
                }
            });
        }
    });

    calendar.render();
    window[`calendarInstance_${config.elementId}`] = calendar;
    return calendar;
};
