@extends('layouts.app')
@section('title', 'Head Department Dashboard')

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-clock"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $pendingApprovals }}</div>
            <div class="stat-label">Pending Approvals</div>
        </div>
    </div>
</div>

<div class="section-header">
    <h3 class="section-title">Recent Events</h3>
    <a href="{{ route('events.index') }}" class="btn btn-outline">View All</a>
</div>

<div class="event-grid">
    @forelse($recentEvents as $event)
    <a href="{{ route('events.show', $event) }}" class="event-card">
        <div class="event-card-header">
            <span class="badge-status badge-{{ $event->status }}">{{ ucfirst(str_replace('_', ' ', $event->status)) }}</span>
        </div>
        <div class="event-card-body">
            <h3 class="event-title">{{ $event->title }}</h3>
            <p class="event-desc">{{ Str::limit($event->description, 100) }}</p>
            <div class="event-meta">
                <span><i class="fas fa-calendar"></i> {{ $event->start_date->format('d M Y') }}</span>
                <span><i class="fas fa-map-marker-alt"></i> {{ $event->location }}</span>
            </div>
        </div>
    </a>
    @empty
    <div class="empty-state" style="grid-column:1/-1;">
        <i class="fas fa-calendar-alt"></i>
        <h3>No recent events</h3>
        <p>No events have been created recently.</p>
    </div>
    @endforelse
</div>
@endsection
