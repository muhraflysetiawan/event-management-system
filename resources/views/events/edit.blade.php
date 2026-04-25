@extends('layouts.app')
@section('title', 'Edit Event')

@section('content')

<div class="card">
    <div class="card-body">
        <form id="update-event-form" method="POST" action="{{ route('events.update', $event) }}">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label" for="title">Event Title *</label>
                <input type="text" id="title" name="title" class="form-input" value="{{ old('title', $event->title) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Description *</label>
                <textarea id="description" name="description" class="form-input" rows="5" required>{{ old('description', $event->description) }}</textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="start_date">Start Date & Time *</label>
                    <p style="font-size:0.75rem;color:var(--text-muted);margin-bottom:0.25rem;">Min. 7 days from now (if changed)</p>
                    <input type="datetime-local" id="start_date" name="start_date" class="form-input" 
                        value="{{ old('start_date', $event->start_date->format('Y-m-d\TH:i')) }}" 
                        min="{{ now()->addDays(7)->format('Y-m-d\TH:i') }}"
                        required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="end_date">End Date & Time *</label>
                    <input type="datetime-local" id="end_date" name="end_date" class="form-input" value="{{ old('end_date', $event->end_date->format('Y-m-d\TH:i')) }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="location">Location *</label>
                    <input type="text" id="location" name="location" class="form-input" value="{{ old('location', $event->location) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="quota">Quota *</label>
                    <input type="number" id="quota" name="quota" class="form-input" value="{{ old('quota', $event->quota) }}" min="1" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="status">Status *</label>
                <p style="font-size:0.75rem;color:var(--text-muted);margin-bottom:0.25rem;">Cancellation allowed until: {{ $event->start_date->copy()->subDay()->format('d M Y, H:i') }} (H-1)</p>
                <select id="status" name="status" class="form-input">
                    @foreach(['draft','pending_approval','approved','published','ongoing','completed','cancelled'] as $s)
                        @php
                            $isDisabled = false;
                            if ($s === 'cancelled' && $event->status !== 'cancelled') {
                                $isDisabled = now()->gt($event->start_date->copy()->subDay());
                            }
                        @endphp
                        <option value="{{ $s }}" {{ old('status', $event->status) === $s ? 'selected' : '' }} {{ $isDisabled ? 'disabled' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $s)) }} {{ $isDisabled ? '(Not allowed)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>

        <div class="action-group mt-3" style="display: flex; align-items: center; justify-content: flex-end; gap: 0.75rem; flex-wrap: wrap;">
            <a href="{{ route('events.show', $event) }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" form="update-event-form" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> Update Event</button>
            
            <form method="POST" action="{{ route('events.destroy', $event) }}" onsubmit="return confirm('Are you sure you want to delete this event?')" style="margin: 0; display: inline-block;">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger" style="background: #980517; box-shadow: none;"><i class="fas fa-trash"></i> Delete Event</button>
            </form>
        </div>
    </div>
</div>
@endsection
