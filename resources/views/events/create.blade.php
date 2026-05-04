@extends('layouts.app')
@section('title', 'Create Event')

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

            <div class="form-group">
                <label class="form-label" for="project_brief">Project Brief (for Approval) *</label>
                <textarea id="project_brief" name="project_brief" class="form-input" rows="5" placeholder="Detail the budget, speakers, and goals for the Head Department to review...">{{ old('project_brief') }}</textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="start_date">Start Date & Time *</label>
                    <input type="datetime-local" id="start_date" name="start_date" class="form-input" 
                        value="{{ old('start_date') }}" required>
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
                <label class="form-label">Target Audience *</label>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer;">
                        <input type="checkbox" name="target_audience[]" value="student" {{ in_array('student', old('target_audience', [])) ? 'checked' : '' }}> Student
                    </label>
                    <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer;">
                        <input type="checkbox" name="target_audience[]" value="lecturer" {{ in_array('lecturer', old('target_audience', [])) ? 'checked' : '' }}> Lecturer
                    </label>
                    <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer;">
                        <input type="checkbox" name="target_audience[]" value="staff" {{ in_array('staff', old('target_audience', [])) ? 'checked' : '' }}> Staff
                    </label>
                    <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer;">
                        <input type="checkbox" name="target_audience[]" value="external" {{ in_array('external', old('target_audience', [])) ? 'checked' : '' }}> External
                    </label>
                </div>
                @error('target_audience')
                    <div style="color:var(--danger); font-size:0.875rem; margin-top:0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Verification Required From (Head Roles) *</label>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap; background: #fdf2f2; padding: 1rem; border-radius: 8px; border: 1px solid #fecaca;">
                    <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer;">
                        <input type="checkbox" name="required_approval_roles[]" value="admin" {{ in_array('admin', old('required_approval_roles', [])) ? 'checked' : '' }}> Admin
                    </label>
                    <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer;">
                        <input type="checkbox" name="required_approval_roles[]" value="head_csdl" {{ in_array('head_csdl', old('required_approval_roles', [])) ? 'checked' : '' }}> Head CSDL
                    </label>
                    <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer;">
                        <input type="checkbox" name="required_approval_roles[]" value="head_baak" {{ in_array('head_baak', old('required_approval_roles', [])) ? 'checked' : '' }}> Head BAAK
                    </label>
                    <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer;">
                        <input type="checkbox" name="required_approval_roles[]" value="head_finance" {{ in_array('head_finance', old('required_approval_roles', [])) ? 'checked' : '' }}> Head Finance
                    </label>
                    <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer;">
                        <input type="checkbox" name="required_approval_roles[]" value="head_gsd" {{ in_array('head_gsd', old('required_approval_roles', [])) ? 'checked' : '' }}> Head GSD
                    </label>
                    <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer;">
                        <input type="checkbox" name="required_approval_roles[]" value="head_sis" {{ in_array('head_sis', old('required_approval_roles', [])) ? 'checked' : '' }}> Head SIS
                    </label>
                    <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer;">
                        <input type="checkbox" name="required_approval_roles[]" value="head_learning" {{ in_array('head_learning', old('required_approval_roles', [])) ? 'checked' : '' }}> Head Learning
                    </label>
                    <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer;">
                        <input type="checkbox" name="required_approval_roles[]" value="acoo" {{ in_array('acoo', old('required_approval_roles', [])) ? 'checked' : '' }}> ACOO
                    </label>
                </div>
                @error('required_approval_roles')
                    <div style="color:var(--danger); font-size:0.875rem; margin-top:0.25rem;">{{ $message }}</div>
                @enderror
                <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">Only the selected roles will be able to approve this event.</p>
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
            table_advtab: true, // Enable advanced table properties (border color, background color)
            table_cell_advtab: true,
            table_row_advtab: true,
            paste_data_images: true, // Allow copying images from clipboard (like from Word)
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
