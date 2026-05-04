@extends('layouts.app')
@section('title', 'Student Dashboard')

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon red"><i class="fas fa-clipboard-check"></i></div>
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
    <div class="stat-card">
        <div class="stat-icon red"><i class="fas fa-running"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $ongoingEvents->count() }}</div>
            <div class="stat-label">Events Ongoing</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="fas fa-user-check"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $attendanceCount }}</div>
            <div class="stat-label">Attendance</div>
        </div>
    </div>
</div>

<div class="card mb-5">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-play-circle" style="color: #980517; margin-right: 0.5rem;"></i> Currently Following (Ongoing)</h3>
    </div>
    <div class="card-body">
        <div class="events-grid">
            @forelse($ongoingEvents as $event)
                <div class="event-card-premium" onclick="window.location='{{ route('events.show', $event) }}'" style="cursor: pointer;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <span class="badge-status">ONGOING</span>
                        <div style="font-size: 0.75rem;">
                            <i class="fas fa-users"></i> {{ $event->participants->count() }}/{{ $event->quota }}
                        </div>
                    </div>
                    <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem; color: #FFFFFF !important;">{{ $event->title }}</h3>
                    <p style="font-size: 0.875rem; margin-bottom: 1.25rem; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; color: #FFFFFF !important;">{{ $event->description }}</p>
                    <div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
                        <span style="background: rgba(255, 255, 255, 0.1); padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.75rem; border: 1px solid rgba(255, 255, 255, 0.2); color: #FFFFFF !important;">
                            <i class="fas fa-calendar-alt"></i> {{ $event->start_date->format('d M Y') }}
                        </span>
                        <span style="background: rgba(255, 255, 255, 0.1); padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.75rem; border: 1px solid rgba(255, 255, 255, 0.2); color: #FFFFFF !important;">
                            <i class="fas fa-map-marker-alt"></i> {{ $event->location }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="empty-state" style="padding: 2rem;">
                    <i class="fas fa-ghost"></i>
                    <p>No ongoing events</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<div class="card mb-5">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-calendar-plus" style="color: #980517; margin-right: 0.5rem;"></i> Available for Registration</h3>
    </div>
    <div class="card-body">
        <div class="events-grid">
            @forelse($availableEvents as $event)
                <div class="event-card-premium" onclick="window.location='{{ route('events.show', $event) }}'" style="cursor: pointer;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <span class="badge-status">OPEN</span>
                        <div style="font-size: 0.75rem;">
                            <i class="fas fa-users"></i> {{ $event->participants->count() }}/{{ $event->quota }}
                        </div>
                    </div>
                    <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem; color: #FFFFFF !important;">{{ $event->title }}</h3>
                    <p style="font-size: 0.875rem; margin-bottom: 1.25rem; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; color: #FFFFFF !important;">{{ $event->description }}</p>
                    <div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
                        <span style="background: rgba(255, 255, 255, 0.1); padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.75rem; border: 1px solid rgba(255, 255, 255, 0.2); color: #FFFFFF !important;">
                            <i class="fas fa-calendar-alt"></i> {{ $event->start_date->format('d M Y') }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="empty-state" style="padding: 2rem;">
                    <i class="fas fa-calendar-times"></i>
                    <p>No events available</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
