@extends('layouts.app')
@section('title', 'Notifications')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Notifications</h3>
        @php $unread = $notifications->where('is_read', false)->count(); @endphp
        @if($unread > 0)
            <span class="badge-status" style="background: rgba(152,5,23,0.1); color: #980517; border: 1px solid rgba(152,5,23,0.2);">
                {{ $unread }} unread
            </span>
        @endif
    </div>

    @forelse($notifications as $notification)
    <a href="{{ route('notifications.show', $notification) }}"
       style="display: flex; align-items: flex-start; gap: 1rem; padding: 1rem 1.5rem; text-decoration: none;
              border-bottom: 1px solid var(--border-color);
              background: {{ !$notification->is_read ? 'rgba(152,5,23,0.02)' : 'transparent' }};
              transition: background 0.15s ease;"
       onmouseover="this.style.background='rgba(152,5,23,0.05)'"
       onmouseout="this.style.background='{{ !$notification->is_read ? 'rgba(152,5,23,0.02)' : 'transparent' }}'">

        {{-- Icon --}}
        <div style="width: 40px; height: 40px; border-radius: 50%; flex-shrink: 0; display: flex; align-items: center; justify-content: center;
                    background: rgba(152,5,23,0.08); color: #980517;">
            <i class="fas fa-{{ match($notification->type) {
                'event'       => 'chalkboard-teacher',
                'participant' => 'clipboard-check',
                'certificate' => 'award',
                default       => 'bell'
            } }}" style="font-size: 0.9rem; color: #980517;"></i>
        </div>

        {{-- Content --}}
        <div style="flex: 1; min-width: 0;">
            <div style="font-weight: 700; color: var(--text-primary); margin-bottom: 0.2rem; font-size: 0.9375rem;">
                {{ $notification->title }}
            </div>
            <div style="color: var(--text-muted); font-size: 0.8125rem; line-height: 1.5; margin-bottom: 0.35rem; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                {{ $notification->message }}
            </div>
            <div style="font-size: 0.75rem; color: var(--text-muted);">
                {{ $notification->created_at->diffForHumans() }}
            </div>
        </div>

        {{-- Unread dot --}}
        @if(!$notification->is_read)
        <div style="width: 9px; height: 9px; background: #980517; border-radius: 50%; margin-top: 6px; flex-shrink: 0;"></div>
        @endif
    </a>
    @empty
    <div style="text-align: center; padding: 4rem 2rem; color: var(--text-muted);">
        <i class="fas fa-bell-slash" style="font-size: 3rem; opacity: 0.25; display: block; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1.125rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 0.5rem;">No notifications</h3>
        <p style="font-size: 0.875rem;">You're all caught up!</p>
    </div>
    @endforelse

    @if($notifications->hasPages())
    <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--border-color);">
        {{ $notifications->links() }}
    </div>
    @endif
</div>
@endsection
