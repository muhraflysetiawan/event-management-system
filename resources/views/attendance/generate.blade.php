@extends('layouts.app')
@section('title', 'Attendance Management')

@section('content')
<div class="card mb-4">
    <div class="card-body">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h2 style="margin-bottom: 0.5rem; font-weight: 800; font-size: 1.5rem; color: #980517;">QR Attendance Status</h2>
            </div>
            <div style="display: flex; align-items: center; gap: 1rem; background: rgba(152, 5, 23, 0.05); padding: 0.5rem 1.25rem; border-radius: 50px; border: 1px solid rgba(152, 5, 23, 0.1);">
                <span style="font-size: 0.85rem; font-weight: 800; color: #980517; letter-spacing: 0.5px;">{{ $event->is_attendance_open ? 'ATTENDANCE IS ACTIVE' : 'ATTENDANCE IS CLOSED' }}</span>
                <form method="POST" action="{{ route('attendance.toggle', $event) }}">
                    @csrf
                    <input type="hidden" name="status" value="{{ $event->is_attendance_open ? 'close' : 'open' }}">
                    <button type="submit" style="background: {{ $event->is_attendance_open ? '#980517' : '#d1d5db' }}; width: 60px; height: 30px; border-radius: 20px; border: 2px solid {{ $event->is_attendance_open ? '#980517' : '#9ca3af' }}; position: relative; cursor: pointer; transition: all 0.3s; box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);">
                        <div style="width: 22px; height: 22px; background: white; border-radius: 50%; position: absolute; top: 2px; left: {{ $event->is_attendance_open ? '32px' : '2px' }}; transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55); box-shadow: 0 2px 4px rgba(0,0,0,0.2);"></div>
                    </button>
                </form>
            </div>
        </div>

        <div class="qr-display" style="text-align: center; opacity: {{ $event->is_attendance_open ? '1' : '0.3' }};">
            <h3 style="font-weight:700;margin-bottom:0.5rem;">Scan this QR Code</h3>
            
            <div style="background:white;display:inline-block;padding:1.5rem;border-radius:var(--radius-lg);margin:1.5rem 0; border: 1px solid var(--border-color);">
                @if($event->qr_token)
                    {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(250)->generate(request()->getSchemeAndHttpHost() . '/attendance/checkin?token=' . $event->qr_token) !!}
                @else
                    <div style="width: 250px; height: 250px; display: flex; align-items: center; justify-content: center; background: #f3f4f6; color: #9ca3af;">
                        QR will appear when opened
                    </div>
                @endif
            </div>

            <div style="margin-top:1rem;padding:1rem;background:var(--bg-input);border-radius:var(--radius-sm);border:1px solid var(--border-color);">
                <div style="font-size:0.75rem;color:var(--text-muted);margin-bottom:0.375rem;">Check-in URL</div>
                <code style="font-size:0.75rem;color:var(--primary-400);word-break:break-all;">{{ $event->qr_token ? request()->getSchemeAndHttpHost() . '/attendance/checkin?token=' . $event->qr_token : 'Not available' }}</code>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3 class="card-title">Attendance List</h3>
        <span class="badge-status badge-approved" style="font-size: 0.8rem;">
            {{ $participants->filter(fn($p) => $p->user->attendances->isNotEmpty())->count() }} / {{ $participants->count() }} Present
        </span>
    </div>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr><th>#</th><th>Name</th><th>Email</th><th>Status</th><th>Checked In At</th></tr>
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
                    <td>{{ $att ? $att->checked_in_at->format('d M Y, H:i') : '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center" style="padding:2rem;color:var(--text-muted);">No accepted participants yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer" style="display: flex; justify-content: flex-end; padding: 1.5rem;">
        <a href="{{ route('events.show', $event) }}" class="btn btn-secondary">Back to Event Details</a>
    </div>
</div>
@endsection
