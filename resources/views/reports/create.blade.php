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

        {{-- ─── EXISTING REPORTS SECTION ─── --}}
        @if($reports->count() > 0)
        <div style="margin-bottom: 2.5rem;">
            <h4 style="margin-bottom: 1rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-history" style="color: #980517;"></i> Submitted Reports
            </h4>
            <div class="table-container" style="margin: 0; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Feedback</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $existingReport)
                        <tr>
                            <td style="font-weight: 600;">{{ $existingReport->title }}</td>
                            <td style="font-size: 0.875rem;">{{ $existingReport->created_at->format('d M Y') }}</td>
                            <td>
                                <span class="badge-status {{ $existingReport->type === 'financial' ? 'badge-ongoing' : 'badge-published' }}" style="font-size: 0.7rem;">
                                    {{ ucfirst($existingReport->type) }}
                                </span>
                            </td>
                            <td>
                                @if($existingReport->management_feedback)
                                    <span class="badge-status badge-accepted" style="font-size: 0.7rem;"><i class="fas fa-check-circle"></i> Received</span>
                                @else
                                    <span class="badge-status badge-pending" style="font-size: 0.7rem;"><i class="fas fa-clock"></i> Pending</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('reports.show', $existingReport) }}" class="btn btn-sm btn-outline">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <hr style="margin-bottom: 2rem; border: none; border-top: 1px solid var(--border-color); opacity: 0.5;">
        @endif

        <h4 style="margin-bottom: 1.5rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-plus-circle" style="color: #980517;"></i> Add New Report
        </h4>

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
