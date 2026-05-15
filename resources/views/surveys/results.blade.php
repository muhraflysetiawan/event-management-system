@extends('layouts.app')
@section('title', 'Survey Results - ' . $event->title)

@push('styles')
<style>
    .survey-header-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .survey-badge {
        background: #980517;
        color: #fff;
        padding: 0.4rem 1rem;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 0.03em;
    }
    .survey-actions {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        margin-bottom: 1.5rem;
    }
    .survey-question-card {
        background: #FFFFFF;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .survey-question-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        background: #980517;
        color: #fff;
        border-radius: 50%;
        font-size: 0.8rem;
        font-weight: 700;
        margin-right: 0.75rem;
        flex-shrink: 0;
    }
    .survey-question-title {
        display: flex;
        align-items: center;
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 1.25rem;
    }
    .survey-question-type {
        margin-left: auto;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        background: var(--bg-input);
        border: 1px solid var(--border-color);
        padding: 0.2rem 0.6rem;
        border-radius: 4px;
    }
    /* Scale rating bars */
    .rating-row {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.6rem;
    }
    .rating-label {
        width: 70px;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-secondary);
        flex-shrink: 0;
    }
    .rating-bar-track {
        flex: 1;
        height: 10px;
        background: #f1f1f1;
        border-radius: 5px;
        overflow: hidden;
    }
    .rating-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #980517, #c0392b);
        border-radius: 5px;
    }
    .rating-count {
        width: 80px;
        text-align: right;
        font-size: 0.8rem;
        color: var(--text-muted);
        font-weight: 500;
    }
    .rating-avg-box {
        text-align: center;
        padding: 1.5rem;
    }
    .rating-avg-number {
        font-size: 3rem;
        font-weight: 900;
        color: #980517;
        line-height: 1;
    }
    .rating-avg-label {
        font-size: 0.85rem;
        color: var(--text-muted);
        margin-top: 0.5rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .scale-layout {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 2rem;
        align-items: center;
    }
    @media (max-width: 768px) {
        .scale-layout { grid-template-columns: 1fr; }
        .rating-avg-box { padding: 1rem 0; }
    }
    /* Text responses table */
    .response-table {
        width: 100%;
        border-collapse: collapse;
    }
    .response-table thead th {
        background: #fafafa;
        border-bottom: 2px solid var(--border-color);
        padding: 0.75rem 1rem;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        text-align: left;
    }
    .response-table tbody td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid var(--border-color);
        vertical-align: top;
        font-size: 0.9rem;
        color: var(--text-primary);
    }
    .response-table tbody tr:last-child td {
        border-bottom: none;
    }
    .response-user {
        font-weight: 600;
        white-space: nowrap;
    }
    .response-text {
        line-height: 1.6;
        color: var(--text-secondary);
    }
    .response-scroll {
        max-height: 350px;
        overflow-y: auto;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
    }
    /* Global reply section */
    .global-reply-section {
        background: #f8fdf9;
        border: 1px solid rgba(5, 150, 105, 0.2);
        border-radius: var(--radius-md);
        padding: 1.75rem;
        margin-top: 0.5rem;
    }
    .global-reply-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 1rem;
        font-weight: 700;
        color: #047857;
        margin-bottom: 0.5rem;
    }
    .global-reply-desc {
        color: var(--text-secondary);
        font-size: 0.875rem;
        margin-bottom: 1.25rem;
        line-height: 1.6;
    }
    .global-reply-section textarea {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        font-family: inherit;
        font-size: 0.9rem;
        resize: vertical;
        min-height: 100px;
        outline: none;
        color: var(--text-primary);
        background: #fff;
    }
    .global-reply-section textarea:focus {
        border-color: #059669;
        box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
    }
    .global-reply-footer {
        display: flex;
        justify-content: flex-end;
        margin-top: 1rem;
    }
    .btn-global-reply {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.65rem 1.5rem;
        background: #059669;
        color: #fff;
        border: none;
        border-radius: var(--radius-sm);
        font-weight: 700;
        font-size: 0.875rem;
        cursor: pointer;
        font-family: inherit;
    }
    .btn-global-reply:hover {
        background: #047857;
    }
    .existing-reply-box {
        background: #fff;
        border: 1px solid rgba(5, 150, 105, 0.25);
        border-left: 4px solid #059669;
        border-radius: var(--radius-sm);
        padding: 1rem 1.25rem;
        margin-bottom: 1.25rem;
    }
    .existing-reply-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #059669;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.4rem;
    }
    .existing-reply-text {
        color: var(--text-primary);
        line-height: 1.7;
        font-size: 0.9rem;
        white-space: pre-wrap;
    }
</style>
@endpush

@section('content')
<div class="card mb-4">
    <div class="card-header">
        <div class="survey-header-bar">
            <h3 class="card-title" style="margin: 0;">Survey Results: {{ $event->title }}</h3>
            <span class="survey-badge"><i class="fas fa-users" style="color: inherit;"></i> {{ $totalResponses }} Responses</span>
        </div>
    </div>
    <div class="card-body">
        <div class="survey-actions">
            <a href="{{ route('surveys.manage', $event) }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Manage</a>
            <a href="{{ route('surveys.report', $event) }}" class="btn btn-primary"><i class="fas fa-file-medical-alt"></i> View Summary Report</a>
        </div>

        @foreach($survey->questions as $index => $question)
            <div class="survey-question-card">
                <div class="survey-question-title">
                    <span class="survey-question-number">{{ $index + 1 }}</span>
                    {{ $question->question_text }}
                    <span class="survey-question-type">{{ $question->type === 'scale' ? 'Scale 1-5' : 'Open Text' }}</span>
                </div>

                @if($question->type === 'scale')
                    @php
                        $counts = $question->responses->groupBy('answer')->map->count();
                        $avg = $question->responses->avg('answer');
                    @endphp
                    <div class="scale-layout">
                        <div>
                            @for($i = 5; $i >= 1; $i--)
                                @php
                                    $count = $counts->get($i) ?? 0;
                                    $percent = $totalResponses > 0 ? ($count / $totalResponses) * 100 : 0;
                                @endphp
                                <div class="rating-row">
                                    <span class="rating-label">★ {{ $i }}</span>
                                    <div class="rating-bar-track">
                                        <div class="rating-bar-fill" style="width: {{ $percent }}%;"></div>
                                    </div>
                                    <span class="rating-count">{{ $count }} ({{ round($percent) }}%)</span>
                                </div>
                            @endfor
                        </div>
                        <div class="rating-avg-box">
                            <div class="rating-avg-number">{{ round($avg, 1) }}</div>
                            <div class="rating-avg-label">Average</div>
                        </div>
                    </div>
                @else
                    <div class="response-scroll">
                        <table class="response-table">
                            <thead>
                                <tr>
                                    <th style="width: 150px;">User</th>
                                    <th>Response</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($question->responses as $response)
                                    <tr>
                                        <td class="response-user">{{ $response->user->name }}</td>
                                        <td class="response-text">{{ $response->answer }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" style="text-align: center; color: var(--text-muted); padding: 2rem;">No responses yet</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endforeach

        {{-- Global Survey Feedback Reply --}}
        <div class="global-reply-section">
            <div class="global-reply-header">
                <i class="fas fa-bullhorn" style="color: #059669;"></i> Global Survey Reply
            </div>
            <p class="global-reply-desc">
                Write a single global response to address the overall survey feedback. This reply will be visible to all participants on the event details page and they will receive a notification.
            </p>

            @if($survey->organizer_reply)
                <div class="existing-reply-box">
                    <div class="existing-reply-label"><i class="fas fa-check-circle" style="color: #059669;"></i> Current Published Reply</div>
                    <div class="existing-reply-text">{{ $survey->organizer_reply }}</div>
                </div>
            @endif

            <form method="POST" action="{{ route('surveys.reply', $event) }}">
                @csrf
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">{{ $survey->organizer_reply ? 'Update Your Reply' : 'Your Reply' }}</label>
                    <textarea name="organizer_reply" rows="4" required placeholder="Thank you all for your feedback. We have noted your concerns regarding...">{{ $survey->organizer_reply }}</textarea>
                </div>
                <div class="global-reply-footer">
                    <button type="submit" class="btn-global-reply">
                        <i class="fas fa-paper-plane" style="color: inherit;"></i>
                        {{ $survey->organizer_reply ? 'Update Global Reply' : 'Publish Global Reply' }}
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
