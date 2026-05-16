@extends('layouts.app')
@section('title', 'Edit Event')

@push('styles')
@include('partials._event-form-styles')
<style>
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
        <form id="update-event-form" method="POST" action="{{ route('events.update', $event) }}" enctype="multipart/form-data">
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
                <label class="form-label" for="project_brief_type">Project Brief Format *</label>
                <select id="project_brief_type" name="project_brief_type" class="form-input" onchange="toggleProjectBriefType()">
                    <option value="text" {{ old('project_brief_type', $event->project_brief_type ?? 'text') === 'text' ? 'selected' : '' }}>Create Manually (Text Editor)</option>
                    <option value="pdf" {{ old('project_brief_type', $event->project_brief_type) === 'pdf' ? 'selected' : '' }}>Upload Document Format PDF</option>
                </select>
            </div>

            <div class="form-group" id="brief_text_container" style="{{ old('project_brief_type', $event->project_brief_type ?? 'text') === 'text' ? '' : 'display:none;' }}">
                <label class="form-label" for="project_brief">Project Brief Content *</label>
                <textarea id="project_brief" name="project_brief" class="form-input" rows="5">{{ old('project_brief', $event->project_brief) }}</textarea>
            </div>

            <div class="form-group" id="brief_pdf_container" style="{{ old('project_brief_type', $event->project_brief_type) === 'pdf' ? '' : 'display:none;' }}">
                <label class="form-label" for="project_brief_pdf">Upload Project Brief (PDF) *</label>
                <input type="file" id="project_brief_pdf" name="project_brief_pdf" class="form-input" accept="application/pdf">
                @if($event->project_brief_pdf)
                    <p style="font-size:0.75rem; color:var(--text-muted); margin-top:0.5rem;">Current PDF: <a href="{{ route('events.projectBriefPdf', $event) }}" target="_blank" style="color:#980517; font-weight:600;">View PDF</a> (Upload new file to replace)</p>
                @endif
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="start_date">Start Date & Time *</label>
                    <input type="datetime-local" id="start_date" name="start_date" class="form-input"
                        value="{{ old('start_date', $event->start_date->format('Y-m-d\TH:i')) }}" 
                        min="{{ $event->start_date->isPast() ? $event->start_date->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i') }}"
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
                <label class="form-label">Target Audience *</label>
                @php $ta = old('target_audience', $event->target_audience ?? []); @endphp
                <div class="checkbox-group">
                    <label class="checkbox-item"><input type="checkbox" name="target_audience[]" value="student" {{ in_array('student', $ta) ? 'checked' : '' }}> Student</label>
                    <label class="checkbox-item"><input type="checkbox" name="target_audience[]" value="lecturer" {{ in_array('lecturer', $ta) ? 'checked' : '' }}> Lecturer</label>
                    <label class="checkbox-item"><input type="checkbox" name="target_audience[]" value="staff" {{ in_array('staff', $ta) ? 'checked' : '' }}> Staff</label>
                    <label class="checkbox-item"><input type="checkbox" name="target_audience[]" value="external" {{ in_array('external', $ta) ? 'checked' : '' }}> External</label>
                </div>
                @error('target_audience')
                    <div style="color:#dc2626; font-size:0.8rem; margin-top:0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Verification Required From (Head Roles) *</label>
                @php $ra = old('required_approval_roles', $event->required_approval_roles ?? []); @endphp
                <div class="approval-box">
                    <div class="checkbox-group">
                        <label class="checkbox-item"><input type="checkbox" name="required_approval_roles[]" value="admin" {{ in_array('admin', $ra) ? 'checked' : '' }}> Admin</label>
                        <label class="checkbox-item"><input type="checkbox" name="required_approval_roles[]" value="head_csdl" {{ in_array('head_csdl', $ra) ? 'checked' : '' }}> Head CSDL</label>
                        <label class="checkbox-item"><input type="checkbox" name="required_approval_roles[]" value="head_baak" {{ in_array('head_baak', $ra) ? 'checked' : '' }}> Head BAAK</label>
                        <label class="checkbox-item"><input type="checkbox" name="required_approval_roles[]" value="head_finance" {{ in_array('head_finance', $ra) ? 'checked' : '' }}> Head Finance</label>
                        <label class="checkbox-item"><input type="checkbox" name="required_approval_roles[]" value="head_gsd" {{ in_array('head_gsd', $ra) ? 'checked' : '' }}> Head GSD</label>
                        <label class="checkbox-item"><input type="checkbox" name="required_approval_roles[]" value="head_sis" {{ in_array('head_sis', $ra) ? 'checked' : '' }}> Head SIS</label>
                        <label class="checkbox-item"><input type="checkbox" name="required_approval_roles[]" value="head_learning" {{ in_array('head_learning', $ra) ? 'checked' : '' }}> Head Learning</label>
                        <label class="checkbox-item"><input type="checkbox" name="required_approval_roles[]" value="acoo" {{ in_array('acoo', $ra) ? 'checked' : '' }}> ACOO</label>
                    </div>
                </div>
                @error('required_approval_roles')
                    <div style="color:#dc2626; font-size:0.8rem; margin-top:0.25rem;">{{ $message }}</div>
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

            {{-- ─── SURVEY SECTION ─── --}}
            <div class="event-section">
                <div class="event-section-header"><i class="fas fa-poll"></i> Satisfaction Survey Setup</div>
                <div class="event-section-subtitle">Configure the survey participants will fill out after the event.</div>

                <div class="form-group">
                    <div class="sq-label">Survey Title</div>
                    <input type="text" name="survey_title" id="survey_title" class="sq-input" value="{{ old('survey_title', $survey->title ?? '') }}" placeholder="e.g. Event Feedback Survey">
                </div>

                <div x-data="{
                    questions: {{ json_encode(old('survey_questions', $survey ? $survey->questions->map(fn($q) => ['text' => $q->question_text, 'type' => $q->type, 'is_required' => $q->is_required]) : [['text' => '', 'type' => 'scale', 'is_required' => 1]])) }},
                    addQuestion() { this.questions.push({ text: '', type: 'scale', is_required: 1 }); },
                    removeQuestion(index) { if (this.questions.length > 1) this.questions.splice(index, 1); }
                }">
                    <template x-for="(question, index) in questions" :key="index">
                        <div class="sq-card">
                            <div class="sq-layout">
                                <div class="sq-field-text">
                                    <div class="sq-label">Question Text</div>
                                    <input type="text" :name="`survey_questions[${index}][text]`" x-model="question.text" class="sq-input" placeholder="e.g. How satisfied were you with the speaker?" required>
                                </div>
                                <div class="sq-field-type">
                                    <div class="sq-label">Type</div>
                                    <select :name="`survey_questions[${index}][type]`" x-model="question.type" class="sq-select">
                                        <option value="scale">Scale (1-5)</option>
                                        <option value="text">Open Text</option>
                                    </select>
                                </div>
                                <div class="sq-field-mandatory">
                                    <label class="sq-mandatory">
                                        <input type="hidden" :name="`survey_questions[${index}][is_required]`" value="0">
                                        <input type="checkbox" :name="`survey_questions[${index}][is_required]`" value="1" x-model="question.is_required"> Mandatory
                                    </label>
                                </div>
                                <div class="sq-field-delete">
                                    <button type="button" @click="removeQuestion(index)" class="sq-btn-delete"><i class="fas fa-trash" style="color:inherit;"></i></button>
                                </div>
                            </div>
                        </div>
                    </template>
                    <button type="button" @click="addQuestion()" class="sq-btn-add"><i class="fas fa-plus" style="color:inherit;"></i> Add Survey Question</button>
                </div>
            </div>

            {{-- ─── REQUIREMENTS SECTION ─── --}}
            <div class="event-section">
                <div class="event-section-header"><i class="fas fa-tasks"></i> Joining Requirements</div>
                <div class="event-section-subtitle">Questions participants must answer before they can join the event.</div>

                <div x-data="{
                    requirements: {{ json_encode(old('requirements', $requirements->count() > 0 ? $requirements->map(fn($r) => ['text' => $r->question_text]) : [['text' => '']])) }},
                    addReq() { this.requirements.push({ text: '' }); },
                    removeReq(index) { this.requirements.splice(index, 1); }
                }">
                    <template x-for="(req, index) in requirements" :key="index">
                        <div class="req-row">
                            <input type="text" :name="`requirements[${index}][text]`" x-model="req.text" class="sq-input" placeholder="e.g. Do you have a laptop?" required>
                            <button type="button" @click="removeReq(index)" class="sq-btn-delete"><i class="fas fa-trash" style="color:inherit;"></i></button>
                        </div>
                    </template>
                    <button type="button" @click="addReq()" class="sq-btn-add"><i class="fas fa-plus" style="color:inherit;"></i> Add Requirement</button>
                </div>
            </div>
        </form>

        <div class="action-group mt-3" style="display: flex; align-items: center; justify-content: flex-end; gap: 0.75rem; flex-wrap: wrap;">
            <a href="{{ route('events.show', $event) }}" class="btn btn-secondary"><i class="fas fa-arrow-left" style="color:inherit;"></i> Back</a>
            <button type="submit" form="update-event-form" class="btn btn-primary btn-lg"><i class="fas fa-save" style="color:inherit;"></i> Update Event</button>

            <form method="POST" action="{{ route('events.destroy', $event) }}" onsubmit="return confirm('Are you sure you want to delete this event?')" style="margin: 0; display: inline-block;">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger" style="background: #980517; box-shadow: none;"><i class="fas fa-trash" style="color:inherit;"></i> Delete Event</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js"></script>
<script>
    function toggleProjectBriefType() {
        var type = document.getElementById('project_brief_type').value;
        if (type === 'text') {
            document.getElementById('brief_text_container').style.display = 'block';
            document.getElementById('brief_pdf_container').style.display = 'none';
        } else {
            document.getElementById('brief_text_container').style.display = 'none';
            document.getElementById('brief_pdf_container').style.display = 'block';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        tinymce.init({
            selector: '#project_brief',
            plugins: 'preview importcss searchreplace autolink directionality visualblocks visualchars fullscreen image link media table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons',
            menubar: 'file edit view insert format tools table help',
            toolbar: 'undo redo | fontfamily fontsize | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | outdent indent | numlist bullist | forecolor backcolor removeformat | pagebreak | image media table link | fullscreen preview print',
            toolbar_sticky: true,
            image_advtab: true,
            table_advtab: true,
            table_cell_advtab: true,
            table_row_advtab: true,
            paste_data_images: true,
            height: 800,
            promotion: false,
            branding: false,
            content_style: `
                html { background: #e2e8f0; padding: 20px 0; }
                body { background: #fff; width: 210mm; min-height: 297mm; margin: 0 auto !important; padding: 2cm !important; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); font-family: 'Arial', sans-serif; font-size: 11pt; color: #333 !important; caret-color: #000 !important; line-height: 1.5; box-sizing: border-box; }
                table { border-collapse: collapse; width: 100%; }
                td, th { border: 1px solid #ccc; padding: 8px; }
            `,
            setup: function(editor) {
                editor.on('change', function() { editor.save(); });
            }
        });
    });
</script>
@endpush
