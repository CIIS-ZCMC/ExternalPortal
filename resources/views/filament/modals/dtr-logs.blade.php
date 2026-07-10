@props(['logs', 'date'])

<div class="dtr-logs-modal">
    @if($logs->isEmpty())
        <div class="dtr-logs-empty">
            <div class="dtr-logs-empty-icon">
                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h8.25m-8.25 3H12M10.5 2.25H9.375c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V8.25c0-.621-.504-1.125-1.125-1.125H10.5a1.125 1.125 0 0 1-1.125-1.125V3.375c0-.621.504-1.125 1.125-1.125Z" />
                </svg>
            </div>
            <p class="dtr-logs-empty-text">No device logs recorded for this date.</p>
        </div>
    @else
        <div class="dtr-logs-summary">
            <div class="dtr-logs-summary-item">
                <span class="dtr-logs-summary-label">Date:</span>
                <span class="dtr-logs-summary-value">{{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</span>
            </div>
            <div class="dtr-logs-summary-item">
                <span class="dtr-logs-summary-label">Total Logs:</span>
                <span class="dtr-logs-summary-value">{{ $logs->count() }}</span>
            </div>
        </div>

        <div class="dtr-logs-table-wrapper">
            <table class="dtr-logs-table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Date &amp; Time</th>
                        <th scope="col">Device</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $index => $log)
                        <tr class="dtr-log-row">
                            <td class="dtr-log-number">{{ $index + 1 }}</td>
                            <td class="dtr-log-datetime">
                                <span class="dtr-log-time-primary">{{ \Carbon\Carbon::parse($log['date_time'])->format('h:i A') }}</span>
                                <span class="dtr-log-time-secondary">{{ \Carbon\Carbon::parse($log['date_time'])->format('M d, Y') }}</span>
                            </td>
                            <td>
                                                <span class="dtr-badge dtr-badge-blue">
                                                    {{ $log['device_name'] ?? 'Unknown' }}
                                                </span>
                            </td>
                            <td>
                                @if(($log['status'] ?? 0) == 255)
                                    <span class="dtr-badge dtr-badge-green">Active</span>
                                @else
                                    <span class="dtr-badge dtr-badge-gray">Inactive</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<style>
    /* --- Base / Light Mode --- */
    .dtr-logs-modal {
        font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        color: #111827;
        padding: 0.5rem 0;
    }

    .dtr-logs-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 3rem 1rem;
        text-align: center;
        border-radius: 0.75rem;
        border: 1px dashed #d1d5db;
        background-color: #f9fafb;
    }

    .dtr-logs-empty-icon {
        width: 3.5rem;
        height: 3.5rem;
        color: #9ca3af;
        margin-bottom: 0.75rem;
    }

    .dtr-logs-empty-icon svg {
        width: 100%;
        height: 100%;
        opacity: 0.6;
    }

    .dtr-logs-empty-text {
        font-size: 0.95rem;
        color: #6b7280;
        margin: 0;
    }

    .dtr-logs-summary {
        display: flex;
        gap: 1.5rem;
        margin-bottom: 1rem;
        padding: 0.75rem 1rem;
        background-color: #f3f4f6;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
    }

    .dtr-logs-summary-item {
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .dtr-logs-summary-label {
        font-size: 0.8rem;
        font-weight: 500;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }

    .dtr-logs-summary-value {
        font-size: 0.9rem;
        font-weight: 600;
        color: #111827;
    }

    .dtr-logs-table-wrapper {
        overflow: hidden;
        border-radius: 0.625rem;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        background-color: #ffffff;
    }

    .dtr-logs-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.9rem;
    }

    .dtr-logs-table thead {
        background-color: #f9fafb;
    }

    .dtr-logs-table thead th {
        position: sticky;
        top: 0;
        z-index: 1;
        padding: 0.85rem 1rem;
        text-align: left;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #4b5563;
        border-bottom: 1px solid #e5e7eb;
    }

    .dtr-logs-table tbody {
        background-color: #ffffff;
    }

    .dtr-logs-table tbody tr {
        border-bottom: 1px solid #f3f4f6;
        transition: background-color 0.15s ease;
    }

    .dtr-logs-table tbody tr:last-child {
        border-bottom: none;
    }

    .dtr-logs-table tbody tr:hover {
        background-color: #f9fafb;
    }

    .dtr-logs-table td {
        padding: 0.9rem 1rem;
        vertical-align: middle;
    }

    .dtr-log-number {
        width: 3rem;
        font-size: 0.8rem;
        font-weight: 600;
        color: #9ca3af;
    }

    .dtr-log-datetime {
        display: flex;
        flex-direction: column;
        gap: 0.15rem;
    }

    .dtr-log-time-primary {
        font-weight: 600;
        color: #111827;
    }

    .dtr-log-time-secondary {
        font-size: 0.75rem;
        color: #6b7280;
    }

    .dtr-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.65rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
        line-height: 1rem;
        border: 1px solid;
    }

    .dtr-badge-blue {
        background-color: #eff6ff;
        color: #1e40af;
        border-color: #bfdbfe;
    }

    .dtr-badge-green {
        background-color: #f0fdf4;
        color: #166534;
        border-color: #bbf7d0;
    }

    .dtr-badge-gray {
        background-color: #f3f4f6;
        color: #4b5563;
        border-color: #e5e7eb;
    }

    /* --- Dark Mode --- */
    .dark .dtr-logs-modal,
    [data-theme="dark"] .dtr-logs-modal,
    html[class~="dark"] .dtr-logs-modal {
        color: #f9fafb;
    }

    .dark .dtr-logs-empty,
    [data-theme="dark"] .dtr-logs-empty,
    html[class~="dark"] .dtr-logs-empty {
        background-color: #1f2937;
        border-color: #4b5563;
    }

    .dark .dtr-logs-empty-icon,
    [data-theme="dark"] .dtr-logs-empty-icon,
    html[class~="dark"] .dtr-logs-empty-icon {
        color: #9ca3af;
    }

    .dark .dtr-logs-empty-text,
    [data-theme="dark"] .dtr-logs-empty-text,
    html[class~="dark"] .dtr-logs-empty-text {
        color: #d1d5db;
    }

    .dark .dtr-logs-summary,
    [data-theme="dark"] .dtr-logs-summary,
    html[class~="dark"] .dtr-logs-summary {
        background-color: #1f2937;
        border-color: #374151;
    }

    .dark .dtr-logs-summary-label,
    [data-theme="dark"] .dtr-logs-summary-label,
    html[class~="dark"] .dtr-logs-summary-label {
        color: #9ca3af;
    }

    .dark .dtr-logs-summary-value,
    [data-theme="dark"] .dtr-logs-summary-value,
    html[class~="dark"] .dtr-logs-summary-value {
        color: #f9fafb;
    }

    .dark .dtr-logs-table-wrapper,
    [data-theme="dark"] .dtr-logs-table-wrapper,
    html[class~="dark"] .dtr-logs-table-wrapper {
        background-color: #111827;
        border-color: #374151;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
    }

    .dark .dtr-logs-table thead,
    [data-theme="dark"] .dtr-logs-table thead,
    html[class~="dark"] .dtr-logs-table thead {
        background-color: #1f2937;
    }

    .dark .dtr-logs-table thead th,
    [data-theme="dark"] .dtr-logs-table thead th,
    html[class~="dark"] .dtr-logs-table thead th {
        color: #d1d5db;
        border-bottom-color: #374151;
    }

    .dark .dtr-logs-table tbody,
    [data-theme="dark"] .dtr-logs-table tbody,
    html[class~="dark"] .dtr-logs-table tbody {
        background-color: #111827;
    }

    .dark .dtr-logs-table tbody tr,
    [data-theme="dark"] .dtr-logs-table tbody tr,
    html[class~="dark"] .dtr-logs-table tbody tr {
        border-bottom-color: #1f2937;
    }

    .dark .dtr-logs-table tbody tr:hover,
    [data-theme="dark"] .dtr-logs-table tbody tr:hover,
    html[class~="dark"] .dtr-logs-table tbody tr:hover {
        background-color: #1f2937;
    }

    .dark .dtr-log-number,
    [data-theme="dark"] .dtr-log-number,
    html[class~="dark"] .dtr-log-number {
        color: #9ca3af;
    }

    .dark .dtr-log-time-primary,
    [data-theme="dark"] .dtr-log-time-primary,
    html[class~="dark"] .dtr-log-time-primary {
        color: #f9fafb;
    }

    .dark .dtr-log-time-secondary,
    [data-theme="dark"] .dtr-log-time-secondary,
    html[class~="dark"] .dtr-log-time-secondary {
        color: #9ca3af;
    }

    .dark .dtr-badge-blue,
    [data-theme="dark"] .dtr-badge-blue,
    html[class~="dark"] .dtr-badge-blue {
        background-color: rgba(30, 64, 175, 0.25);
        color: #93c5fd;
        border-color: rgba(59, 130, 246, 0.35);
    }

    .dark .dtr-badge-green,
    [data-theme="dark"] .dtr-badge-green,
    html[class~="dark"] .dtr-badge-green {
        background-color: rgba(22, 101, 52, 0.25);
        color: #86efac;
        border-color: rgba(34, 197, 94, 0.35);
    }

    .dark .dtr-badge-gray,
    [data-theme="dark"] .dtr-badge-gray,
    html[class~="dark"] .dtr-badge-gray {
        background-color: rgba(75, 85, 99, 0.35);
        color: #d1d5db;
        border-color: rgba(107, 114, 128, 0.45);
    }
</style>
