@extends('layouts.app')
@section('title', 'Notifications')

@section('content')
<div class="card">
    @if($notifications->count())
    <div class="card-header">
        <div></div>
        <form method="POST" action="{{ route('notifications.markAllAsRead') }}">
            @csrf
            <button type="submit" class="btn btn-primary btn-sm">Mark All Read</button>
        </form>
    </div>
    @endif
    <div class="card-body" style="padding:0;">
    @forelse($notifications as $notification)
    <div class="notification-item {{ !$notification->is_read ? 'unread' : '' }}">
        <div class="notification-icon {{ $notification->type }}">
            <i class="fas fa-{{ match($notification->type) {
                'event' => 'chalkboard-teacher',
                'participant' => 'clipboard-check',
                'certificate' => 'award',
                default => 'bell'
            } }}"></i>
        </div>
        <div class="notification-content">
            <div class="notification-title">{{ $notification->title }}</div>
            <div class="notification-message">{{ $notification->message }}</div>
            <div class="notification-time">{{ $notification->created_at->diffForHumans() }}</div>
        </div>
        @if(!$notification->is_read)
        <form method="POST" action="{{ route('notifications.markAsRead', $notification) }}">
            @csrf @method('PATCH')
            <button type="submit" class="btn btn-sm btn-outline" title="Mark as read"><i class="fas fa-check"></i></button>
        </form>
        @endif
    </div>
    @empty
    <div class="empty-state" style="padding:3rem;">
        <i class="fas fa-bell-slash"></i>
        <h3>No notifications</h3>
        <p>You're all caught up!</p>
    </div>
    @endforelse
</div>

<div class="pagination-wrapper">{{ $notifications->links() }}    </div>
</div>
@endsection
