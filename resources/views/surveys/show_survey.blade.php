@extends('layouts.app')
@section('title', 'Satisfaction Survey - ' . $event->title)

@section('content')
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header text-center">
        <h3 class="card-title">{{ $survey->title }}</h3>
        <p class="text-muted" style="margin-top: 0.5rem;">Please share your feedback to download your certificate.</p>
    </div>
    <div class="card-body">
        <form action="{{ route('surveys.submit', $event) }}" method="POST">
            @csrf
            
            @foreach($survey->questions as $index => $question)
                <div class="mb-5 p-4" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                    <p class="mb-3" style="font-weight: 600; font-size: 1.1rem;">{{ $index + 1 }}. {{ $question->question_text }}</p>
                    
                    @if($question->type === 'scale')
                        <div class="scale-options" style="display: flex; justify-content: space-between; max-width: 500px; margin: 0 auto; position: relative; padding-bottom: 2rem;">
                            @for($i = 1; $i <= 5; $i++)
                                <div style="text-align: center; flex: 1;">
                                    <label style="cursor: pointer; display: block;">
                                        <input type="radio" name="answers[{{ $question->id }}]" value="{{ $i }}" required style="width: 1.5rem; height: 1.5rem; margin-bottom: 0.5rem;">
                                        <div style="font-size: 0.9rem;">{{ $i }}</div>
                                    </label>
                                </div>
                            @endfor
                            <div style="position: absolute; bottom: 0; left: 0; right: 0; display: flex; justify-content: space-between; font-size: 0.8rem; color: var(--text-muted); padding: 0 10px;">
                                <span>Very Dissatisfied</span>
                                <span>Very Satisfied</span>
                            </div>
                        </div>
                    @else
                        <textarea name="answers[{{ $question->id }}]" class="form-control" rows="4" placeholder="Your comments here..." required></textarea>
                    @endif
                </div>
            @endforeach

            <div class="action-group" style="justify-content: center;">
                <button type="submit" class="btn btn-primary btn-lg" style="padding: 1rem 3rem; font-size: 1.2rem;">
                    Submit Survey & Get Certificate <i class="fas fa-arrow-right" style="margin-left: 10px;"></i>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
