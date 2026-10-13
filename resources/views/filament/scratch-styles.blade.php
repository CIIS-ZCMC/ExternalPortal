<link rel="stylesheet" href="{{ asset('css/scratch.css') }}?v={{ file_exists(public_path('css/scratch.css')) ? filemtime(public_path('css/scratch.css')) : time() }}">
<style>
    /* ==========================================================================
       ZCMC External Portal - Scratch CSS (Inlined for instant reliability)
       ========================================================================== */

    /* --------------------------------------------------------------------------
       1. Brand Logo & Header Constraints (Compact, Professional Sizing)
       -------------------------------------------------------------------------- */
    .fi-logo,
    .fi-sidebar-header .fi-logo,
    .fi-topbar .fi-logo {
        height: 1.85rem !important;
        max-height: 1.85rem !important;
        display: flex !important;
        align-items: center !important;
    }

    .fi-logo img,
    .fi-sidebar-header .fi-logo img,
    .fi-topbar .fi-logo img,
    .fi-sidebar-header img,
    .fi-topbar img,
    .zcmc-brand-seal {
        width: 1.5rem !important;
        height: 1.5rem !important;
        min-width: 1.5rem !important;
        min-height: 1.5rem !important;
        max-width: 1.5rem !important;
        max-height: 1.5rem !important;
        object-fit: contain !important;
        flex-shrink: 0 !important;
        display: block !important;
    }

    .zcmc-brand-logo {
        display: inline-flex !important;
        align-items: center !important;
        gap: 0.5rem !important;
        height: 1.85rem !important;
        max-height: 1.85rem !important;
        user-select: none !important;
    }

    .zcmc-brand-seal-wrapper {
        position: relative !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 1.75rem !important;
        height: 1.75rem !important;
        min-width: 1.75rem !important;
        min-height: 1.75rem !important;
        max-width: 1.75rem !important;
        max-height: 1.75rem !important;
        overflow: hidden !important;
        border-radius: 0.375rem !important;
        padding: 0.125rem !important;
        flex-shrink: 0 !important;
        background-color: #ffffff !important;
    }

    .zcmc-brand-text {
        display: flex !important;
        flex-direction: column !important;
        justify-content: center !important;
        line-height: 1.15 !important;
        white-space: nowrap !important;
    }

    .zcmc-brand-title {
        font-size: 0.8125rem !important; /* ~13px */
        font-weight: 700 !important;
        letter-spacing: -0.01em !important;
        display: flex !important;
        align-items: center !important;
        gap: 0.35rem !important;
    }

    .zcmc-brand-subtitle {
        font-size: 0.625rem !important; /* ~10px */
        font-weight: 500 !important;
        letter-spacing: 0.01em !important;
        opacity: 0.75 !important;
    }

    .zcmc-brand-tag {
        font-size: 0.5625rem !important; /* ~9px */
        font-weight: 700 !important;
        padding: 0.08rem 0.3rem !important;
        border-radius: 0.25rem !important;
        line-height: 1 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.04em !important;
    }

    /* Sidebar Header Compact Padding */
    .fi-sidebar-header {
        padding-top: 0.625rem !important;
        padding-bottom: 0.625rem !important;
        min-height: 3.5rem !important;
        align-items: center !important;
    }

    /* --------------------------------------------------------------------------
       2. Strict Icon & SVG Sizing Protection (Never shrink buttons or wrappers)
       -------------------------------------------------------------------------- */
    /* Universal SVG containment */
    .fi-body svg,
    .fi-panel-portal svg,
    .fi-panel-administratorPanel svg {
        max-width: 100%;
        box-sizing: border-box;
    }

    /* Default icon sizing for all standard Filament icons (ONLY target SVG elements) */
    svg.fi-icon:not(.fi-loading-indicator):not([class*="loading-indicator"]),
    .fi-icon:not(.fi-loading-indicator) > svg:not(.fi-loading-indicator),
    .fi-sidebar-item-icon > svg:not(.fi-loading-indicator),
    svg.fi-sidebar-item-icon:not(.fi-loading-indicator),
    .fi-sidebar-nav svg:not(.fi-loading-indicator),
    .fi-sidebar-header svg:not(.fi-loading-indicator),
    .fi-btn svg:not(.fi-loading-indicator):not([class*="loading-indicator"]),
    .fi-btn-icon:not(.fi-loading-indicator):not([class*="loading-indicator"]),
    svg.fi-btn-icon:not(.fi-loading-indicator):not([class*="loading-indicator"]),
    .fi-icon-btn > svg:not(.fi-loading-indicator):not([class*="loading-indicator"]),
    svg.fi-icon-btn:not(.fi-loading-indicator):not([class*="loading-indicator"]),
    .fi-ta-cell svg:not(.fi-loading-indicator):not([class*="loading-indicator"]),
    .fi-ta-icon > svg:not(.fi-loading-indicator):not([class*="loading-indicator"]),
    .fi-dropdown-list-item-icon > svg:not(.fi-loading-indicator),
    .fi-modal-icon > svg:not(.fi-loading-indicator),
    .fi-section svg:not(.fi-loading-indicator) {
        width: 1.25rem !important;
        height: 1.25rem !important;
        max-width: 1.25rem !important;
        max-height: 1.25rem !important;
        min-width: 1.25rem !important;
        min-height: 1.25rem !important;
        flex-shrink: 0 !important;
        display: inline-block;
        vertical-align: middle !important;
    }

    /* Specific Filament Size Enums (ONLY on SVG elements) */
    svg.fi-size-xs:not(.fi-loading-indicator),
    .fi-icon.fi-size-xs > svg:not(.fi-loading-indicator),
    svg.fi-icon.fi-size-xs:not(.fi-loading-indicator) {
        width: 0.75rem !important; height: 0.75rem !important;
        max-width: 0.75rem !important; max-height: 0.75rem !important;
        min-width: 0.75rem !important; min-height: 0.75rem !important;
    }

    svg.fi-size-sm:not(.fi-loading-indicator),
    .fi-icon.fi-size-sm > svg:not(.fi-loading-indicator),
    svg.fi-icon.fi-size-sm:not(.fi-loading-indicator) {
        width: 1rem !important; height: 1rem !important;
        max-width: 1rem !important; max-height: 1rem !important;
        min-width: 1rem !important; min-height: 1rem !important;
    }

    svg.fi-size-md:not(.fi-loading-indicator),
    .fi-icon.fi-size-md > svg:not(.fi-loading-indicator),
    svg.fi-icon.fi-size-md:not(.fi-loading-indicator) {
        width: 1.25rem !important; height: 1.25rem !important;
        max-width: 1.25rem !important; max-height: 1.25rem !important;
        min-width: 1.25rem !important; min-height: 1.25rem !important;
    }

    svg.fi-size-lg:not(.fi-loading-indicator),
    .fi-icon.fi-size-lg > svg:not(.fi-loading-indicator),
    svg.fi-icon.fi-size-lg:not(.fi-loading-indicator) {
        width: 1.35rem !important; height: 1.35rem !important;
        max-width: 1.35rem !important; max-height: 1.35rem !important;
        min-width: 1.35rem !important; min-height: 1.35rem !important;
    }

    svg.fi-size-xl:not(.fi-loading-indicator),
    .fi-icon.fi-size-xl > svg:not(.fi-loading-indicator),
    svg.fi-icon.fi-size-xl:not(.fi-loading-indicator) {
        width: 1.5rem !important; height: 1.5rem !important;
        max-width: 1.5rem !important; max-height: 1.5rem !important;
        min-width: 1.5rem !important; min-height: 1.5rem !important;
    }

    svg.fi-size-2xl:not(.fi-loading-indicator),
    .fi-icon.fi-size-2xl > svg:not(.fi-loading-indicator),
    svg.fi-icon.fi-size-2xl:not(.fi-loading-indicator) {
        width: 2rem !important; height: 2rem !important;
        max-width: 2rem !important; max-height: 2rem !important;
        min-width: 2rem !important; min-height: 2rem !important;
    }

    /* Table Cell Schedule Column Icons */
    .fi-ta-cell .fi-icon > svg:not(.fi-loading-indicator),
    .fi-ta-cell svg.fi-icon:not(.fi-loading-indicator) {
        width: 1.125rem !important;
        height: 1.125rem !important;
        max-width: 1.125rem !important;
        max-height: 1.125rem !important;
        min-width: 1.125rem !important;
        min-height: 1.125rem !important;
    }

    /* Empty State Table Icon */
    .fi-ta-empty-state-icon-bg svg,
    .fi-ta-empty-state svg {
        width: 2.25rem !important;
        height: 2.25rem !important;
        max-width: 2.25rem !important;
        max-height: 2.25rem !important;
        min-width: 2.25rem !important;
        min-height: 2.25rem !important;
    }

    /* Badges, Sorting, Pagination, and Breadcrumb Icons */
    .fi-badge > svg:not(.fi-loading-indicator),
    .fi-badge-icon > svg:not(.fi-loading-indicator),
    svg.fi-badge-icon:not(.fi-loading-indicator),
    .fi-ta-header-cell-sort-icon > svg:not(.fi-loading-indicator),
    svg.fi-ta-header-cell-sort-icon:not(.fi-loading-indicator),
    .fi-breadcrumbs svg,
    .fi-pagination-item svg {
        width: 0.875rem !important;
        height: 0.875rem !important;
        max-width: 0.875rem !important;
        max-height: 0.875rem !important;
        min-width: 0.875rem !important;
        min-height: 0.875rem !important;
    }

    /* Form Input Prefix/Suffix Icons & Table Filter/Search */
    .fi-input-wrp-icon > svg:not(.fi-loading-indicator),
    svg.fi-input-wrp-icon:not(.fi-loading-indicator),
    .fi-ta-filter svg:not(.fi-loading-indicator),
    .fi-ta-search-field svg:not(.fi-loading-indicator) {
        width: 1.125rem !important;
        height: 1.125rem !important;
        max-width: 1.125rem !important;
        max-height: 1.125rem !important;
        min-width: 1.125rem !important;
        min-height: 1.125rem !important;
    }

    /* Tailwind utility fallbacks for SVGs */
    svg.w-3 { width: 0.75rem !important; height: 0.75rem !important; }
    svg.w-4 { width: 1rem !important; height: 1rem !important; }
    svg.w-5 { width: 1.25rem !important; height: 1.25rem !important; }
    svg.w-6 { width: 1.5rem !important; height: 1.5rem !important; }
    svg.w-7 { width: 1.75rem !important; height: 1.75rem !important; }
    svg.w-8 { width: 2rem !important; height: 2rem !important; }

    /* --------------------------------------------------------------------------
       3. Table Component Layout Integrity (Protect Buttons, Badges & Actions)
       -------------------------------------------------------------------------- */
    /* Ensure action buttons and links have natural width, padding, and never overlap */
    .fi-btn {
        width: auto !important;
        max-width: none !important;
        min-height: 2.25rem !important;
        white-space: nowrap !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 0.375rem !important;
    }

    .fi-badge {
        width: auto !important;
        max-width: none !important;
        white-space: nowrap !important;
        display: inline-flex !important;
        align-items: center !important;
    }

    /* Table record actions (Manage Schedule, Logs) cleanly side-by-side */
    .fi-ta-actions {
        display: inline-flex !important;
        align-items: center !important;
        gap: 0.75rem !important;
        flex-wrap: nowrap !important;
        white-space: nowrap !important;
    }

    .fi-ta-actions .fi-link,
    .fi-ta-actions .fi-btn {
        display: inline-flex !important;
        align-items: center !important;
        gap: 0.25rem !important;
        white-space: nowrap !important;
    }

    /* Table header toolbar actions */
    .fi-ta-header-actions-ctn {
        display: flex !important;
        align-items: center !important;
        gap: 0.5rem !important;
        flex-wrap: wrap !important;
    }

    .fi-ta-header-cell {
        font-size: 0.7rem !important;
        letter-spacing: 0.03em !important;
        font-weight: 600 !important;
        white-space: nowrap !important;
    }

    .fi-ta-cell {
        font-size: 0.8125rem !important;
    }

    /* --------------------------------------------------------------------------
       4. Header / Topbar Clean-up: Remove Toggle Buttons & Topbar Global Search
       -------------------------------------------------------------------------- */
    .fi-topbar-open-sidebar-btn,
    .fi-topbar-close-sidebar-btn,
    .fi-topbar-open-collapse-sidebar-btn,
    .fi-topbar-close-collapse-sidebar-btn,
    .fi-topbar-start > .fi-icon-btn,
    .fi-topbar-start > button,
    .fi-topbar > .fi-icon-btn,
    .fi-global-search,
    .fi-global-search-field,
    .fi-topbar .fi-global-search,
    .fi-topbar-end .fi-global-search {
        display: none !important;
    }

    /* --------------------------------------------------------------------------
       5. Loading Indicator: Hide When Idle, Display Only When Button/Action Clicked
       -------------------------------------------------------------------------- */
    /* By default (idle state), all loading indicators are strictly hidden */
    .fi-loading-indicator,
    svg.fi-loading-indicator,
    .fi-icon.fi-loading-indicator,
    .fi-btn .fi-loading-indicator,
    .fi-link .fi-loading-indicator,
    .fi-input-wrp .fi-loading-indicator,
    .fi-input-wrp-prefix .fi-loading-indicator,
    .fi-input-wrp-suffix .fi-loading-indicator {
        display: none;
        width: 1.125rem;
        height: 1.125rem;
        max-width: 1.125rem;
        max-height: 1.125rem;
        min-width: 1.125rem;
        min-height: 1.125rem;
        vertical-align: middle;
        flex-shrink: 0;
        animation: fi-spin 1s linear infinite;
    }

    /* Livewire active loading state: when an action/button is clicked, Livewire sets inline display */
    .fi-loading-indicator[style*="display: inline"],
    .fi-loading-indicator[style*="display:inline"],
    .fi-loading-indicator[style*="display: block"],
    .fi-loading-indicator[style*="display:block"],
    .fi-loading-indicator[style*="display: flex"],
    .fi-loading-indicator[style*="display:flex"],
    svg.fi-loading-indicator[style*="display: inline"],
    svg.fi-loading-indicator[style*="display:inline"],
    svg.fi-loading-indicator[style*="display: block"],
    svg.fi-loading-indicator[style*="display:block"],
    svg.fi-loading-indicator[style*="display: flex"],
    svg.fi-loading-indicator[style*="display:flex"] {
        display: inline-block !important;
        width: 1.125rem !important;
        height: 1.125rem !important;
        max-width: 1.125rem !important;
        max-height: 1.125rem !important;
        min-width: 1.125rem !important;
        min-height: 1.125rem !important;
        vertical-align: middle !important;
        animation: fi-spin 1s linear infinite !important;
        opacity: 1 !important;
        visibility: visible !important;
    }

    /* When input prefix container is actively displayed by Livewire during request */
    .fi-input-wrp-prefix[style*="display: flex"] .fi-loading-indicator,
    .fi-input-wrp-prefix[style*="display:flex"] .fi-loading-indicator,
    .fi-input-wrp-prefix[style*="display: inline"] .fi-loading-indicator,
    .fi-input-wrp-prefix[style*="display:inline"] .fi-loading-indicator {
        display: inline-block !important;
        width: 1.125rem !important;
        height: 1.125rem !important;
        max-width: 1.125rem !important;
        max-height: 1.125rem !important;
        animation: fi-spin 1s linear infinite !important;
    }

    /* Filament form processing active state (when submit button is clicked) */
    .fi-processing .fi-loading-indicator,
    .fi-btn.fi-processing .fi-loading-indicator,
    .fi-btn.fi-processing svg.fi-loading-indicator {
        display: inline-block !important;
        width: 1.125rem !important;
        height: 1.125rem !important;
        max-width: 1.125rem !important;
        max-height: 1.125rem !important;
        animation: fi-spin 1s linear infinite !important;
    }

    @keyframes fi-spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }

    /* --------------------------------------------------------------------------
       6. Section Header Layout Protection
       -------------------------------------------------------------------------- */
    .fi-section-header {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 0.75rem !important;
    }

    .fi-section-header-text-ctn {
        flex: 1 1 auto !important;
        min-width: 0 !important;
    }

    .fi-section-header-heading {
        white-space: nowrap !important;
    }

    .fi-section-header-after-ctn {
        margin-inline-start: auto !important;
        display: inline-flex !important;
        align-items: center !important;
        flex-shrink: 0 !important;
    }

    /* --------------------------------------------------------------------------
       7. Schedule Calendar (FullCalendar) Theme & Scratch Styles
       -------------------------------------------------------------------------- */
    .zcmc-calendar-card {
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.75rem !important;
        padding: 1.25rem !important;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
    }

    .dark .zcmc-calendar-card {
        background-color: #0f172a !important;
        border-color: #1e293b !important;
    }

    .fc-theme-standard .fc-toolbar.fc-header-toolbar {
        margin-bottom: 1.25rem !important;
        gap: 0.75rem !important;
        flex-wrap: wrap !important;
    }

    .fc-theme-standard .fc-toolbar-title {
        font-size: 1.125rem !important;
        font-weight: 700 !important;
        color: #0f172a !important;
    }

    .dark .fc-theme-standard .fc-toolbar-title {
        color: #f8fafc !important;
    }

    .fc-theme-standard .fc-button {
        background-color: #f1f5f9 !important;
        border-color: #cbd5e1 !important;
        color: #334155 !important;
        font-weight: 600 !important;
        font-size: 0.8125rem !important;
        border-radius: 0.5rem !important;
        padding: 0.35rem 0.75rem !important;
        box-shadow: none !important;
        transition: all 0.15s ease-in-out !important;
    }

    .dark .fc-theme-standard .fc-button {
        background-color: #1e293b !important;
        border-color: #334155 !important;
        color: #cbd5e1 !important;
    }

    .fc-theme-standard .fc-button-primary:hover,
    .fc-theme-standard .fc-button-primary:focus {
        background-color: #e2e8f0 !important;
        border-color: #94a3b8 !important;
        color: #0f172a !important;
    }

    .dark .fc-theme-standard .fc-button-primary:hover {
        background-color: #334155 !important;
        border-color: #475569 !important;
        color: #ffffff !important;
    }

    .fc-theme-standard .fc-button-active,
    .fc-theme-standard .fc-button-primary.fc-button-active {
        background-color: #2563eb !important;
        border-color: #1d4ed8 !important;
        color: #ffffff !important;
    }

    .fc-theme-standard td, 
    .fc-theme-standard th {
        border-color: #e2e8f0 !important;
    }

    .dark .fc-theme-standard td, 
    .dark .fc-theme-standard th {
        border-color: #1e293b !important;
    }

    .fc-theme-standard .fc-col-header-cell {
        background-color: #f8fafc !important;
        padding: 0.5rem 0 !important;
        font-size: 0.75rem !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        color: #64748b !important;
    }

    .dark .fc-theme-standard .fc-col-header-cell {
        background-color: #0f172a !important;
        color: #94a3b8 !important;
    }

    .fc-theme-standard .fc-daygrid-day.fc-day-today {
        background-color: rgba(59, 130, 246, 0.05) !important;
    }

    .dark .fc-theme-standard .fc-daygrid-day.fc-day-today {
        background-color: rgba(59, 130, 246, 0.12) !important;
    }

    /* --------------------------------------------------------------------------
       Interactive Target Day Highlighting on Drag & Drop
       -------------------------------------------------------------------------- */
    /* FullCalendar native drop target highlighting */
    .fc-theme-standard .fc-highlight {
        background-color: rgba(37, 99, 235, 0.18) !important;
        border: 2px dashed #2563eb !important;
        border-radius: 0.375rem !important;
        box-shadow: inset 0 0 12px rgba(37, 99, 235, 0.25) !important;
    }

    .dark .fc-theme-standard .fc-highlight {
        background-color: rgba(59, 130, 246, 0.25) !important;
        border: 2px dashed #60a5fa !important;
        box-shadow: inset 0 0 14px rgba(59, 130, 246, 0.35) !important;
    }

    /* Custom dragover indicator for active hovered day cell */
    .fc-daygrid-day.fc-drag-target-day,
    .fc-timegrid-col.fc-drag-target-day,
    .fc-timegrid-slot.fc-drag-target-day {
        background-color: rgba(37, 99, 235, 0.15) !important;
        outline: 2px dashed #2563eb !important;
        outline-offset: -2px !important;
        transition: background-color 0.05s ease !important;
    }

    .dark .fc-daygrid-day.fc-drag-target-day,
    .dark .fc-timegrid-col.fc-drag-target-day,
    .dark .fc-timegrid-slot.fc-drag-target-day {
        background-color: rgba(59, 130, 246, 0.22) !important;
        outline: 2px dashed #60a5fa !important;
        outline-offset: -2px !important;
    }


    .fc-theme-standard .fc-daygrid-day-number {
        font-size: 0.8125rem !important;
        font-weight: 600 !important;
        color: #475569 !important;
        padding: 0.35rem 0.5rem !important;
    }

    .dark .fc-theme-standard .fc-daygrid-day-number {
        color: #94a3b8 !important;
    }

    .fc-event {
        border-radius: 0.375rem !important;
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        padding: 0.125rem 0.35rem !important;
        margin-bottom: 0.2rem !important;
        cursor: grab !important;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08) !important;
        transition: transform 0.1s ease, box-shadow 0.1s ease !important;
        position: relative !important;
    }

    .fc-event:hover {
        transform: translateY(-1px) !important;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.15) !important;
    }

    /* Event Dragging and Mirror Visibility - Clearly show plotted card being dragged */
    .fc-event.fc-event-dragging,
    .fc-event-dragging,
    .fc-daygrid-event.fc-event-dragging {
        opacity: 0.9 !important;
        transform: scale(1.02) !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.2) !important;
        z-index: 9999 !important;
        cursor: grabbing !important;
        visibility: visible !important;
        display: block !important;
    }

    .fc-event.fc-event-mirror,
    .fc-event-mirror,
    .fc-daygrid-event.fc-event-mirror {
        opacity: 0.95 !important;
        box-shadow: 0 12px 28px -5px rgba(37, 99, 235, 0.4), 0 8px 10px -6px rgba(0, 0, 0, 0.25) !important;
        z-index: 99999 !important;
        transform: scale(1.02) !important;
        border: 2px solid #ffffff !important;
        outline: 2px solid #2563eb !important;
        visibility: visible !important;
        display: block !important;
    }

    /* External preset card dragging clone */
    .fc-event-dragging.fc-shift-item,
    body > .fc-shift-item {
        z-index: 99999 !important;
        box-shadow: 0 12px 28px -5px rgba(0, 0, 0, 0.35) !important;
        transform: scale(1.04) !important;
        opacity: 0.92 !important;
        pointer-events: none !important;
    }

    /* Event Resizer Handles - Bidirectional arrow cursor <-> on extending */
    .fc-event-resizer,
    .fc-event-resizer-end,
    .fc-event-resizer-start,
    .fc-daygrid-event-resizer,
    .fc-daygrid-event-resizer-end,
    .fc-daygrid-event-resizer-start {
        cursor: ew-resize !important;
        cursor: col-resize !important;
        display: block !important;
        opacity: 0.85 !important;
        z-index: 50 !important;
        transition: all 0.15s ease !important;
    }

    .fc-event:hover .fc-event-resizer,
    .fc-event-resizer:hover {
        cursor: ew-resize !important;
        cursor: col-resize !important;
        opacity: 1 !important;
    }

    /* Horizontal resizers (DayGrid Month View) with bidirectional handle arrow */
    .fc-event-resizer-end,
    .fc-daygrid-event-resizer-end {
        cursor: ew-resize !important;
        cursor: col-resize !important;
        right: -2px !important;
        top: 0 !important;
        bottom: 0 !important;
        width: 16px !important;
        border-top-right-radius: 0.375rem !important;
        border-bottom-right-radius: 0.375rem !important;
        background: rgba(255, 255, 255, 0.5) !important;
        border-left: 2px solid rgba(0, 0, 0, 0.35) !important;
    }

    .fc-event:hover .fc-event-resizer-end,
    .fc-event:hover .fc-daygrid-event-resizer-end,
    .fc-event-resizer-end:hover {
        cursor: ew-resize !important;
        cursor: col-resize !important;
        background: rgba(255, 255, 255, 0.85) !important;
        border-left: 2px solid #2563eb !important;
    }

    .fc-event-resizer-start,
    .fc-daygrid-event-resizer-start {
        cursor: ew-resize !important;
        cursor: col-resize !important;
        left: -2px !important;
        top: 0 !important;
        bottom: 0 !important;
        width: 16px !important;
        border-top-left-radius: 0.375rem !important;
        border-bottom-left-radius: 0.375rem !important;
        background: rgba(255, 255, 255, 0.5) !important;
        border-right: 2px solid rgba(0, 0, 0, 0.35) !important;
    }

    .fc-event:hover .fc-event-resizer-start,
    .fc-event:hover .fc-daygrid-event-resizer-start,
    .fc-event-resizer-start:hover {
        cursor: ew-resize !important;
        cursor: col-resize !important;
        background: rgba(255, 255, 255, 0.85) !important;
        border-right: 2px solid #2563eb !important;
    }

    /* Vertical resizers (TimeGrid Week / Day View) */
    .fc-timegrid-event-resizer-bottom {
        cursor: ns-resize !important;
        left: 0 !important;
        right: 0 !important;
        bottom: -2px !important;
        height: 12px !important;
        border-bottom-left-radius: 0.375rem !important;
        border-bottom-right-radius: 0.375rem !important;
        background: rgba(255, 255, 255, 0.5) !important;
        border-top: 2px solid rgba(0, 0, 0, 0.25) !important;
    }

    .fc-timegrid-event-resizer-top {
        cursor: ns-resize !important;
        left: 0 !important;
        right: 0 !important;
        top: -2px !important;
        height: 12px !important;
        border-top-left-radius: 0.375rem !important;
        border-top-right-radius: 0.375rem !important;
        background: rgba(255, 255, 255, 0.5) !important;
        border-bottom: 2px solid rgba(0, 0, 0, 0.25) !important;
    }

    .fc-shift-item {
        transition: transform 0.1s ease, box-shadow 0.1s ease !important;
        touch-action: none !important;
    }

    .fc-shift-item:hover {
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 10px -1px rgba(0, 0, 0, 0.1) !important;
    }

    /* --------------------------------------------------------------------------
       8. Scratch Shifting Preset Badges (Compact Calendar Event Style)
       -------------------------------------------------------------------------- */
    .zcmc-shift-card {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        padding: 0.25rem 0.5rem !important;
        border-radius: 0.375rem !important;
        border-width: 1px !important;
        border-style: solid !important;
        cursor: grab !important;
        user-select: none !important;
        margin-bottom: 0.35rem !important;
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        line-height: 1.25 !important;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
        transition: transform 0.08s ease !important;
    }

    .zcmc-shift-card:active {
        cursor: grabbing !important;
        transform: scale(0.99) !important;
    }

    /* Morning Shift Card (Sky / Cyan Blue) */
    .zcmc-shift-morning {
        background: #0284c7 !important;
        border-color: #0369a1 !important;
        color: #ffffff !important;
    }

    /* Afternoon Shift Card (Warm Amber / Orange) */
    .zcmc-shift-afternoon {
        background: #d97706 !important;
        border-color: #b45309 !important;
        color: #ffffff !important;
    }

    /* Night Shift Card (Deep Indigo / Violet) */
    .zcmc-shift-night {
        background: #4f46e5 !important;
        border-color: #4338ca !important;
        color: #ffffff !important;
    }

    /* Regular Office Hours Card (Emerald Green) */
    .zcmc-shift-office {
        background: #059669 !important;
        border-color: #047857 !important;
        color: #ffffff !important;
    }

    /* Day Shift Card (Teal) */
    .zcmc-shift-day {
        background: #0d9488 !important;
        border-color: #0f766e !important;
        color: #ffffff !important;
    }

    /* 24-Hour Duty Shift Card (Rose / Coral) */
    .zcmc-shift-24hr {
        background: #e11d48 !important;
        border-color: #be123c !important;
        color: #ffffff !important;
    }

    /* 8 - 4 Grid System Classes */
    .zcmc-grid-layout {
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 1.5rem !important;
    }

    @media (min-width: 1024px) {
        .zcmc-grid-layout {
            grid-template-columns: repeat(12, minmax(0, 1fr)) !important;
        }
        .zcmc-col-8 {
            grid-column: span 8 / span 8 !important;
        }
        .zcmc-col-4 {
            grid-column: span 4 / span 4 !important;
        }
    }

    /* Constrain ALL icons inside calendar cards strictly to avoid blowup */
    .zcmc-calendar-card svg,
    .zcmc-calendar-card .zcmc-icon {
        width: 0.875rem !important;
        height: 0.875rem !important;
        max-width: 0.875rem !important;
        max-height: 0.875rem !important;
        min-width: 0.875rem !important;
        min-height: 0.875rem !important;
        flex-shrink: 0 !important;
        display: inline-block !important;
    }

    /* Clear All Button on Calendar Toolbar */
    .fc .fc-clearAllBtn-button {
        background-color: #fee2e2 !important;
        border-color: #fca5a5 !important;
        color: #b91c1c !important;
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        padding: 0.35rem 0.65rem !important;
        border-radius: 0.375rem !important;
        transition: all 0.15s ease !important;
        margin-left: 0.5rem !important;
    }

    .fc .fc-clearAllBtn-button:hover {
        background-color: #ef4444 !important;
        border-color: #dc2626 !important;
        color: #ffffff !important;
    }

    /* ==========================================================================
       Header Profile Banner & Tab Switcher (Scratch GUI)
       ========================================================================== */
    .zcmc-profile-banner {
        display: flex !important;
        flex-direction: column !important;
        align-items: stretch !important;
        justify-content: space-between !important;
        gap: 1rem !important;
        padding: 1rem 1.25rem !important;
        border-radius: 0.875rem !important;
    }

    @media (min-width: 768px) {
        .zcmc-profile-banner {
            flex-direction: row !important;
            align-items: center !important;
        }
    }

    .zcmc-tab-switcher {
        display: inline-flex !important;
        align-items: center !important;
        padding: 0.25rem !important;
        border-radius: 0.75rem !important;
        background-color: #f1f5f9 !important;
        border: 1px solid #e2e8f0 !important;
        user-select: none !important;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.05) !important;
    }

    .dark .zcmc-tab-switcher {
        background-color: rgba(30, 41, 59, 0.8) !important;
        border-color: #334155 !important;
    }

    .zcmc-tab-btn {
        display: inline-flex !important;
        align-items: center !important;
        gap: 0.5rem !important;
        padding: 0.45rem 0.875rem !important;
        font-size: 0.75rem !important;
        font-weight: 700 !important;
        line-height: 1 !important;
        border-radius: 0.5rem !important;
        color: #64748b !important;
        background: transparent !important;
        border: none !important;
        cursor: pointer !important;
        transition: all 0.15s ease !important;
    }

    .zcmc-tab-btn:hover {
        color: #0f172a !important;
    }

    .dark .zcmc-tab-btn {
        color: #94a3b8 !important;
    }

    .dark .zcmc-tab-btn:hover {
        color: #f8fafc !important;
    }

    .zcmc-tab-btn-active {
        background-color: #ffffff !important;
        color: #2563eb !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08), 0 1px 2px rgba(0, 0, 0, 0.04) !important;
    }

    .dark .zcmc-tab-btn-active {
        background-color: #0f172a !important;
        color: #60a5fa !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3) !important;
    }
</style>

