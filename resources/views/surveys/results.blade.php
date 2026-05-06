@extends('layouts.app')
@section('title', 'Survey Results - ' . $event->title)

@section('content')
<div class="card mb-4">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3 class="card-title">Survey Results: {{ $event->title }}</h3>
        <span class="badge-status" style="background: var(--primary-color); color: white;">{{ $totalResponses }} Responses</span>
    </div>
    <div class="card-body">
        <div class="action-group mb-4">
            <a href="{{ route('surveys.manage', $event) }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Manage</a>
            <a href="{{ route('surveys.report', $event) }}" class="btn btn-primary"><i class="fas fa-file-medical-alt"></i> View Summary Report</a>
        </div>

        @foreach($survey->questions as $index => $question)
            <div class="mb-5 p-4" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                <h4 class="mb-4">{{ $index + 1 }}. {{ $question->question_text }}</h4>
                
                @if($question->type === 'scale')
                    @php
                        $counts = $question->responses->groupBy('answer')->map->count();
                        $avg = $question->responses->avg('answer');
                    @endphp
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            @for($i = 5; $i >= 1; $i--)
                                @php
                                    $count = $counts->get($i) ?? 0;
                                    $percent = $totalResponses > 0 ? ($count / $totalResponses) * 100 : 0;
                                @endphp
                                <div class="mb-2">
                                    <div style="display: flex; justify-content: space-between; font-size: 0.9rem; margin-bottom: 2px;">
                                        <span>Rating {{ $i }}</span>
                                        <span>{{ $count }} ({{ round($percent) }}%)</span>
                                    </div>
                                    <div style="height: 8px; background: var(--bg-input); border-radius: 4px; overflow: hidden;">
                                        <div style="height: 100%; width: {{ $percent }}%; background: var(--primary-color);"></div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                        <div class="col-md-6 text-center">
                            <div style="font-size: 3.5rem; font-weight: 800; color: var(--primary-color);">{{ round($avg, 1) }}</div>
                            <div style="font-size: 1.1rem; color: var(--text-muted);">Average Rating</div>
                        </div>
                    </div>
                @else
                    <div class="table-container" style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-sm">
                            <thead>
                                <tr><th>User</th><th>Response</th></tr>
                            </thead>
                            <tbody>
                                @forelse($question->responses as $response)
                                    <tr>
                                        <td style="width: 150px; font-weight: 600;">{{ $response->user->name }}</td>
                                        <td>{{ $response->answer }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="text-center">No responses yet</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endsection
