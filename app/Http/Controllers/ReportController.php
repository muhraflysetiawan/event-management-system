<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Event;
use App\Models\Participant;
use App\Models\Attendance;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function create(Event $event)
    {
        $totalParticipants = Participant::where('event_id', $event->id)
            ->where('status', 'accepted')->count();
        $totalAttended = Attendance::where('event_id', $event->id)->count();

        $reports = Report::where('event_id', $event->id)
            ->with(['creator', 'feedbackBy'])
            ->latest()
            ->get();

        return view('reports.create', compact('event', 'totalParticipants', 'totalAttended', 'reports'));
    }

    public function store(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:overall,financial',
            'content' => 'required|string',
            'summary' => 'nullable|string',
            'budget_allocated' => 'nullable|numeric|min:0',
            'total_expenses' => 'nullable|numeric|min:0',
            'financial_notes' => 'nullable|string',
        ]);

        $validated['event_id'] = $event->id;
        $validated['created_by'] = auth()->id();
        $validated['total_participants'] = Participant::where('event_id', $event->id)
            ->where('status', 'accepted')->count();
        $validated['total_attended'] = Attendance::where('event_id', $event->id)->count();

        $report = Report::create($validated);

        \App\Services\NotificationService::notifyReportSubmitted($report);

        return redirect()->route('reports.show', $report)
            ->with('success', 'Report created successfully!');
    }

    public function show(Report $report)
    {
        $report->load(['event', 'creator', 'feedbackBy']);
        return view('reports.show', compact('report'));
    }

    public function submitFeedback(Request $request, Report $report)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->isACOO() && !$user->isHeadDepartment()) {
            abort(403);
        }

        $request->validate([
            'management_feedback' => 'required|string',
        ]);

        $report->update([
            'management_feedback' => $request->management_feedback,
            'management_feedback_by' => $user->id,
            'management_feedback_at' => now(),
        ]);

        \App\Services\NotificationService::notifyReportFeedback($report);

        return back()->with('success', 'Feedback submitted successfully!');
    }

    public function updateSurveyReply(Request $request, Report $report)
    {
        $user = auth()->user();
        if ($report->created_by !== $user->id && !$user->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'survey_reply' => 'required|string',
        ]);

        $report->update([
            'survey_reply' => $request->survey_reply,
        ]);

        \App\Services\NotificationService::notifySurveyReply($report);

        return back()->with('success', 'Survey reply updated and participants notified!');
    }

    public function exportPdf(Report $report)
    {
        $user = auth()->user();
        $isOrganizer = $report->created_by === $user->id && !$user->isAdmin();
        
        if ($isOrganizer && !$report->management_feedback) {
            return back()->with('error', 'You can only download the report after management feedback has been received.');
        }

        $report->load(['event.participants.user', 'event.attendances.user', 'creator', 'feedbackBy']);
        
        // Fetch Survey Data
        $survey = \App\Models\Survey::where('event_id', $report->event_id)->with('questions.responses')->first();
        $surveySummary = [];
        if ($survey) {
            foreach ($survey->questions as $question) {
                if ($question->type === 'scale') {
                    $avg = $question->responses()->avg('answer');
                    $surveySummary[] = [
                        'question' => $question->question_text,
                        'average' => round($avg, 2),
                        'total' => $question->responses()->count(),
                    ];
                }
            }
        }

        $attendances = Attendance::with('user')->where('event_id', $report->event_id)->get();
        $participants = Participant::with('user')->where('event_id', $report->event_id)->get();

        $pdf = Pdf::loadView('reports.pdf', compact('report', 'attendances', 'participants', 'surveySummary'));
        return $pdf->download("report-{$report->event->title}.pdf");
    }

    public function exportExcel(Report $report)
    {
        $user = auth()->user();
        $isOrganizer = $report->created_by === $user->id && !$user->isAdmin();
        
        if ($isOrganizer && !$report->management_feedback) {
            return back()->with('error', 'You can only download the report after management feedback has been received.');
        }

        $report->load('event');
        
        if ($report->type === 'financial') {
            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\FinancialReportExport($report), 
                "financial-report-{$report->event->title}.xlsx"
            );
        }

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\EventReportExport($report->event), 
            "report-{$report->event->title}.xlsx"
        );
    }
}
