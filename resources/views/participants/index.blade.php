@extends('layouts.app')
@section('title', 'Manage Participants')

@section('content')
<div class="card">
    {{-- ─── Filters ─── --}}
    <div class="card-header" style="flex-wrap: wrap; gap: 0.75rem; align-items: flex-start;">
        <h3 class="card-title" style="flex-shrink: 0;">Participants</h3>
        <form method="GET" action="{{ route('participants.index') }}" style="display: flex; flex-wrap: wrap; gap: 0.5rem; flex: 1; min-width: 0; align-items: center; margin: 0;">
            <select name="event_id" class="form-input" style="flex: 1; min-width: 180px; max-width: 300px;" onchange="this.form.submit()">
                <option value="">All Events</option>
                @foreach($events as $t)
                <option value="{{ $t->id }}" {{ request('event_id') == $t->id ? 'selected' : '' }}>{{ $t->title }}</option>
                @endforeach
            </select>
            <select name="status" class="form-input" style="min-width: 120px; max-width: 160px;" onchange="this.form.submit()">
                <option value="">All Status</option>
                @foreach(['pending','accepted','rejected'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </form>
        <div style="display: flex; gap: 0.5rem; flex-shrink: 0; flex-wrap: wrap;">
            <a href="{{ route('attendance.export.pdf', request()->all()) }}" class="btn btn-sm btn-danger">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
            <a href="{{ route('attendance.export.excel', request()->all()) }}" class="btn btn-sm btn-success">
                <i class="fas fa-file-excel"></i> Excel
            </a>
        </div>
    </div>

    {{-- ─── Table ─── --}}
    <div class="table-container" style="margin: 0; border: none;">
        <table class="table">
            <thead>
                <tr>
                    <th>Participant</th>
                    <th>Event</th>
                    <th>Reg #</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($participants as $reg)
                <tr>
                    <td>
                        <div style="font-weight: 600; color: var(--text-primary);">{{ $reg->user->name }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $reg->user->email }}</div>
                    </td>
                    <td style="color: var(--text-secondary); max-width: 200px;">
                        <div style="overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                            {{ $reg->event->title }}
                        </div>
                    </td>
                    <td>
                        <code style="background: var(--bg-input); padding: 2px 6px; border-radius: 4px; font-size: 0.8rem; color: #980517;">
                            {{ $reg->participant_number ?? $reg->registration_number }}
                        </code>
                    </td>
                    <td style="color: var(--text-muted); white-space: nowrap;">{{ $reg->created_at->format('d M Y') }}</td>
                    <td><span class="badge-status badge-{{ $reg->status }}">{{ ucfirst($reg->status) }}</span></td>
                    <td>
                        @if($reg->status === 'pending')
                            <div class="action-group">
                                <form method="POST" action="{{ route('participants.updateStatus', $reg) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="accepted">
                                    <button class="btn btn-sm btn-primary" title="Accept"><i class="fas fa-check"></i></button>
                                </form>
                                <form method="POST" action="{{ route('participants.updateStatus', $reg) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="rejected">
                                    <button class="btn btn-sm btn-danger" title="Reject"><i class="fas fa-times"></i></button>
                                </form>
                            </div>
                        @elseif($reg->status === 'accepted')
                            <a href="{{ route('certificates.custom', $reg) }}" class="btn btn-sm btn-outline" title="Issue Special Certificate">
                                <i class="fas fa-award"></i> Special Cert
                            </a>
                        @else
                            <span style="color: var(--text-muted);">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 2.5rem; color: var(--text-muted);">
                        <i class="fas fa-users" style="font-size: 2rem; display: block; margin-bottom: 0.5rem; opacity: 0.3;"></i>
                        No participants found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ─── Pagination ─── --}}
    <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--border-color);">
        {{ $participants->withQueryString()->links() }}
    </div>
</div>
@endsection
