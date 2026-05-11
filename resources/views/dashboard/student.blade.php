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

<div class="card mb-5" style="border: none; box-shadow: var(--shadow-md);">
    <div class="card-header" style="background: transparent; border-bottom: 1px solid var(--border-color); padding: 1.5rem;">
        <h3 class="card-title" style="font-weight: 800; color: #980517; display: flex; align-items: center; gap: 0.75rem;">
            <i class="fas fa-play-circle"></i> Currently Following (Ongoing)
        </h3>
    </div>
    <div class="card-body" style="padding: 1.5rem;">
        <div class="events-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem;">
            @forelse($ongoingEvents as $event)
                <div class="event-card-premium" onclick="window.location='{{ route('events.show', $event) }}'" 
                     style="cursor: pointer; padding: 1.5rem; border-radius: 16px; min-height: 200px; display: flex; flex-direction: column;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <span class="badge-status" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.4);">ONGOING</span>
                        <div style="font-size: 0.75rem; font-weight: 700; color: white;">
                            <i class="fas fa-users" style="color: white;"></i> {{ $event->participants->count() }}/{{ $event->quota }}
                        </div>
                    </div>
                    <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 0.5rem; color: white !important;">{{ $event->title }}</h3>
                    <p style="font-size: 0.875rem; color: rgba(255,255,255,0.9) !important; margin-bottom: 1.5rem; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $event->description }}</p>
                    
                    <div style="margin-top: auto; display: flex; flex-direction: column; gap: 0.5rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8rem; color: white;">
                            <i class="fas fa-calendar-alt" style="width: 16px; color: white;"></i>
                            <span>{{ $event->start_date->format('d M Y, H:i') }}</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8rem; color: white;">
                            <i class="fas fa-map-marker-alt" style="width: 16px; color: white;"></i>
                            <span style="display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $event->location }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state" style="grid-column: 1 / -1; padding: 4rem 2rem; text-align: center; color: var(--text-muted);">
                    <i class="fas fa-ghost" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.2;"></i>
                    <p style="font-weight: 600;">No ongoing events at the moment</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<div class="card mb-5" style="border: none; box-shadow: var(--shadow-md);">
    <div class="card-header" style="background: transparent; border-bottom: 1px solid var(--border-color); padding: 1.5rem;">
        <h3 class="card-title" style="font-weight: 800; color: #980517; display: flex; align-items: center; gap: 0.75rem;">
            <i class="fas fa-calendar-plus"></i> Available for Registration
        </h3>
    </div>
    <div class="card-body" style="padding: 1.5rem;">
        <div class="events-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem;">
            @forelse($availableEvents as $event)
                <div class="event-card-premium" onclick="window.location='{{ route('events.show', $event) }}'" 
                     style="cursor: pointer; padding: 1.5rem; border-radius: 16px; min-height: 180px; display: flex; flex-direction: column;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <span class="badge-status" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.4);">OPEN</span>
                        <div style="font-size: 0.75rem; font-weight: 700; color: white;">
                            <i class="fas fa-users" style="color: white;"></i> {{ $event->participants->count() }}/{{ $event->quota }}
                        </div>
                    </div>
                    <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 0.5rem; color: white !important;">{{ $event->title }}</h3>
                    <p style="font-size: 0.875rem; color: rgba(255,255,255,0.9) !important; margin-bottom: 1.5rem; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $event->description }}</p>
                    
                    <div style="margin-top: auto;">
                        <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8rem; color: white;">
                            <i class="fas fa-calendar-alt" style="width: 16px; color: white;"></i>
                            <span>{{ $event->start_date->format('d M Y, H:i') }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state" style="grid-column: 1 / -1; padding: 4rem 2rem; text-align: center; color: var(--text-muted);">
                    <i class="fas fa-calendar-times" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.2;"></i>
                    <p style="font-weight: 600;">No events available for registration</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<style>
.event-card-new:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
    border-color: #980517 !important;
}
</style>
@endsection
