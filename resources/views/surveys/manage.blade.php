@extends('layouts.app')
@section('title', 'Manage Survey - ' . $event->title)

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Satisfaction Survey: {{ $event->title }}</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('surveys.save', $event) }}" method="POST" id="survey-form">
            @csrf
            
            <div class="form-group mb-4">
                <label for="title" class="form-label">Survey Title</label>
                <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $survey->title ?? 'Satisfaction Survey: ' . $event->title) }}" required>
            </div>

            <div id="questions-container">
                <label class="form-label mb-2">Survey Questions</label>
                @if(isset($survey) && $survey->questions->count() > 0)
                    @foreach($survey->questions as $index => $question)
                        <div class="question-item mb-3 p-3" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                            <div class="row">
                                <div class="col-md-8 mb-2">
                                    <input type="text" name="questions[{{ $index }}][text]" class="form-control" placeholder="Question text" value="{{ $question->question_text }}" required>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <select name="questions[{{ $index }}][type]" class="form-control">
                                        <option value="scale" {{ $question->type === 'scale' ? 'selected' : '' }}>Scale (1-5)</option>
                                        <option value="text" {{ $question->type === 'text' ? 'selected' : '' }}>Open Text</option>
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-danger remove-question" style="width: 100%;"><i class="fas fa-trash"></i></button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="question-item mb-3 p-3" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                        <div class="row">
                            <div class="col-md-8 mb-2">
                                <input type="text" name="questions[0][text]" class="form-control" placeholder="Question text" required>
                            </div>
                            <div class="col-md-3 mb-2">
                                <select name="questions[0][type]" class="form-control">
                                    <option value="scale">Scale (1-5)</option>
                                    <option value="text">Open Text</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-danger remove-question" style="width: 100%;"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <button type="button" id="add-question" class="btn btn-outline mb-4"><i class="fas fa-plus"></i> Add Question</button>

            <div class="action-group" style="justify-content: flex-end;">
                <a href="{{ route('events.show', $event) }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Survey</button>
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
        div.className = 'question-item mb-3 p-3';
        div.style.background = 'var(--bg-card)';
        div.style.border = '1px solid var(--border-color)';
        div.style.borderRadius = 'var(--radius-md)';
        div.innerHTML = `
            <div class="row">
                <div class="col-md-8 mb-2">
                    <input type="text" name="questions[${questionIndex}][text]" class="form-control" placeholder="Question text" required>
                </div>
                <div class="col-md-3 mb-2">
                    <select name="questions[${questionIndex}][type]" class="form-control">
                        <option value="scale">Scale (1-5)</option>
                        <option value="text">Open Text</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger remove-question" style="width: 100%;"><i class="fas fa-trash"></i></button>
                </div>
            </div>
        `;
        container.appendChild(div);
        questionIndex++;
        attachRemoveEvent(div.querySelector('.remove-question'));
    });

    function attachRemoveEvent(button) {
        button.addEventListener('click', function() {
            if (container.querySelectorAll('.question-item').length > 1) {
                button.closest('.question-item').remove();
            } else {
                alert('A survey must have at least one question.');
            }
        });
    }

    container.querySelectorAll('.remove-question').forEach(attachRemoveEvent);
});
</script>
@endsection
