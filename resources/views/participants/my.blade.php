@extends('layouts.app')
@section('title', 'My Participants')

@section('content')
<div class="card" style="background:#FFFFFF;">
    <div class="table-container">
        <table class="table" style="background:#FFFFFF; font-size:1.0625rem;">
            <thead>
                <tr><th>Event</th><th>Reg Number</th><th>Date</th><th>Event Date</th><th>Status</th></tr>
            </thead>
            <tbody>
                @forelse($participants as $reg)
                <tr>
                    <td>
                        <a href="{{ route('events.show', $reg->event) }}" class="link" style="font-weight:600;">
                            {{ $reg->event->title }}
                        </a>
                    </td>
                    <td><code style="color:var(--primary-400);">{{ $reg->participant_number }}</code></td>
                    <td>{{ $reg->created_at->format('d M Y') }}</td>
                    <td>{{ $reg->event->start_date->format('d M Y, H:i') }}</td>
                    <td><span class="badge-status badge-{{ $reg->status }}">{{ ucfirst($reg->status) }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding:3rem;">
                        <div class="empty-state">
                            <i class="fas fa-clipboard"></i>
                            <h3>No participants yet</h3>
                            <p>Browse available events to register</p>
                             <a href="{{ route('events.index') }}" class="btn btn-primary mt-2">Browse Events</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="pagination-wrapper">{{ $participants->links() }}</div>
@endsection
