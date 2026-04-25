@extends('layouts.app')
@section('title', 'Create Event')

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('events.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label" for="title">Event Title *</label>
                <input type="text" id="title" name="title" class="form-input" value="{{ old('title') }}" placeholder="e.g. Web Development Workshop" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Description *</label>
                <textarea id="description" name="description" class="form-input" rows="5" placeholder="Describe the event objectives, prerequisites, and agenda..." required>{{ old('description') }}</textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="start_date">Start Date & Time *</label>
                    <p style="font-size:0.75rem;color:var(--text-muted);margin-bottom:0.25rem;">Min. 7 days from now</p>
                    <input type="datetime-local" id="start_date" name="start_date" class="form-input" 
                        value="{{ old('start_date') }}" 
                        min="{{ now()->addDays(7)->format('Y-m-d\TH:i') }}"
                        required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="end_date">End Date & Time *</label>
                    <input type="datetime-local" id="end_date" name="end_date" class="form-input" value="{{ old('end_date') }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="location">Location *</label>
                    <input type="text" id="location" name="location" class="form-input" value="{{ old('location') }}" placeholder="e.g. Room A-301, Building B" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="quota">Participant Quota *</label>
                    <input type="number" id="quota" name="quota" class="form-input" value="{{ old('quota', 30) }}" min="1" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="status">Initial Status *</label>
                <select id="status" name="status" class="form-input">
                    <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft (save without submitting)</option>
                    <option value="pending_approval" {{ old('status') === 'pending_approval' ? 'selected' : '' }}>Pending Approval (submit to Head Department)</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Event Materials (Optional)</label>
                <div style="background:#f8f9fa; padding:15px; border-radius:8px; border:1px solid #e2e8f0; margin-bottom:10px;">
                    <input type="text" name="materials[0][title]" class="form-input" style="margin-bottom:10px;" placeholder="Material Title">
                    <textarea name="materials[0][description]" class="form-input" rows="2" placeholder="Material Description"></textarea>
                </div>
            </div>

            <div class="action-group mt-2" style="justify-content:flex-end;">
                <button type="submit" class="btn btn-primary btn-lg">Create Event</button>
            </div>
        </form>
    </div>
</div>
@endsection
