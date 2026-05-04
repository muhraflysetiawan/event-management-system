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

        <form method="POST" action="{{ route('reports.store', $event) }}" x-data="{ type: 'overall' }">
            @csrf
            
            <div class="form-group">
                <label class="form-label">Report Type *</label>
                <select name="type" x-model="type" class="form-input" required>
                    <option value="overall">Laporan Keseluruhan (Overall)</option>
                    <option value="financial">Laporan Keuangan (Financial)</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Report Title *</label>
                <input type="text" name="title" class="form-input" value="{{ old('title', 'Event Report — ' . $event->title) }}" required>
            </div>
            
            <div x-show="type === 'financial'" x-transition style="display: none; background: var(--bg-input); padding: 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); margin-bottom: 1.5rem;">
                <h4 style="margin-bottom: 1rem; color: var(--text-primary);"><i class="fas fa-file-invoice-dollar"></i> Financial Details</h4>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Budget Allocated (Rp)</label>
                        <input type="number" name="budget_allocated" class="form-input" value="{{ old('budget_allocated') }}" placeholder="e.g. 5000000">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Total Expenses (Rp)</label>
                        <input type="number" name="total_expenses" class="form-input" value="{{ old('total_expenses') }}" placeholder="e.g. 4500000">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Financial Notes</label>
                    <textarea name="financial_notes" class="form-input" rows="3" placeholder="Detail any major expenses, surplus, or deficit...">{{ old('financial_notes') }}</textarea>
                </div>
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
                <a href="{{ route('events.show', $event) }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
                <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> Save Report</button>
            </div>
        </form>
    </div>
</div>
@endsection
