@extends('layouts.app')
@section('title', 'Student Dashboard')

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-clipboard-check"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $registeredCount }}</div>
            <div class="stat-label">Registered Events</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="fas fa-award"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $certificatesCount }}</div>
            <div class="stat-label">Certificates Earned</div>
        </div>
    </div>
</div>

<!-- Upcoming Events -->
<div class="card mb-3">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-calendar-alt" style="color:#000000;margin-right:0.5rem;"></i> Available Events</h3>
        <a href="{{ route('events.index') }}" class="btn btn-sm btn-outline">Browse All</a>
    </div>
    <div class="card-body">
        @forelse($upcomingEvents as $event)
        <a href="{{ route('events.show', $event) }}" style="display:flex;align-items:center;justify-content:space-between;padding:0.875rem 0;border-bottom:1px solid var(--border-color);text-decoration:none;color:inherit;transition:opacity 0.2s;">
            <div>
                <div style="font-weight:600;font-size:0.9375rem;color:var(--text-primary);">{{ $event->title }}</div>
                <div style="font-size:0.8125rem;color:var(--text-muted);margin-top:0.25rem;">
                    <i class="fas fa-calendar" style="margin-right:0.25rem;"></i> {{ $event->start_date->format('d M Y, H:i') }}
                    <span style="margin:0 0.5rem;">•</span>
                    <i class="fas fa-map-marker-alt" style="margin-right:0.25rem;"></i> {{ $event->location }}
                </div>
            </div>
            <div style="text-align:right;">
                <span class="badge-status badge-published">Open</span>
                <div style="font-size:0.75rem;color:var(--text-muted);margin-top:0.375rem;">{{ $event->availableSlots() }} slots left</div>
            </div>
        </a>
        @empty
        <div class="empty-state">
            <i class="fas fa-calendar-times"></i>
            <p>No upcoming events available</p>
        </div>
        @endforelse
    </div>
</div>

<!-- My Recent Participants -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-clipboard-list" style="color:#000000;margin-right:0.5rem;"></i> My Participants</h3>
        <a href="{{ route('participants.my') }}" class="btn btn-sm btn-outline">View All</a>
    </div>
    <div class="card-body">
        @forelse($myParticipants as $reg)
        <div style="display:flex;align-items:center;justify-content:space-between;padding:0.75rem 0;border-bottom:1px solid var(--border-color);">
            <div>
                <div style="font-weight:600;font-size:0.9375rem;">{{ $reg->event->title }}</div>
                <div style="font-size:0.8125rem;color:var(--text-muted);">Reg #{{ $reg->participant_number }}</div>
            </div>
            <span class="badge-status badge-{{ $reg->status }}">{{ ucfirst($reg->status) }}</span>
        </div>
        @empty
        <div class="empty-state">
            <i class="fas fa-clipboard"></i>
            <p>No participants yet. Browse events to get started!</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
