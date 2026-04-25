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
            <div class="stat-label">Pending Approvals</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="event-grid">
        @forelse($myEvents as $event)
        <a href="{{ route('events.show', $event) }}" class="event-card">
            <div class="event-card-header">
                <span class="badge-status badge-{{ $event->status }}">{{ ucfirst($event->status) }}</span>
            </div>
            <div class="event-card-body">
                <h3 class="event-title">{{ $event->title }}</h3>
                <p class="event-desc">{{ Str::limit($event->description, 100) }}</p>
                <div class="event-meta">
                    <span><i class="fas fa-calendar"></i> {{ $event->start_date->format('d M Y') }}</span>
                    <span><i class="fas fa-map-marker-alt"></i> {{ $event->location }}</span>
                    <span><i class="fas fa-users"></i> {{ $event->quota }} slots</span>
                </div>
            </div>
        </a>
        @empty
        <div class="empty-state" style="grid-column:1/-1;">
            <i class="fas fa-chalkboard-teacher"></i>
            <h3>No events yet</h3>
            <p>Create your first event to get started</p>
            <div style="margin-top: 1rem;">
                <a href="{{ route('events.create') }}" class="btn btn-primary">Create Event</a>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
