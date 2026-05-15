@extends('layouts.app')
@section('title', 'Manage Survey - ' . $event->title)

@push('styles')
<style>
    .survey-setup-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }
    .survey-setup-header i { color: #980517; }
    .survey-setup-subtitle {
        color: #980517;
        font-size: 0.85rem;
        font-style: italic;
        margin-bottom: 1.75rem;
    }
    .survey-title-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 0.4rem;
    }
    .survey-title-input {
        width: 100%;
        padding: 0.65rem 0.85rem;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        font-family: inherit;
        font-size: 0.9rem;
        color: var(--text-primary);
        background: #fff;
        outline: none;
        margin-bottom: 1.5rem;
    }
    .survey-title-input:focus {
        border-color: #980517;
        box-shadow: 0 0 0 3px rgba(152, 5, 23, 0.08);
    }
    /* Question item card */
    .question-card {
        background: #FFFFFF;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 1.25rem 1.5rem;
        margin-bottom: 0.75rem;
    }
    .question-card:hover {
        border-color: var(--border-color-strong);
    }
    .question-layout {
        display: flex;
        gap: 1rem;
        align-items: flex-end;
    }
    .question-field-text {
        flex: 1;
        min-width: 0;
    }
    .question-field-type {
        width: 140px;
        flex-shrink: 0;
    }
    .question-field-mandatory {
        flex-shrink: 0;
        display: flex;
        align-items: center;
        padding-bottom: 0.15rem;
    }
    .question-field-delete {
        flex-shrink: 0;
        padding-bottom: 0.15rem;
    }
    .field-label {
        font-size: 0.7rem;
        font-weight: 700;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 0.35rem;
    }
    .question-input {
        width: 100%;
        padding: 0.6rem 0.75rem;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        font-family: inherit;
        font-size: 0.875rem;
        color: var(--text-primary);
        background: #fff;
        outline: none;
    }
    .question-input:focus {
        border-color: #980517;
        box-shadow: 0 0 0 3px rgba(152, 5, 23, 0.08);
    }
    .question-select {
        width: 100%;
        padding: 0.6rem 0.75rem;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        font-family: inherit;
        font-size: 0.85rem;
        color: var(--text-primary);
        background: #fff;
        outline: none;
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.5rem center;
        background-size: 1rem;
        padding-right: 2rem;
    }
    .question-select:focus {
        border-color: #980517;
        box-shadow: 0 0 0 3px rgba(152, 5, 23, 0.08);
    }
    .mandatory-label {
        display: flex;
        align-items: center;
        gap: 0.3rem;
        font-size: 0.8rem;
        color: var(--text-secondary);
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
    }
    .mandatory-label input[type="checkbox"] {
        width: 16px;
        height: 16px;
        accent-color: #980517;
    }
    .btn-delete-q {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #dc2626, #ef4444);
        border: none;
        border-radius: var(--radius-sm);
        color: #fff;
        cursor: pointer;
        font-size: 0.85rem;
    }
    .btn-delete-q:hover {
        background: linear-gradient(135deg, #b91c1c, #dc2626);
    }
    .btn-add-q {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1.5rem;
        background: linear-gradient(135deg, #980517, #c0392b);
        border: none;
        border-radius: var(--radius-sm);
        color: #fff;
        font-weight: 700;
        font-size: 0.85rem;
        cursor: pointer;
        font-family: inherit;
        margin-top: 0.25rem;
    }
    .btn-add-q:hover {
        background: linear-gradient(135deg, #7f0413, #980517);
    }
    /* Footer */
    .manage-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border-color);
        flex-wrap: wrap;
        gap: 0.75rem;
    }
    .manage-footer-right {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        align-items: center;
    }
    /* Responsive */
    @media (max-width: 768px) {
        .question-layout {
            flex-direction: column;
            align-items: stretch;
            gap: 0.75rem;
        }
        .question-field-type { width: 100%; }
        .question-field-mandatory { justify-content: flex-start; }
        .manage-footer { flex-direction: column; align-items: stretch; }
        .manage-footer-right { justify-content: flex-end; }
    }
</style>
@endpush

@section('content')
<div class="card">
    <div class="card-body">
        <div class="survey-setup-header">
            <i class="fas fa-clipboard-list"></i> Satisfaction Survey Setup
        </div>
        <div class="survey-setup-subtitle">Configure the survey participants will fill out after the event.</div>

        <form action="{{ route('surveys.save', $event) }}" method="POST" id="survey-form">
            @csrf

            <div class="survey-title-label">Survey Title</div>
            <input type="text" name="title" id="title" class="survey-title-input" value="{{ old('title', $survey->title ?? 'Satisfaction Survey: ' . $event->title) }}" required>

            <div id="questions-container">
                @if(isset($survey) && $survey->questions->count() > 0)
                    @foreach($survey->questions as $index => $question)
                        <div class="question-card">
                            <div class="question-layout">
                                <div class="question-field-text">
                                    <div class="field-label">Question Text</div>
                                    <input type="text" name="questions[{{ $index }}][text]" class="question-input" placeholder="Enter question text..." value="{{ $question->question_text }}" required>
                                </div>
                                <div class="question-field-type">
                                    <div class="field-label">Type</div>
                                    <select name="questions[{{ $index }}][type]" class="question-select">
                                        <option value="scale" {{ $question->type === 'scale' ? 'selected' : '' }}>Scale (1-5)</option>
                                        <option value="text" {{ $question->type === 'text' ? 'selected' : '' }}>Open Text</option>
                                    </select>
                                </div>
                                <div class="question-field-mandatory">
                                    <label class="mandatory-label">
                                        <input type="hidden" name="questions[{{ $index }}][is_required]" value="0">
                                        <input type="checkbox" name="questions[{{ $index }}][is_required]" value="1" {{ $question->is_required ? 'checked' : '' }}>
                                        Mandatory
                                    </label>
                                </div>
                                <div class="question-field-delete">
                                    <button type="button" class="btn-delete-q remove-question"><i class="fas fa-trash" style="color: inherit;"></i></button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="question-card">
                        <div class="question-layout">
                            <div class="question-field-text">
                                <div class="field-label">Question Text</div>
                                <input type="text" name="questions[0][text]" class="question-input" placeholder="Enter question text..." required>
                            </div>
                            <div class="question-field-type">
                                <div class="field-label">Type</div>
                                <select name="questions[0][type]" class="question-select">
                                    <option value="scale">Scale (1-5)</option>
                                    <option value="text">Open Text</option>
                                </select>
                            </div>
                            <div class="question-field-mandatory">
                                <label class="mandatory-label">
                                    <input type="hidden" name="questions[0][is_required]" value="0">
                                    <input type="checkbox" name="questions[0][is_required]" value="1" checked>
                                    Mandatory
                                </label>
                            </div>
                            <div class="question-field-delete">
                                <button type="button" class="btn-delete-q remove-question"><i class="fas fa-trash" style="color: inherit;"></i></button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <button type="button" id="add-question" class="btn-add-q"><i class="fas fa-plus" style="color: inherit;"></i> Add Question</button>

            <div class="manage-footer">
                <div></div>
                <div class="manage-footer-right">
                    @if(isset($survey))
                        <a href="{{ route('surveys.results', $event) }}" class="btn" style="background: #f59e0b; color: white; border-radius: var(--radius-sm);"><i class="fas fa-chart-pie" style="color: inherit;"></i> View Results</a>
                        <a href="{{ route('surveys.report', $event) }}" class="btn" style="background: #8b5cf6; color: white; border-radius: var(--radius-sm);"><i class="fas fa-file-medical-alt" style="color: inherit;"></i> View Report</a>
                    @endif
                    <a href="{{ route('events.show', $event) }}" class="btn btn-secondary"><i class="fas fa-arrow-left" style="color: inherit;"></i> Back</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save" style="color: inherit;"></i> Save Survey</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('questions-container');
    const addButton = document.getElementById('add-question');
    let questionIndex = {{ isset($survey) ? $survey->questions->count() : 1 }};

    addButton.addEventListener('click', function() {
        const div = document.createElement('div');
        div.className = 'question-card';
        div.innerHTML = `
            <div class="question-layout">
                <div class="question-field-text">
                    <div class="field-label">Question Text</div>
                    <input type="text" name="questions[${questionIndex}][text]" class="question-input" placeholder="Enter question text..." required>
                </div>
                <div class="question-field-type">
                    <div class="field-label">Type</div>
                    <select name="questions[${questionIndex}][type]" class="question-select">
                        <option value="scale">Scale (1-5)</option>
                        <option value="text">Open Text</option>
                    </select>
                </div>
                <div class="question-field-mandatory">
                    <label class="mandatory-label">
                        <input type="hidden" name="questions[${questionIndex}][is_required]" value="0">
                        <input type="checkbox" name="questions[${questionIndex}][is_required]" value="1" checked>
                        Mandatory
                    </label>
                </div>
                <div class="question-field-delete">
                    <button type="button" class="btn-delete-q remove-question"><i class="fas fa-trash" style="color: inherit;"></i></button>
                </div>
            </div>
        `;
        container.appendChild(div);
        questionIndex++;
        attachRemoveEvent(div.querySelector('.remove-question'));
    });

    function attachRemoveEvent(button) {
        button.addEventListener('click', function() {
            if (container.querySelectorAll('.question-card').length > 1) {
                button.closest('.question-card').remove();
            } else {
                alert('A survey must have at least one question.');
            }
        });
    }

    container.querySelectorAll('.remove-question').forEach(attachRemoveEvent);
});
</script>
@endsection
