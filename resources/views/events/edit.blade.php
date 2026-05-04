@extends('layouts.app')
@section('title', 'Edit Event')

@push('styles')
<style>
    /* Add a subtle background behind the editor to make the A4 paper pop */
    .tox-tinymce {
        border-radius: var(--radius-sm) !important;
        border: 1px solid var(--border-color) !important;
        box-shadow: var(--shadow-sm);
    }
    .tox-editor-header {
        border-bottom: 1px solid var(--border-color) !important;
        box-shadow: none !important;
    }
</style>
@endpush

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

            <div class="form-group">
                <label class="form-label" for="project_brief">Project Brief (for Approval) *</label>
                <textarea id="project_brief" name="project_brief" class="form-input" rows="5" required>{{ old('project_brief', $event->project_brief) }}</textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="start_date">Start Date & Time *</label>
                    <input type="datetime-local" id="start_date" name="start_date" class="form-input" 
                        value="{{ old('start_date', $event->start_date->format('Y-m-d\TH:i')) }}" required>
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
                <label class="form-label">Target Audience *</label>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    @php $ta = old('target_audience', $event->target_audience ?? []); @endphp
                    <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer;">
                        <input type="checkbox" name="target_audience[]" value="student" {{ in_array('student', $ta) ? 'checked' : '' }}> Student
                    </label>
                    <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer;">
                        <input type="checkbox" name="target_audience[]" value="lecturer" {{ in_array('lecturer', $ta) ? 'checked' : '' }}> Lecturer
                    </label>
                    <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer;">
                        <input type="checkbox" name="target_audience[]" value="staff" {{ in_array('staff', $ta) ? 'checked' : '' }}> Staff
                    </label>
                    <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer;">
                        <input type="checkbox" name="target_audience[]" value="external" {{ in_array('external', $ta) ? 'checked' : '' }}> External
                    </label>
                </div>
                @error('target_audience')
                    <div style="color:var(--danger); font-size:0.875rem; margin-top:0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Verification Required From (Head Roles) *</label>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap; background: #fdf2f2; padding: 1rem; border-radius: 8px; border: 1px solid #fecaca;">
                    @php $ra = old('required_approval_roles', $event->required_approval_roles ?? []); @endphp
                    <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer;">
                        <input type="checkbox" name="required_approval_roles[]" value="admin" {{ in_array('admin', $ra) ? 'checked' : '' }}> Admin
                    </label>
                    <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer;">
                        <input type="checkbox" name="required_approval_roles[]" value="head_csdl" {{ in_array('head_csdl', $ra) ? 'checked' : '' }}> Head CSDL
                    </label>
                    <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer;">
                        <input type="checkbox" name="required_approval_roles[]" value="head_baak" {{ in_array('head_baak', $ra) ? 'checked' : '' }}> Head BAAK
                    </label>
                    <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer;">
                        <input type="checkbox" name="required_approval_roles[]" value="head_finance" {{ in_array('head_finance', $ra) ? 'checked' : '' }}> Head Finance
                    </label>
                    <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer;">
                        <input type="checkbox" name="required_approval_roles[]" value="head_gsd" {{ in_array('head_gsd', $ra) ? 'checked' : '' }}> Head GSD
                    </label>
                    <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer;">
                        <input type="checkbox" name="required_approval_roles[]" value="head_sis" {{ in_array('head_sis', $ra) ? 'checked' : '' }}> Head SIS
                    </label>
                    <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer;">
                        <input type="checkbox" name="required_approval_roles[]" value="head_learning" {{ in_array('head_learning', $ra) ? 'checked' : '' }}> Head Learning
                    </label>
                    <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer;">
                        <input type="checkbox" name="required_approval_roles[]" value="acoo" {{ in_array('acoo', $ra) ? 'checked' : '' }}> ACOO
                    </label>
                </div>
                @error('required_approval_roles')
                    <div style="color:var(--danger); font-size:0.875rem; margin-top:0.25rem;">{{ $message }}</div>
                @enderror
                <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">Only the selected roles will be able to approve this event.</p>
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
                            if ($s === 'approved' && $event->status !== 'approved') {
                                if (!in_array(auth()->user()->role->slug ?? '', ['admin', 'head_csdl', 'acoo'])) {
                                    $isDisabled = true;
                                }
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
            <a href="{{ route('events.show', $event) }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
            <button type="submit" form="update-event-form" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> Update Event</button>
            
            <form method="POST" action="{{ route('events.destroy', $event) }}" onsubmit="return confirm('Are you sure you want to delete this event?')" style="margin: 0; display: inline-block;">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger" style="background: #980517; box-shadow: none;"><i class="fas fa-trash"></i> Delete Event</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        tinymce.init({
            selector: '#project_brief',
            plugins: 'preview importcss searchreplace autolink directionality visualblocks visualchars fullscreen image link media table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons',
            menubar: 'file edit view insert format tools table help',
            toolbar: 'undo redo | fontfamily fontsize | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | outdent indent | numlist bullist | forecolor backcolor removeformat | pagebreak | image media table link | fullscreen preview print',
            toolbar_sticky: true,
            image_advtab: true,
            table_advtab: true, // Enable advanced table properties
            table_cell_advtab: true,
            table_row_advtab: true,
            paste_data_images: true, // Allow copying images from clipboard
            height: 800,
            promotion: false,
            branding: false,
            content_style: `
                html {
                    background: #e2e8f0;
                    padding: 20px 0;
                }
                body {
                    background: #fff;
                    width: 210mm;
                    min-height: 297mm;
                    margin: 0 auto !important;
                    padding: 2cm !important;
                    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                    font-family: 'Arial', sans-serif;
                    font-size: 11pt;
                    color: #333 !important;
                    caret-color: #000 !important; /* Force black cursor */
                    line-height: 1.5;
                    box-sizing: border-box;
                }
                table { border-collapse: collapse; width: 100%; }
                td, th { border: 1px solid #ccc; padding: 8px; }
            `,
            setup: function(editor) {
                editor.on('change', function() {
                    editor.save(); // Sync the underlying textarea automatically
                });
            }
        });
    });
</script>
@endpush
