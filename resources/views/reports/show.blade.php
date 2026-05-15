@extends('layouts.app')
@section('title', 'Report Details')

@section('content')
@push('styles')
<style>
    .report-top-bar {
        margin-bottom: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .report-actions {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }
    @media (max-width: 640px) {
        .report-top-bar {
            flex-direction: column;
            align-items: stretch;
            gap: 0.75rem;
        }
        .report-actions {
            flex-direction: column;
            align-items: stretch;
            gap: 0.5rem;
        }
        .report-actions > * {
            width: 100%;
            justify-content: center;
            text-align: center;
        }
        .report-actions .action-group {
            flex-direction: column;
            width: 100%;
        }
        .report-actions .action-group .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

<div class="report-top-bar">
    <a href="{{ route('events.show', $report->event) }}" class="link"><i class="fas fa-arrow-left"></i> Back to event</a>
    <div class="report-actions">
        @if($report->management_feedback)
            <span class="badge-status badge-accepted" style="width: 100%;"><i class="fas fa-check-circle"></i> Feedback Received</span>
        @else
            <span class="badge-status badge-pending" style="width: 100%;"><i class="fas fa-clock"></i> Awaiting Feedback</span>
        @endif
        
        <div class="action-group">
            @php
                $user = auth()->user();
                $isOrganizer = $report->created_by === $user->id && !$user->isAdmin();
                $canDownload = !$isOrganizer || $report->management_feedback;
            @endphp
            
            @if($canDownload)
                <a href="{{ route('reports.exportPdf', $report) }}" class="btn btn-primary btn-sm"><i class="fas fa-file-pdf"></i> Export PDF</a>
                <a href="{{ route('reports.exportExcel', $report) }}" class="btn btn-success btn-sm"><i class="fas fa-file-excel"></i> Export Excel</a>
            @else
                <button class="btn btn-secondary btn-sm" title="Waiting for management feedback" disabled><i class="fas fa-lock"></i> PDF Locked</button>
            @endif
        </div>
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

        <hr style="margin: 2.5rem 0; border: none; border-top: 1px solid var(--border-color);">

        <hr style="margin: 2.5rem 0; border: none; border-top: 1px solid var(--border-color);">

        {{-- ─── Management Feedback Section ─── --}}
        @if($report->management_feedback)
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: var(--radius-lg); padding: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h4 style="color: #166534; margin: 0;"><i class="fas fa-comment-dots"></i> Management Feedback</h4>
                    <span style="font-size: 0.8rem; color: #166534; font-weight: 600;">
                        By {{ $report->feedbackBy->name }} on {{ $report->management_feedback_at->format('d M Y, H:i') }}
                    </span>
                </div>
                <div style="color: #14532d; line-height: 1.7; white-space: pre-line;">{{ $report->management_feedback }}</div>
            </div>
        @elseif(auth()->user()->isAdmin() || auth()->user()->isACOO() || auth()->user()->isHeadDepartment())
            <div style="background: var(--bg-input); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.5rem;">
                <h4 style="margin-bottom: 1rem;"><i class="fas fa-pen-fancy"></i> Provide Feedback</h4>
                <form method="POST" action="{{ route('reports.feedback', $report) }}">
                    @csrf
                    <div class="form-group">
                        <textarea name="management_feedback" class="form-input" rows="4" placeholder="Enter your review and feedback for this report..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Submit Feedback
                    </button>
                </form>
            </div>
        @else
            <div style="background: #fffbeb; border: 1px solid #fef3c7; border-radius: var(--radius-lg); padding: 1.5rem; text-align: center; color: #92400e;">
                <i class="fas fa-info-circle" style="font-size: 1.5rem; margin-bottom: 0.5rem; display: block;"></i>
                Waiting for management to review and provide feedback.
            </div>
        @endif

        <hr style="margin: 2.5rem 0; border: none; border-top: 1px solid var(--border-color);">

        {{-- ─── Survey Reply Section ─── --}}
        <div style="background: var(--bg-input); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.5rem; margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h4 style="color: var(--text-primary); margin: 0;"><i class="fas fa-reply"></i> Survey Response / Organizer Reply</h4>
                @if($report->survey_reply)
                    <span class="badge-status badge-accepted" style="font-size: 0.75rem;"><i class="fas fa-check"></i> Reply Posted</span>
                @else
                    <span class="badge-status badge-pending" style="font-size: 0.75rem;"><i class="fas fa-clock"></i> No Reply Yet</span>
                @endif
            </div>

            @if(auth()->user()->isAdmin() || $report->created_by === auth()->id())
                <form method="POST" action="{{ route('reports.updateSurveyReply', $report) }}">
                    @csrf
                    <div class="form-group">
                        <textarea name="survey_reply" class="form-input" rows="4" placeholder="Write your response to the survey results and feedback here... participants will be notified." required>{{ old('survey_reply', $report->survey_reply) }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> {{ $report->survey_reply ? 'Update Reply' : 'Submit Reply' }}
                    </button>
                </form>
            @elseif($report->survey_reply)
                <div style="color: var(--text-secondary); line-height: 1.7; white-space: pre-line;">{{ $report->survey_reply }}</div>
            @else
                <div style="text-align: center; color: var(--text-muted); font-style: italic; padding: 1rem;">
                    No reply has been provided by the organizer yet.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
