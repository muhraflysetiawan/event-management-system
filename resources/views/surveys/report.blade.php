@extends('layouts.app')
@section('title', 'Survey Report - ' . $event->title)

@push('styles')
<style>
    .report-card {
        border: none;
        box-shadow: var(--shadow-lg);
        border-radius: var(--radius-lg);
        overflow: hidden;
    }
    .report-hero {
        padding: 3rem 2rem;
        background: linear-gradient(135deg, #980517, #473f3d);
        color: #fff;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    .report-hero::before {
        content: '';
        position: absolute;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.06);
        border-radius: 50%;
        top: -60px;
        right: -60px;
    }
    .report-hero::after {
        content: '';
        position: absolute;
        width: 120px;
        height: 120px;
        background: rgba(255,255,255,0.04);
        border-radius: 50%;
        bottom: -30px;
        left: 10%;
    }
    .report-hero-title {
        font-size: 1.75rem;
        font-weight: 800;
        letter-spacing: -0.01em;
        margin-bottom: 0.4rem;
        position: relative;
        z-index: 1;
    }
    .report-hero-subtitle {
        font-size: 1.1rem;
        opacity: 0.9;
        font-weight: 500;
        position: relative;
        z-index: 1;
    }
    .report-conclusion-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1.5rem;
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.3);
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.9rem;
        margin-top: 1.25rem;
        backdrop-filter: blur(4px);
        position: relative;
        z-index: 1;
    }
    .report-body {
        padding: 2.5rem;
    }
    @media (max-width: 768px) {
        .report-body { padding: 1.25rem; }
        .report-hero { padding: 2rem 1.25rem; }
        .report-hero-title { font-size: 1.35rem; }
    }
    /* Score overview */
    .score-overview {
        display: flex;
        justify-content: center;
        margin-bottom: 3rem;
    }
    .score-box {
        text-align: center;
        background: #fafafa;
        padding: 2rem 3.5rem;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border-color);
    }
    .score-number {
        font-size: 4rem;
        font-weight: 900;
        color: #980517;
        line-height: 1;
    }
    .score-max {
        font-size: 1.25rem;
        color: var(--text-muted);
        font-weight: 600;
    }
    .score-label {
        margin-top: 0.5rem;
        color: var(--text-secondary);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-size: 0.8rem;
    }
    /* Performance table */
    .section-title {
        font-size: 1.1rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 0.6rem;
        margin-bottom: 1.25rem;
        color: var(--text-primary);
    }
    .section-title i {
        color: #980517;
    }
    .perf-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 0.5rem;
    }
    .perf-table thead th {
        background: transparent;
        border: none;
        padding: 0 1rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--text-muted);
    }
    .perf-table tbody tr {
        background: #fdfdfd;
        border-radius: var(--radius-sm);
    }
    .perf-table tbody td {
        padding: 1.1rem 1rem;
        border: 1px solid var(--border-color);
        border-left: none;
        border-right: none;
    }
    .perf-table tbody td:first-child {
        border-left: 1px solid var(--border-color);
        border-radius: var(--radius-sm) 0 0 var(--radius-sm);
        font-weight: 600;
        color: var(--text-primary);
    }
    .perf-table tbody td:last-child {
        border-right: 1px solid var(--border-color);
        border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
    }
    .perf-score {
        font-weight: 800;
        font-size: 1.15rem;
        text-align: center;
    }
    .perf-score.excellent { color: #10b981; }
    .perf-score.good { color: #f59e0b; }
    .perf-score.poor { color: #ef4444; }
    .perf-status {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-weight: 700;
        font-size: 0.8rem;
    }
    .perf-status.excellent { color: #10b981; }
    .perf-status.good { color: #f59e0b; }
    .perf-status.poor { color: #ef4444; }
    /* Insight sections */
    .insight-warning {
        background: #fffafa;
        border: 1px solid #fee2e2;
        border-radius: var(--radius-lg);
        padding: 2rem;
        position: relative;
        overflow: hidden;
        margin-bottom: 2rem;
    }
    .insight-warning-icon {
        position: absolute;
        top: -10px;
        right: -10px;
        font-size: 4rem;
        color: #ef4444;
        opacity: 0.05;
    }
    .insight-warning-title {
        color: #991b1b;
        font-size: 1.1rem;
        font-weight: 800;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }
    .insight-warning-desc {
        color: #7f1d1d;
        margin-bottom: 1.5rem;
        line-height: 1.6;
        font-size: 0.9rem;
    }
    .insight-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 0.75rem;
    }
    .insight-item {
        padding: 1.1rem;
        background: #fff;
        border-radius: var(--radius-md);
        border: 1px solid #fecaca;
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
    }
    .insight-item-question {
        font-size: 0.8rem;
        font-weight: 700;
        color: #991b1b;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }
    .insight-item-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .insight-item-label {
        font-size: 0.8rem;
        color: var(--text-secondary);
    }
    .insight-item-score {
        font-weight: 800;
        color: #ef4444;
        font-size: 1.05rem;
    }
    .insight-success {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: var(--radius-lg);
        padding: 2rem;
        text-align: center;
        margin-bottom: 2rem;
    }
    .insight-success-icon {
        font-size: 2.5rem;
        color: #10b981;
        margin-bottom: 0.75rem;
    }
    .insight-success-title {
        color: #166534;
        font-size: 1.15rem;
        font-weight: 800;
        margin-bottom: 0.3rem;
    }
    .insight-success-desc {
        color: #14532d;
        font-size: 0.9rem;
    }
    .report-actions {
        display: flex;
        gap: 0.75rem;
        justify-content: center;
        margin-top: 2rem;
    }
    @media print {
        .no-print { display: none !important; }
        body { background: white !important; }
        .card, .report-card { border: none !important; box-shadow: none !important; }
        .report-hero { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
</style>
@endpush

@section('content')
<div class="card report-card mb-4" id="report-content">
    <div class="report-hero">
        <div class="report-hero-title">EVENT SATISFACTION REPORT</div>
        <div class="report-hero-subtitle">{{ $event->title }}</div>
        <div class="report-conclusion-badge">
            <i class="fas fa-chart-line" style="color: inherit;"></i> Overall Conclusion: {{ $conclusion }}
        </div>
    </div>
    <div class="report-body">
        {{-- Score Overview --}}
        <div class="score-overview">
            <div class="score-box">
                <div class="score-number">{{ round($finalScore, 1) }} <span class="score-max">/ 5.0</span></div>
                <div class="score-label">Aggregate Satisfaction Score</div>
            </div>
        </div>

        {{-- Performance Breakdown --}}
        <div style="margin-bottom: 3rem;">
            <div class="section-title">
                <i class="fas fa-tasks"></i> Performance Breakdown
            </div>
            <div class="table-container">
                <table class="perf-table">
                    <thead>
                        <tr>
                            <th>Metric / Question</th>
                            <th style="text-align: center;">Average Score</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $data)
                            @php
                                $tier = $data['average'] >= 4 ? 'excellent' : ($data['average'] >= 3.5 ? 'good' : 'poor');
                                $statusText = $tier === 'excellent' ? 'EXCELLENT' : ($tier === 'good' ? 'GOOD' : 'UNSATISFACTORY');
                                $statusIcon = $tier === 'excellent' ? 'fa-check-circle' : ($tier === 'good' ? 'fa-info-circle' : 'fa-exclamation-circle');
                            @endphp
                            <tr>
                                <td>{{ $data['question'] }}</td>
                                <td class="perf-score {{ $tier }}">{{ $data['average'] }}</td>
                                <td>
                                    <span class="perf-status {{ $tier }}">
                                        <i class="fas {{ $statusIcon }}" style="color: inherit;"></i> {{ $statusText }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Insight Section --}}
        @if(count($unsatisfactory) > 0)
            <div class="insight-warning">
                <div class="insight-warning-icon"><i class="fas fa-lightbulb" style="color: inherit;"></i></div>
                <div class="insight-warning-title">
                    <i class="fas fa-exclamation-triangle" style="color: inherit;"></i> Areas for Improvement
                </div>
                <p class="insight-warning-desc">The following metrics were identified as <strong>unsatisfactory</strong> (score below 3.5) and require strategic attention for future events:</p>
                <div class="insight-grid">
                    @foreach($unsatisfactory as $item)
                        <div class="insight-item">
                            <div class="insight-item-question">{{ $item['question'] }}</div>
                            <div class="insight-item-row">
                                <span class="insight-item-label">Avg. Satisfaction Score</span>
                                <span class="insight-item-score">{{ $item['average'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="insight-success">
                <div class="insight-success-icon"><i class="fas fa-check-circle" style="color: inherit;"></i></div>
                <div class="insight-success-title">High Performance Achieved</div>
                <p class="insight-success-desc">All satisfaction metrics are above the satisfactory threshold. Excellent delivery!</p>
            </div>
        @endif

        <div class="report-actions no-print">
            <button onclick="window.print()" class="btn btn-outline" style="padding: 0.65rem 2rem;"><i class="fas fa-print" style="color: inherit;"></i> Print Report</button>
            <a href="{{ route('surveys.results', $event) }}" class="btn btn-secondary" style="padding: 0.65rem 2rem;">Back to Results</a>
        </div>
    </div>
</div>
@endsection
