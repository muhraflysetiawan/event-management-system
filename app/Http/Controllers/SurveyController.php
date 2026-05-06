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
        ]);

        DB::transaction(function () use ($request, $event) {
            $survey = $event->survey()->updateOrCreate(
                ['event_id' => $event->id],
                ['title' => $request->title]
            );

            // Simple way: delete old questions and create new ones
            // In a production app, we might want to keep IDs to preserve responses, 
            // but for this task, a fresh start is easier.
            $survey->questions()->delete();

            foreach ($request->questions as $q) {
                $survey->questions()->create([
                    'question_text' => $q['text'],
                    'type' => $q['type'],
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
            'requirements.*' => 'required|string',
        ]);

        DB::transaction(function () use ($request, $event) {
            $event->requirements()->delete();

            if ($request->has('requirements')) {
                foreach ($request->requirements as $text) {
                    $event->requirements()->create([
                        'question_text' => $text,
                    ]);
                }
            }
        });

        return redirect()->route('events.show', $event)
            ->with('success', 'Joining requirements saved successfully!');
    }

    // --- Participant Actions (Requirements) ---

    public function showRequirements(Event $event)
    {
        $user = auth()->user();
        
        // Check if already registered
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
        
        foreach ($requirements as $req) {
            if (!isset($answers[$req->id]) || $answers[$req->id] !== 'yes') {
                return redirect()->route('events.show', $event)
                    ->with('error', 'You do not meet the requirements to join this event.');
            }
        }

        // All requirements met, join the event
        Participant::create([
            'registration_number' => Participant::generateRegistrationNumber($event, $user),
            'user_id' => $user->id,
            'event_id' => $event->id,
            'status' => 'accepted',
        ]);

        return redirect()->route('events.show', $event)
            ->with('success', 'You have successfully joined the event!');
    }

    // --- Participant Actions (Survey) ---

    public function showSurvey(Event $event)
    {
        $user = auth()->user();
        $survey = $event->survey()->with('questions')->firstOrFail();

        // Check if already filled
        if (SurveyResponse::where('survey_id', $survey->id)->where('user_id', $user->id)->exists()) {
            return redirect()->route('certificates.index')->with('info', 'You have already completed the survey.');
        }

        return view('surveys.show_survey', compact('event', 'survey'));
    }

    public function submitSurvey(Request $request, Event $event)
    {
        $user = auth()->user();
        $survey = $event->survey()->with('questions')->firstOrFail();

        $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required',
        ]);

        DB::transaction(function () use ($request, $survey, $user) {
            foreach ($request->answers as $questionId => $answer) {
                SurveyResponse::create([
                    'survey_id' => $survey->id,
                    'user_id' => $user->id,
                    'question_id' => $questionId,
                    'answer' => $answer,
                ]);
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
