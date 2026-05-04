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

        return view('reports.create', compact('event', 'totalParticipants', 'totalAttended'));
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

        return redirect()->route('reports.show', $report)
            ->with('success', 'Report created successfully!');
    }

    public function show(Report $report)
    {
        $report->load(['event', 'creator']);
        return view('reports.show', compact('report'));
    }

    public function exportPdf(Report $report)
    {
        $report->load(['event.participants.user', 'event.attendances.user', 'creator']);
        $attendances = Attendance::with('user')->where('event_id', $report->event_id)->get();
        $participants = Participant::with('user')->where('event_id', $report->event_id)->get();

        $pdf = Pdf::loadView('reports.pdf', compact('report', 'attendances', 'participants'));
        return $pdf->download("report-{$report->event->title}.pdf");
    }

    public function exportExcel(Report $report)
    {
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
