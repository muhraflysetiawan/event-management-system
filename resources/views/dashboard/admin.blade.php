@extends('layouts.app')
@section('title', 'Admin Dashboard')

@section('content')
<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-chalkboard-teacher"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $totalEvents }}</div>
            <div class="stat-label">Total Events</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="fas fa-users"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $totalUsers }}</div>
            <div class="stat-label">Total Users</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="fas fa-clock"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $pendingParticipants }}</div>
            <div class="stat-label">Pending Participants</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="fas fa-check-circle"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $completedEvents }}</div>
            <div class="stat-label">Completed Events</div>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;" class="dashboard-grid">
    <!-- Chart -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-chart-bar" style="color:#000000;margin-right:0.5rem;"></i> Monthly Overview</h3>
        </div>
        <div class="card-body">
            <div class="chart-container">
                <div class="chart-bars">
                    @foreach($monthlyStats as $stat)
                    <div class="chart-bar-group">
                        <div class="chart-bars-wrapper">
                            <div class="chart-bar primary" style="height: {{ max(4, ($stat['events'] * 15)) }}px;" title="{{ $stat['events'] }} events"></div>
                            <div class="chart-bar" style="height: {{ max(4, ($stat['participants'] * 5)) }}px; background:#473f3d;" title="{{ $stat['participants'] }} participants"></div>
                        </div>
                        <span class="chart-label">{{ $stat['month'] }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="chart-legend">
                    <span><span class="legend-dot primary"></span> Events</span>
                    <span><span class="legend-dot" style="background:#473f3d;"></span> Participants</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Participants -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-clipboard-list" style="color:#000000;margin-right:0.5rem;"></i> Recent Participants</h3>
            <a href="{{ route('participants.index') }}" class="btn btn-sm btn-outline">View All</a>
        </div>
        <div class="card-body">
            @forelse($recentParticipants as $reg)
            <div style="display:flex;align-items:center;justify-content:space-between;padding:0.75rem 0;border-bottom:1px solid var(--border-color);">
                <div>
                    <div style="font-weight:600;font-size:0.9375rem;">{{ $reg->user->name }}</div>
                    <div style="font-size:0.8125rem;color:var(--text-muted);">{{ $reg->event->title }}</div>
                </div>
                <span class="badge-status badge-{{ $reg->status }}">{{ ucfirst($reg->status) }}</span>
            </div>
            @empty
            <div class="empty-state"><p>No participants yet</p></div>
            @endforelse
        </div>
    </div>
</div>

<!-- Recent Events -->
<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-list" style="color:#000000;margin-right:0.5rem;"></i> Recent Events</h3>
        <a href="{{ route('events.index') }}" class="btn btn-sm btn-outline">View All</a>
    </div>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Participants</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentEvents as $event)
                <tr>
                    <td style="font-weight:600;color:var(--text-primary);">{{ $event->title }}</td>
                    <td>{{ $event->start_date->format('d M Y') }}</td>
                    <td><span class="badge-status badge-{{ $event->status }}">{{ ucfirst($event->status) }}</span></td>
                    <td>{{ $event->participants_count ?? $event->participants->count() }}</td>
                    <td>
                        <div class="action-group">
                            <a href="{{ route('events.show', $event) }}" class="btn btn-sm btn-outline"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('attendance.generate', $event) }}" class="btn btn-sm btn-outline"><i class="fas fa-qrcode"></i></a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center" style="padding:2rem;">No Events yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
@media (max-width: 768px) {
    .dashboard-grid { grid-template-columns: 1fr !important; }
}
</style>
@endsection
