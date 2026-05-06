<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;
use App\Models\EventRequirement;
use App\Models\Participant;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SurveyController extends Controller
{
    // --- Organizer Actions ---

    public function manageSurvey(Event $event)
    {
        $this->authorizeOrganizer($event);
        $survey = $event->survey()->with('questions')->first();
        return view('surveys.manage', compact('event', 'survey'));
    }

    public function saveSurvey(Request $request, Event $event)
    {
        $this->authorizeOrganizer($event);

        $request->validate([
            'title' => 'required|string|max:255',
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'required|string',
            'questions.*.type' => 'required|in:scale,text',
            'questions.*.is_required' => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($request, $event) {
            $survey = $event->survey()->updateOrCreate(
                ['event_id' => $event->id],
                ['title' => $request->title]
            );

            $survey->questions()->delete();

            foreach ($request->questions as $q) {
                $survey->questions()->create([
                    'question_text' => $q['text'],
                    'type' => $q['type'],
                    'is_required' => isset($q['is_required']) ? (bool)$q['is_required'] : false,
                ]);
            }
        });

        return redirect()->route('events.show', $event)
            ->with('success', 'Survey saved successfully!');
    }

    public function manageRequirements(Event $event)
    {
        $this->authorizeOrganizer($event);
        $requirements = $event->requirements;
        return view('surveys.requirements', compact('event', 'requirements'));
    }

    public function saveRequirements(Request $request, Event $event)
    {
        $this->authorizeOrganizer($event);

        $request->validate([
            'requirements' => 'nullable|array',
            'requirements.*.text' => 'required|string',
        ]);

        DB::transaction(function () use ($request, $event) {
            $event->requirements()->delete();

            if ($request->has('requirements')) {
                foreach ($request->requirements as $req) {
                    $event->requirements()->create([
                        'question_text' => $req['text'],
                        'is_required' => false, // No longer used for blocking
                    ]);
                }
            }
        });

        return redirect()->route('events.show', $event)
            ->with('success', 'Joining requirements saved successfully!');
    }

    public function viewResults(Event $event)
    {
        $this->authorizeOrganizer($event);
        $survey = $event->survey()->with(['questions.responses.user'])->firstOrFail();
        
        $totalResponses = $survey->responses()->distinct('user_id')->count();

        return view('surveys.results', compact('event', 'survey', 'totalResponses'));
    }

    public function viewReport(Event $event)
    {
        $this->authorizeOrganizer($event);
        $survey = $event->survey()->with(['questions.responses'])->firstOrFail();
        
        $questions = $survey->questions;
        $reportData = [];
        $overallScore = 0;
        $scaleQuestionCount = 0;

        foreach ($questions as $question) {
            if ($question->type === 'scale') {
                $avg = $question->responses()->avg('answer');
                $reportData[] = [
                    'question' => $question->question_text,
                    'average' => round($avg, 2),
                    'total' => $question->responses()->count(),
                ];
                $overallScore += $avg;
                $scaleQuestionCount++;
            }
        }

        $finalScore = $scaleQuestionCount > 0 ? $overallScore / $scaleQuestionCount : 0;
        $conclusion = "Good";
        if ($finalScore < 3.0) $conclusion = "Needs Improvement";
        elseif ($finalScore < 4.0) $conclusion = "Satisfactory";

        $unsatisfactory = array_filter($reportData, function($item) {
            return $item['average'] < 3.5;
        });

        return view('surveys.report', compact('event', 'survey', 'reportData', 'finalScore', 'conclusion', 'unsatisfactory'));
    }

    // --- Participant Actions (Requirements) ---

    public function showRequirements(Event $event)
    {
        $user = auth()->user();
        if ($event->participants()->where('user_id', $user->id)->exists()) {
            return redirect()->route('events.show', $event)->with('info', 'You are already registered.');
        }

        $requirements = $event->requirements;
        return view('surveys.show_requirements', compact('event', 'requirements'));
    }

    public function submitRequirements(Request $request, Event $event)
    {
        $user = auth()->user();
        $requirements = $event->requirements;
        $answers = $request->input('answers', []);
        
        $yesCount = 0;
        $noCount = 0;

        foreach ($requirements as $req) {
            $answer = $answers[$req->id] ?? 'no';
            if ($answer === 'yes') {
                $yesCount++;
            } else {
                $noCount++;
            }
        }

        // Always join the event
        Participant::create([
            'registration_number' => Participant::generateRegistrationNumber($event, $user),
            'user_id' => $user->id,
            'event_id' => $event->id,
            'status' => 'accepted',
        ]);

        $message = 'You have successfully joined the event!';
        if ($yesCount <= $noCount && $requirements->count() > 0) {
            return redirect()->route('events.show', $event)
                ->with('success', $message)
                ->with('info', 'Suggestion: Based on your answers, you might not fully meet the ideal criteria for this event, but you are still welcome to join!');
        }

        return redirect()->route('events.show', $event)
            ->with('success', $message);
    }

    // --- Participant Actions (Survey) ---

    public function showSurvey(Event $event)
    {
        $user = auth()->user();
        $survey = $event->survey()->with('questions')->firstOrFail();

        if (SurveyResponse::where('survey_id', $survey->id)->where('user_id', $user->id)->exists()) {
            return redirect()->route('certificates.index')->with('info', 'You have already completed the survey.');
        }

        return view('surveys.show_survey', compact('event', 'survey'));
    }

    public function submitSurvey(Request $request, Event $event)
    {
        $user = auth()->user();
        $survey = $event->survey()->with('questions')->firstOrFail();

        $answers = $request->input('answers', []);
        
        // Manual validation for mandatory questions
        foreach ($survey->questions as $question) {
            if ($question->is_required && empty($answers[$question->id])) {
                return back()->with('error', 'Please answer all mandatory survey questions.');
            }
        }

        DB::transaction(function () use ($answers, $survey, $user) {
            foreach ($answers as $questionId => $answer) {
                if ($answer !== null) {
                    SurveyResponse::create([
                        'survey_id' => $survey->id,
                        'user_id' => $user->id,
                        'question_id' => $questionId,
                        'answer' => $answer,
                    ]);
                }
            }
        });

        $certificate = Certificate::where('user_id', $user->id)
            ->where('event_id', $event->id)
            ->first();

        if ($certificate) {
            return redirect()->route('surveys.thank_you', ['certificate' => $certificate->id]);
        }

        return redirect()->route('certificates.index')
            ->with('success', 'Thank you for your feedback!');
    }

    public function thankYou(Certificate $certificate)
    {
        return view('surveys.thank_you', compact('certificate'));
    }

    private function authorizeOrganizer(Event $event)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && $event->created_by !== $user->id) {
            abort(403);
        }
    }
}
