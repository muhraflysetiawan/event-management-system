@extends('layouts.app')
@section('title', 'Create Report')

@section('content')

<div class="card">
    <div class="card-body">
        <div class="detail-grid mb-3">
            <div class="detail-item">
                <div class="detail-label">Event</div>
                <div class="detail-value">{{ $event->title }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Total Registered</div>
                <div class="detail-value">{{ $totalParticipants }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Total Attended</div>
                <div class="detail-value">{{ $totalAttended }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Attendance Rate</div>
                <div class="detail-value">{{ $totalParticipants > 0 ? round(($totalAttended / $totalParticipants) * 100) : 0 }}%</div>
            </div>
        </div>

        <form method="POST" action="{{ route('reports.store', $event) }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Report Title *</label>
                <input type="text" name="title" class="form-input" value="{{ old('title', 'Event Report — ' . $event->title) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Report Content *</label>
                <textarea name="content" class="form-input" rows="8" placeholder="Write your detailed report..." required>{{ old('content') }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Summary (optional)</label>
                <textarea name="summary" class="form-input" rows="3" placeholder="Brief summary...">{{ old('summary') }}</textarea>
            </div>
            <div class="action-group mt-3" style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <a href="{{ route('events.show', $event) }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> Save Report</button>
            </div>
        </form>
    </div>
</div>
@endsection
