@extends('layouts.app')
@section('title', 'Committee Dashboard')

@section('content')

{{-- ─── Stats Row ─── --}}
<div class="stats-grid" style="margin-bottom: 2rem;">
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-chalkboard-teacher"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $totalCreated }}</div>
            <div class="stat-label">Events Created</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="fas fa-user-clock"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $pendingApprovals }}</div>
            <div class="stat-label">Pending Participants</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-hourglass-half"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $waitingApprovals->count() }}</div>
            <div class="stat-label">Awaiting Admin Approval</div>
        </div>
    </div>
</div>

{{-- ─── Waiting For Approval ─── --}}
@if($waitingApprovals->count() > 0)
<div class="card mb-3">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-hourglass-half" style="color: #d97706;"></i>
            Waiting for Approval
        </h3>
        <span class="badge-status badge-pending" style="font-size: 0.75rem;">
            {{ $waitingApprovals->count() }} pending
        </span>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.25rem;">
            @foreach($waitingApprovals as $event)
                <div class="event-card-premium" onclick="window.location='{{ route('events.show', $event) }}'" style="cursor: pointer;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                        <span style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; background: rgba(255,255,255,0.2); padding: 3px 8px; border-radius: 12px; color: #fff;">
                            PENDING APPROVAL
                        </span>
                        <span style="font-size: 0.75rem; opacity: 0.8;">
                            {{ count($event->approved_by_roles ?? []) }}/{{ count($event->required_approval_roles ?? []) }} approved
                        </span>
                    </div>
                    <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.5rem; color: #fff;">{{ $event->title }}</h3>
                    <p style="font-size: 0.8rem; line-height: 1.5; color: rgba(255,255,255,0.85); overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                        {{ $event->description }}
                    </p>
                    <div style="margin-top: 0.75rem; font-size: 0.75rem; opacity: 0.75;">
                        <i class="fas fa-calendar-alt"></i> {{ $event->start_date->format('d M Y') }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- ─── All My Events ─── --}}
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-list-ul" style="color: #980517;"></i>
            Your Events
        </h3>
        <a href="{{ route('events.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Create New
        </a>
    </div>
    <div class="card-body">
        @forelse($myEvents as $event)
            <div class="event-card-premium" onclick="window.location='{{ route('events.show', $event) }}'" style="cursor: pointer; margin-bottom: 1rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
                    <span style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; background: rgba(255,255,255,0.2); padding: 3px 8px; border-radius: 12px; color: #fff;">
                        {{ str_replace('_', ' ', strtoupper($event->status)) }}
                    </span>
                    <span style="font-size: 0.75rem; opacity: 0.8;">
                        <i class="fas fa-users"></i> {{ $event->participants->count() }}/{{ $event->quota }}
                    </span>
                </div>
                <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.35rem; color: #fff;">{{ $event->title }}</h3>
                <p style="font-size: 0.8rem; line-height: 1.5; color: rgba(255,255,255,0.85); overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                    {{ $event->description }}
                </p>
                <div style="margin-top: 0.75rem; font-size: 0.75rem; opacity: 0.75;">
                    <i class="fas fa-calendar-alt"></i> {{ $event->start_date->format('d M Y') }} — {{ $event->location }}
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-calendar-plus"></i>
                <h3>No events yet</h3>
                <p>Create your first event to get started.</p>
                <a href="{{ route('events.create') }}" class="btn btn-primary" style="margin-top: 1rem;">
                    <i class="fas fa-plus"></i> Create Event
                </a>
            </div>
        @endforelse
    </div>
</div>

@endsection
