@extends('layouts.app')
@section('title', 'Attendance List')

@section('content')

<div class="card">
    <div class="card-header">
        <h3 class="card-title"></h3>
        <span class="badge-status badge-{{ $event->status }}">{{ $participants->filter(fn($p) => $p->user->attendances->isNotEmpty())->count() }} / {{ $participants->count() }} checked in</span>
    </div>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr><th>#</th><th>Name</th><th>Email</th><th>Status</th><th>Checked In At</th><th>IP Address</th></tr>
            </thead>
            <tbody>
                @forelse($participants as $i => $participant)
                @php $att = $participant->user->attendances->first(); @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td style="font-weight:600;color:var(--text-primary);">{{ $participant->user->name }}</td>
                    <td>{{ $participant->user->email }}</td>
                    <td>
                        @if($att)
                            <span class="badge-status badge-completed">Present</span>
                        @else
                            <span class="badge-status badge-cancelled">Absent</span>
                        @endif
                    </td>
                    <td>{{ $att ? $att->checked_in_at->format('d M Y, H:i:s') : '—' }}</td>
                    <td style="color:var(--text-muted);">{{ $att->ip_address ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center" style="padding:2rem;color:var(--text-muted);">No participants accepted yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body">
        <div class="action-group mt-3" style="display: flex; justify-content: flex-end;">
            <a href="{{ route('events.show', $event) }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
    </div>
</div>
@endsection
