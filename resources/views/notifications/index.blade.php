@extends('layouts.app')
@section('title', 'Notifications')

@section('content')
    @forelse($notifications as $notification)
    <div class="card mb-3">
        <div class="card-body">
            <div class="notification-item {{ !$notification->is_read ? 'unread' : '' }}" style="display: flex; gap: 1rem; align-items: flex-start; border-bottom: none; padding: 0;">
                <div class="notification-icon {{ $notification->type }}" style="width: 40px; height: 40px; border-radius: 50%; background: rgba(152, 5, 23, 0.1); display: flex; align-items: center; justify-content: center; color: #980517; flex-shrink: 0;">
                    <i class="fas fa-{{ match($notification->type) {
                        'event' => 'chalkboard-teacher',
                        'participant' => 'clipboard-check',
                        'certificate' => 'award',
                        default => 'bell'
                    } }}"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-title" style="font-weight: 700; color: #1f2937; margin-bottom: 0.25rem;">{{ $notification->title }}</div>
                    <div class="notification-message" style="color: #4b5563; font-size: 0.875rem; margin-bottom: 0.5rem;">
                        @if($notification->type === 'event' && $notification->event_id && str_contains($notification->message, 'Register now!'))
                            {!! str_replace('Register now!', '<a href="'.route('events.show', $notification->event_id).'" style="color:#980517;font-weight:bold;text-decoration:underline;">Register now!</a>', e($notification->message)) !!}
                        @else
                            {{ $notification->message }}
                        @endif
                    </div>
                    <div class="notification-time" style="font-size: 0.75rem; color: #9ca3af;">{{ $notification->created_at->diffForHumans() }}</div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="card">
        <div class="card-body empty-state" style="padding:3rem; text-align: center;">
            <i class="fas fa-bell-slash" style="font-size: 3rem; color: #9ca3af; margin-bottom: 1rem;"></i>
            <h3>No notifications</h3>
            <p>You're all caught up!</p>
        </div>
    </div>
    @endforelse

<div class="pagination-wrapper">{{ $notifications->links() }}</div>
@endsection
