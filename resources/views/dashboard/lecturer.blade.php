@extends('layouts.app')
@section('title', 'Lecturer Dashboard')

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-chalkboard-teacher" style="color:var(--primary-400);margin-right:0.5rem;"></i> Available Events</h3>
    </div>
    <div class="card-body">
        @forelse($availableEvents as $event)
        <a href="{{ route('events.show', $event) }}" style="display:flex;align-items:center;justify-content:space-between;padding:0.875rem 0;border-bottom:1px solid var(--border-color);text-decoration:none;color:inherit;">
            <div>
                <div style="font-weight:600;color:var(--text-primary);">{{ $event->title }}</div>
                <div style="font-size:0.8125rem;color:var(--text-muted);margin-top:0.25rem;">
                    <i class="fas fa-calendar"></i> {{ $event->start_date->format('d M Y') }} • <i class="fas fa-map-marker-alt"></i> {{ $event->location }}
                </div>
            </div>
            <span class="badge-status badge-{{ $event->status }}">{{ ucfirst($event->status) }}</span>
        </a>
        @empty
        <div class="empty-state"><i class="fas fa-inbox"></i><p>No events available</p></div>
        @endforelse
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-bell" style="color:var(--primary-400);margin-right:0.5rem;"></i> Recent Notifications</h3>
        <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-outline">View All</a>
    </div>
    <div class="card-body">
        @forelse($notifications as $notification)
        <div class="notification-item {{ !$notification->is_read ? 'unread' : '' }}">
            <div class="notification-icon {{ $notification->type }}"><i class="fas fa-{{ $notification->type === 'event' ? 'chalkboard-teacher' : 'bell' }}"></i></div>
            <div class="notification-content">
                <div class="notification-title">{{ $notification->title }}</div>
                <div class="notification-message">{{ $notification->message }}</div>
                <div class="notification-time">{{ $notification->created_at->diffForHumans() }}</div>
            </div>
        </div>
        @empty
        <div class="empty-state"><i class="fas fa-bell-slash"></i><p>No notifications</p></div>
        @endforelse
    </div>
</div>
@endsection
