@extends('layouts.app')
@section('title', 'Attendance List')

@section('content')

<div class="card">
    <div class="card-header">
        <h3 class="card-title"></h3>
        <span class="badge-status badge-{{ $event->status }}">{{ $attendances->count() }} checked in</span>
    </div>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr><th>#</th><th>Name</th><th>Email</th><th>Checked In At</th><th>IP Address</th></tr>
            </thead>
            <tbody>
                @forelse($attendances as $i => $att)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td style="font-weight:600;color:var(--text-primary);">{{ $att->user->name }}</td>
                    <td>{{ $att->user->email }}</td>
                    <td>{{ $att->checked_in_at->format('d M Y, H:i:s') }}</td>
                    <td style="color:var(--text-muted);">{{ $att->ip_address ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center" style="padding:2rem;color:var(--text-muted);">No attendance records yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body">
        <div class="action-group mt-3" style="display: flex; justify-content: flex-end;">
            <a href="{{ route('events.show', $event) }}" class="btn btn-secondary">Cancel</a>
        </div>
    </div>
</div>
@endsection
