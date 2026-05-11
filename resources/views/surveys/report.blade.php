@extends('layouts.app')
@section('title', 'Survey Report - ' . $event->title)

@section('content')
<div class="card mb-4" id="report-content" style="border: none; box-shadow: var(--shadow-lg);">
    <div class="card-header text-center" style="padding: 3rem 2rem; background: linear-gradient(135deg, #980517, #473f3d); color: white; border: none;">
        <h1 style="font-size: 2.25rem; font-weight: 800; margin-bottom: 0.5rem; letter-spacing: -0.02em;">EVENT SATISFACTION REPORT</h1>
        <h4 style="opacity: 0.9; font-weight: 500;">{{ $event->title }}</h4>
        <div style="margin-top: 1.5rem;">
            <span style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3); border-radius: 50px; font-weight: 700; backdrop-filter: blur(4px);">
                <i class="fas fa-chart-line"></i> Overall Conclusion: {{ $conclusion }}
            </span>
        </div>
    </div>
    <div class="card-body" style="padding: 3rem;">
        {{-- ─── Score Overview ─── --}}
        <div style="display: flex; justify-content: center; margin-bottom: 4rem;">
            <div style="text-align: center; background: var(--bg-input); padding: 2.5rem 4rem; border-radius: var(--radius-lg); border: 1px solid var(--border-color); box-shadow: var(--shadow-sm);">
                <div style="font-size: 4.5rem; font-weight: 900; color: #980517; line-height: 1;">{{ round($finalScore, 1) }} <span style="font-size: 1.5rem; color: var(--text-muted); font-weight: 600;">/ 5.0</span></div>
                <p style="margin-top: 0.75rem; color: var(--text-secondary); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; font-size: 0.875rem;">Aggregate Satisfaction Score</p>
            </div>
        </div>

        {{-- ─── Performance Breakdown ─── --}}
        <div style="margin-bottom: 4rem;">
            <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
                <i class="fas fa-tasks" style="color: #980517;"></i> Performance Breakdown
            </h3>
            <div class="table-container">
                <table class="table" style="border-collapse: separate; border-spacing: 0 0.5rem; margin-top: -0.5rem;">
                    <thead>
                        <tr style="border: none;">
                            <th style="background: transparent; border: none; padding-bottom: 1rem;">Metric / Question</th>
                            <th class="text-center" style="background: transparent; border: none; padding-bottom: 1rem;">Average Score</th>
                            <th style="background: transparent; border: none; padding-bottom: 1rem;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $data)
                            <tr style="background: #fdfdfd; border: 1px solid var(--border-color);">
                                <td style="padding: 1.25rem; border-radius: 8px 0 0 8px; font-weight: 600; color: var(--text-primary);">{{ $data['question'] }}</td>
                                <td class="text-center" style="padding: 1.25rem; font-weight: 800; font-size: 1.25rem; color: {{ $data['average'] >= 4 ? '#10b981' : ($data['average'] >= 3.5 ? '#f59e0b' : '#ef4444') }};">
                                    {{ $data['average'] }}
                                </td>
                                <td style="padding: 1.25rem; border-radius: 0 8px 8px 0;">
                                    @if($data['average'] >= 4)
                                        <span style="display: inline-flex; align-items: center; gap: 0.4rem; font-weight: 700; color: #10b981;">
                                            <i class="fas fa-check-circle"></i> EXCELLENT
                                        </span>
                                    @elseif($data['average'] >= 3.5)
                                        <span style="display: inline-flex; align-items: center; gap: 0.4rem; font-weight: 700; color: #f59e0b;">
                                            <i class="fas fa-info-circle"></i> GOOD
                                        </span>
                                    @else
                                        <span style="display: inline-flex; align-items: center; gap: 0.4rem; font-weight: 700; color: #ef4444;">
                                            <i class="fas fa-exclamation-circle"></i> UNSATISFACTORY
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ─── Insight Section ─── --}}
        @if(count($unsatisfactory) > 0)
            <div style="background: #fffafa; border: 1px solid #fee2e2; border-radius: var(--radius-lg); padding: 2.5rem; position: relative; overflow: hidden;">
                <div style="position: absolute; top: -10px; right: -10px; font-size: 5rem; color: #ef4444; opacity: 0.05;">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <h3 style="color: #991b1b; font-size: 1.25rem; font-weight: 800; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.75rem;">
                    <i class="fas fa-exclamation-triangle"></i> Areas for Improvement
                </h3>
                <p style="color: #7f1d1d; margin-bottom: 2rem; line-height: 1.6;">The following metrics were identified as <strong>unsatisfactory</strong> (score below 3.5) and require strategic attention for future events:</p>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1rem;">
                    @foreach($unsatisfactory as $item)
                        <div style="padding: 1.25rem; background: white; border-radius: 12px; border: 1px solid #fecaca; box-shadow: 0 4px 6px rgba(0,0,0,0.02); display: flex; flex-direction: column; gap: 0.5rem;">
                            <div style="font-size: 0.875rem; font-weight: 700; color: #991b1b;">{{ strtoupper($item['question']) }}</div>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-size: 0.8125rem; color: var(--text-secondary);">Avg. Satisfaction Score</span>
                                <span style="font-weight: 800; color: #ef4444; font-size: 1.1rem;">{{ $item['average'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: var(--radius-lg); padding: 2.5rem; text-align: center;">
                <div style="font-size: 3rem; color: #10b981; margin-bottom: 1rem;"><i class="fas fa-check-circle"></i></div>
                <h3 style="color: #166534; font-size: 1.25rem; font-weight: 800; margin-bottom: 0.5rem;">High Performance Achieved</h3>
                <p style="color: #14532d;">All satisfaction metrics are above the satisfactory threshold. Excellent delivery!</p>
            </div>
        @endif

        <div class="text-center no-print mt-5" style="display: flex; gap: 1rem; justify-content: center; align-items: center;">
            <button onclick="window.print()" class="btn btn-outline" style="padding: 0.75rem 2rem;"><i class="fas fa-print"></i> Print Report</button>
            <a href="{{ route('surveys.results', $event) }}" class="btn btn-secondary" style="padding: 0.75rem 2rem;">Back to Results</a>
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
