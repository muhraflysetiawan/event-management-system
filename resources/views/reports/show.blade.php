@extends('layouts.app')
@section('title', 'Report Details')

@section('content')
<div style="margin-bottom:1.5rem;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;">
    <a href="{{ route('events.show', $report->event) }}" class="link"><i class="fas fa-arrow-left"></i> Back to event</a>
    <div class="action-group">
        <a href="{{ route('reports.exportPdf', $report) }}" class="btn btn-primary btn-sm"><i class="fas fa-file-pdf"></i> Export PDF</a>
        <a href="{{ route('reports.exportExcel', $report) }}" class="btn btn-success btn-sm"><i class="fas fa-file-excel"></i> Export Excel</a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $report->title }}</h3>
    </div>
    <div class="card-body">
        <div class="detail-grid mb-3">
            <div class="detail-item">
                <div class="detail-label">Event</div>
                <div class="detail-value">{{ $report->event->title }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Created By</div>
                <div class="detail-value">{{ $report->creator->name }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Total Participants</div>
                <div class="detail-value">{{ $report->total_participants }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Total Attended</div>
                <div class="detail-value">{{ $report->total_attended }}</div>
            </div>
        </div>

        @if($report->type === 'financial')
        <div style="background:var(--bg-input);border:1px solid var(--border-color);border-radius:var(--radius-sm);padding:1rem;margin-bottom:1.5rem;">
            <div class="detail-label" style="color: var(--primary-400); margin-bottom: 0.5rem;"><i class="fas fa-file-invoice-dollar"></i> Financial Summary</div>
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label">Budget Allocated</div>
                    <div class="detail-value">{{ $report->budget_allocated ? 'Rp ' . number_format($report->budget_allocated, 0, ',', '.') : '-' }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Total Expenses</div>
                    <div class="detail-value">{{ $report->total_expenses ? 'Rp ' . number_format($report->total_expenses, 0, ',', '.') : '-' }}</div>
                </div>
            </div>
            @if($report->financial_notes)
            <div class="mt-3">
                <div class="detail-label">Financial Notes</div>
                <p style="color:var(--text-secondary);margin-top:0.5rem;">{{ $report->financial_notes }}</p>
            </div>
            @endif
        </div>
        @endif

        @if($report->summary)
        <div style="background:var(--bg-input);border:1px solid var(--border-color);border-radius:var(--radius-sm);padding:1rem;margin-bottom:1.5rem;">
            <div class="detail-label">Summary</div>
            <p style="color:var(--text-secondary);margin-top:0.5rem;">{{ $report->summary }}</p>
        </div>
        @endif

        <div>
            <div class="detail-label" style="margin-bottom:0.75rem;">Report Content</div>
            <div style="color:var(--text-secondary);line-height:1.8;white-space:pre-line;">{{ $report->content }}</div>
        </div>
    </div>
</div>
@endsection
