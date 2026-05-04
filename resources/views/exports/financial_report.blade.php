<table>
    <thead>
        <tr>
            <th colspan="2" style="font-weight: bold; font-size: 14px;">FINANCIAL REPORT</th>
        </tr>
        <tr>
            <th style="font-weight: bold;">Event Title</th>
            <td>{{ $event->title }}</td>
        </tr>
        <tr>
            <th style="font-weight: bold;">Report Created By</th>
            <td>{{ $report->creator->name }}</td>
        </tr>
        <tr>
            <th style="font-weight: bold;">Date Created</th>
            <td>{{ $report->created_at->format('d M Y, H:i') }}</td>
        </tr>
        <tr>
            <th colspan="2"></th>
        </tr>
        <tr>
            <th colspan="2" style="font-weight: bold;">FINANCIAL SUMMARY</th>
        </tr>
        <tr>
            <th style="font-weight: bold;">Budget Allocated</th>
            <td>{{ $report->budget_allocated ? 'Rp ' . number_format($report->budget_allocated, 0, ',', '.') : '-' }}</td>
        </tr>
        <tr>
            <th style="font-weight: bold;">Total Expenses</th>
            <td>{{ $report->total_expenses ? 'Rp ' . number_format($report->total_expenses, 0, ',', '.') : '-' }}</td>
        </tr>
        <tr>
            <th style="font-weight: bold;">Surplus / Deficit</th>
            @php
                $diff = ($report->budget_allocated ?? 0) - ($report->total_expenses ?? 0);
            @endphp
            <td style="{{ $diff < 0 ? 'color: red;' : 'color: green;' }}">
                {{ $diff < 0 ? '-' : '' }}Rp {{ number_format(abs($diff), 0, ',', '.') }}
            </td>
        </tr>
        <tr>
            <th colspan="2"></th>
        </tr>
        <tr>
            <th colspan="2" style="font-weight: bold;">FINANCIAL NOTES</th>
        </tr>
        <tr>
            <td colspan="2">{{ $report->financial_notes ?: 'No notes provided.' }}</td>
        </tr>
    </thead>
</table>
