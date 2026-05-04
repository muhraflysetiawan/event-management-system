@extends('layouts.app')
@section('title', 'Committee Dashboard')

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-chalkboard-teacher"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $totalCreated }}</div>
            <div class="stat-label">Events Created</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="fas fa-clock"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $pendingApprovals }}</div>
            <div class="stat-label">Pending Participant Approvals</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-file-signature"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $waitingApprovals->count() }}</div>
            <div class="stat-label">Waiting Admin Approval</div>
        </div>
    </div>
</div>

<div class="card mb-5">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-hourglass-half" style="color: #980517; margin-right: 0.5rem;"></i> Waiting for Approval</h3>
    </div>
    <div class="card-body">
        <div class="events-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 2rem;">
            @forelse($waitingApprovals as $event)
                <div class="event-card-premium" onclick="window.location='{{ route('events.show', $event) }}'" style="cursor: pointer;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <span class="badge-status">PENDING</span>
                    </div>
                    <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem; color: #FFFFFF !important;">{{ $event->title }}</h3>
                    <p style="font-size: 0.875rem; margin-bottom: 1.25rem; line-height: 1.5; color: #FFFFFF !important;">{{ Str::limit($event->description, 80) }}</p>
                </div>
            @empty
                <div class="empty-state" style="padding: 1rem;">
                    <p>No events waiting for approval.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-list-ul" style="color: #980517; margin-right: 0.5rem;"></i> Your Recent Events</h3>
    </div>
    <div class="card-body">
        <div class="events-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 2rem;">
            @forelse($myEvents as $event)
                <div class="event-card-premium" onclick="window.location='{{ route('events.show', $event) }}'" style="cursor: pointer;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <span class="badge-status">{{ strtoupper($event->status) }}</span>
                        <div style="font-size: 0.75rem;">
                            <i class="fas fa-users"></i> {{ $event->participants->count() }}/{{ $event->quota }}
                        </div>
                    </div>
                    <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem; color: #FFFFFF !important;">{{ $event->title }}</h3>
                    <p style="font-size: 0.875rem; margin-bottom: 1.25rem; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; color: #FFFFFF !important;">{{ $event->description }}</p>
                </div>
            @empty
                <div class="empty-state" style="padding: 3rem;">
                    <i class="fas fa-plus-circle" style="font-size: 3rem; color: #9ca3af; margin-bottom: 1rem;"></i>
                    <h3 style="color: #4b5563;">Start your journey</h3>
                    <p style="color: #6b7280;">Create your first event today!</p>
                    <div style="margin-top: 1rem;">
                        <a href="{{ route('events.create') }}" class="btn btn-primary">Create Event</a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
