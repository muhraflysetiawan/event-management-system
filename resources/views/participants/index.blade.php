@extends('layouts.app')
@section('title', 'Manage Participants')

@section('content')
<div class="card">
    <div class="card-body">
        <form method="GET" action="{{ route('participants.index') }}" style="display:flex; flex-wrap:wrap; gap:0.75rem; align-items:center; margin-bottom: 1.5rem;">
            <div style="flex:1; min-width:240px;">
                <select name="event_id" class="form-input" style="width:100%;" onchange="this.form.submit()">
                    <option value="">All Events</option>
                    @foreach($events as $t)
                    <option value="{{ $t->id }}" {{ request('event_id') == $t->id ? 'selected' : '' }}>{{ $t->title }}</option>
                    @endforeach
                </select>
            </div>
            <div style="flex:1; min-width:140px;">
                <select name="status" class="form-input" style="width:100%;" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    @foreach(['pending','accepted','rejected'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
        </form>

        <div style="display:flex; justify-content:flex-end; gap:0.75rem; margin-bottom:1.5rem;">
            <a href="{{ route('attendance.export.pdf', request()->all()) }}" class="btn btn-outline" style="border-color:#ef4444; color:#ef4444;">
                <i class="fas fa-file-pdf"></i> Export Attendance (PDF)
            </a>
            <a href="{{ route('attendance.export.excel', request()->all()) }}" class="btn btn-outline" style="border-color:#10b981; color:#10b981;">
                <i class="fas fa-file-excel"></i> Export Attendance (Excel)
            </a>
        </div>

        <div class="table-container" style="border-top: 1px solid var(--border-color); padding-top: 0.75rem;">
            <table class="table">
            <thead>
                <tr><th>Student</th><th>Event</th><th>Reg Number</th><th>Date</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($participants as $reg)
                <tr>
                    <td>
                        <div style="font-weight:600;color:var(--text-primary);">{{ $reg->user->name }}</div>
                        <div style="font-size:0.75rem;color:var(--text-muted);">{{ $reg->user->email }}</div>
                    </td>
                    <td>{{ $reg->event->title }}</td>
                    <td><code style="color:var(--primary-400);">{{ $reg->participant_number }}</code></td>
                    <td>{{ $reg->created_at->format('d M Y') }}</td>
                    <td><span class="badge-status badge-{{ $reg->status }}">{{ ucfirst($reg->status) }}</span></td>
                    <td>
                        @if($reg->status === 'pending')
                        <div class="action-group">
                            <form method="POST" action="{{ route('participants.updateStatus', $reg) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="accepted">
                                <button class="btn btn-sm btn-success" title="Accept"><i class="fas fa-check"></i></button>
                            </form>
                            <form method="POST" action="{{ route('participants.updateStatus', $reg) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="rejected">
                                <button class="btn btn-sm btn-danger" title="Reject"><i class="fas fa-times"></i></button>
                            </form>
                        </div>
                        @elseif($reg->status === 'accepted')
                        <div class="action-group">
                            <a href="{{ route('certificates.custom', $reg) }}" class="btn btn-sm btn-primary" title="Issue Special Certificate">
                                <i class="fas fa-award"></i> Special Cert
                            </a>
                        </div>
                        @else
                        <span style="color:var(--text-muted);">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center" style="padding:2rem;color:var(--text-muted);">No participants found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="pagination-wrapper">{{ $participants->withQueryString()->links() }}    </div>
</div>
@endsection
