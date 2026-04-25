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
            'content' => 'required|string',
            'summary' => 'nullable|string',
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

    public function exportCsv(Report $report)
    {
        $report->load('event');
        $attendances = Attendance::with('user')->where('event_id', $report->event_id)->get();
        $participants = Participant::with('user')->where('event_id', $report->event_id)->get();

        $filename = "report-{$report->event->title}.csv";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($report, $attendances, $participants) {
            $file = fopen('php://output', 'w');

            // Report info
            fputcsv($file, ['Event Report']);
            fputcsv($file, ['Title', $report->title]);
            fputcsv($file, ['Event', $report->event->title]);
            fputcsv($file, ['Date', $report->event->start_date->format('Y-m-d')]);
            fputcsv($file, ['Total Participants', $report->total_participants]);
            fputcsv($file, ['Total Attended', $report->total_attended]);
            fputcsv($file, []);

            // Participants
            fputcsv($file, ['Participants']);
            fputcsv($file, ['Name', 'Email', 'Participant Number', 'Status']);
            foreach ($participants as $reg) {
                fputcsv($file, [$reg->user->name, $reg->user->email, $reg->participant_number, $reg->status]);
            }
            fputcsv($file, []);

            // Attendance
            fputcsv($file, ['Attendance']);
            fputcsv($file, ['Name', 'Email', 'Checked In At']);
            foreach ($attendances as $att) {
                fputcsv($file, [$att->user->name, $att->user->email, $att->checked_in_at->format('Y-m-d H:i:s')]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
