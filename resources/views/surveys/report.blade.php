@extends('layouts.app')
@section('title', 'Survey Report - ' . $event->title)

@section('content')
<div class="card mb-4" id="report-content">
    <div class="card-header text-center" style="padding: 2rem;">
        <h2 class="mb-1">EVENT SATISFACTION REPORT</h2>
        <h4 class="text-muted">{{ $event->title }}</h4>
        <div class="mt-3">
            <span class="badge-status" style="font-size: 1rem; padding: 0.5rem 1rem; background: {{ $conclusion === 'Good' ? '#10b981' : ($conclusion === 'Satisfactory' ? '#f59e0b' : '#ef4444') }}; color: white;">
                Overall Conclusion: {{ $conclusion }}
            </span>
        </div>
    </div>
    <div class="card-body" style="padding: 2rem;">
        <div class="row mb-5">
            <div class="col-md-6 offset-md-3 text-center">
                <div style="font-size: 4rem; font-weight: 800; color: var(--primary-color);">{{ round($finalScore, 1) }} / 5.0</div>
                <p class="text-muted">Aggregate Satisfaction Score</p>
            </div>
        </div>

        <h3 class="mb-4" style="border-bottom: 2px solid var(--border-color); padding-bottom: 0.5rem;">Performance Breakdown</h3>
        <div class="table-container mb-5">
            <table class="table">
                <thead>
                    <tr>
                        <th>Metric / Question</th>
                        <th class="text-center">Average Score</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportData as $data)
                        <tr>
                            <td style="font-weight: 500;">{{ $data['question'] }}</td>
                            <td class="text-center" style="font-weight: 700; font-size: 1.1rem; color: {{ $data['average'] >= 4 ? '#10b981' : ($data['average'] >= 3.5 ? '#f59e0b' : '#ef4444') }};">
                                {{ $data['average'] }}
                            </td>
                            <td>
                                @if($data['average'] >= 4)
                                    <span style="color: #10b981;"><i class="fas fa-smile"></i> Excellent</span>
                                @elseif($data['average'] >= 3.5)
                                    <span style="color: #f59e0b;"><i class="fas fa-meh"></i> Good</span>
                                @else
                                    <span style="color: #ef4444;"><i class="fas fa-frown"></i> Unsatisfactory</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if(count($unsatisfactory) > 0)
            <div class="mb-5 p-4" style="background: rgba(239, 68, 68, 0.05); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: var(--radius-md);">
                <h3 class="mb-3" style="color: #ef4444;"><i class="fas fa-exclamation-triangle"></i> Areas for Improvement</h3>
                <p class="mb-3">The following metrics were identified as <strong>unsatisfactory</strong> (score below 3.5) and require attention for future events:</p>
                <ul style="list-style-type: none; padding-left: 0;">
                    @foreach($unsatisfactory as $item)
                        <li class="mb-2" style="padding: 0.75rem; background: white; border-radius: 4px; border-left: 4px solid #ef4444;">
                            <strong>{{ $item['question'] }}</strong> — Average Score: <span style="color: #ef4444; font-weight: 700;">{{ $item['average'] }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @else
            <div class="mb-5 p-4" style="background: rgba(16, 185, 129, 0.05); border: 1px solid rgba(16, 185, 129, 0.2); border-radius: var(--radius-md);">
                <h3 class="mb-2" style="color: #10b981;"><i class="fas fa-check-circle"></i> High Performance</h3>
                <p>All satisfaction metrics are above the unsatisfactory threshold. Great job!</p>
            </div>
        @endif

        <div class="text-center no-print mt-5">
            <button onclick="window.print()" class="btn btn-outline"><i class="fas fa-print"></i> Print Report</button>
            <a href="{{ route('surveys.results', $event) }}" class="btn btn-secondary">Back to Results</a>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print { display: none !important; }
    body { background: white !important; }
    .card { border: none !important; box-shadow: none !important; }
    .card-header { background: white !important; }
}
</style>
@endsection
