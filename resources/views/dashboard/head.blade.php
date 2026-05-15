@extends('layouts.app')
@section('title', auth()->user()->role->name . ' Dashboard')

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon red"><i class="fas fa-file-signature"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $pendingApprovals }}</div>
            <div class="stat-label">Pending Your Approval</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="fas fa-comment-medical"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $pendingFeedbackReports->count() }}</div>
            <div class="stat-label">Reports Needing Feedback</div>
        </div>
    </div>
</div>

<div class="card mb-5">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-comment-dots" style="color: #980517; margin-right: 0.5rem;"></i> Reports Needing Feedback</h3>
    </div>
    <div class="card-body">
        <div class="events-grid">
            @forelse($pendingFeedbackReports as $report)
                <div class="event-card-premium" onclick="window.location='{{ route('reports.show', $report) }}'" style="cursor: pointer; background: linear-gradient(135deg, #980517, #7f0413) !important;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <span class="badge-status" style="background: rgba(255,255,255,0.2) !important;">FEEDBACK REQUIRED</span>
                    </div>
                    <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem; color: #FFFFFF !important;">{{ $report->title }}</h3>
                    <p style="font-size: 0.875rem; margin-bottom: 1.25rem; line-height: 1.5; color: #FFFFFF !important;">Event: {{ $report->event->title }}</p>
                </div>
            @empty
                <div class="empty-state" style="padding: 1rem;">
                    <p style="color: #6b7280;">There are no reports needing your feedback.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<div class="card mb-5">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-exclamation-circle" style="color: #980517; margin-right: 0.5rem;"></i> Events Needing Approval</h3>
    </div>
    <div class="card-body">
        <div class="events-grid">
            @forelse($eventsToApprove as $event)
                <div class="event-card-premium" onclick="window.location='{{ route('events.show', $event) }}'" style="cursor: pointer;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <span class="badge-status">REQUIRES ACTION</span>
                    </div>
                    <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem; color: #FFFFFF !important;">{{ $event->title }}</h3>
                    <p style="font-size: 0.875rem; margin-bottom: 1.25rem; line-height: 1.5; color: #FFFFFF !important;">{{ Str::limit($event->description, 80) }}</p>
                </div>
            @empty
                <div class="empty-state" style="padding: 1rem;">
                    <p style="color: #6b7280;">You have no pending events to approve.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-history" style="color: #980517; margin-right: 0.5rem;"></i> Your Approval History</h3>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr><th>Event Title</th><th>Action</th><th>Date</th></tr>
                </thead>
                <tbody>
                    @forelse($approvalHistory as $log)
                    <tr>
                        <td style="font-weight:600;">{{ $log->event->title ?? 'N/A' }}</td>
                        <td><span class="badge-status badge-{{ $log->action === 'approved' ? 'published' : 'cancelled' }}">{{ ucfirst($log->action) }}</span></td>
                        <td style="color: #6b7280;">{{ $log->created_at->format('d M Y, H:i') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="text-align:center; padding: 2rem; color: #9ca3af;">No approval history found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
