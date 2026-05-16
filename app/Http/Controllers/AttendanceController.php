<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\Participant;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use App\Exports\AttendanceExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class AttendanceController extends Controller
{
    public function exportPdf(Request $request)
    {
        $eventId = $request->get('event_id');
        $query = Attendance::with(['user', 'event']);

        if ($eventId) {
            $query->where('event_id', $eventId);
            $event = Event::find($eventId);
            $title = "Attendance List - " . ($event ? $event->title : 'Event Not Found');
        } else {
            $title = "Attendance List - All Events";
        }

        $attendances = $query->latest()->get();

        $pdf = Pdf::loadView('exports.attendance_pdf', compact('attendances', 'title'));
        return $pdf->download('attendance-list.pdf');
    }

    public function exportExcel(Request $request)
    {
        $eventId = $request->get('event_id');
        return Excel::download(new AttendanceExport($eventId), 'attendance-list.xlsx');
    }

    public function generate(Event $event)
    {
        $participants = Participant::with(['user', 'user.attendances' => function($q) use ($event) {
            $q->where('event_id', $event->id);
        }])
        ->where('event_id', $event->id)
        ->where('status', 'accepted')
        ->get();

        return view('attendance.generate', compact('event', 'participants'));
    }

    public function toggleStatus(Request $request, Event $event)
    {
        $status = $request->input('status'); // 'open' or 'close'

        if ($status === 'open') {
            $token = $event->qr_token ?: Str::upper(Str::random(5));
            $event->update([
                'is_attendance_open' => true,
                'qr_token' => $token,
                'qr_expires_at' => null,
            ]);

            $qrUrl = $request->getSchemeAndHttpHost() . '/attendance/checkin?token=' . $token;

            return back()->with([
                'qr_generated' => true,
                'qr_url' => $qrUrl,
                'qr_token' => $token,
                'success' => 'Attendance is now OPEN.',
            ]);
        } else {
            $event->update([
                'is_attendance_open' => false,
            ]);
            return back()->with('success', 'Attendance is now CLOSED.');
        }
    }

    public function showScanner()
    {
        $openEvents = Event::where('is_attendance_open', true)->get();
        return view('attendance.scan', compact('openEvents'));
    }

    public function checkinForm(Request $request)
    {
        $token = $request->get('token');
        $event = Event::where('qr_token', $token)->first();

        if (!$event) {
            return view('attendance.result', ['success' => false, 'message' => 'Invalid QR code.']);
        }

        if (!$event->is_attendance_open || $event->status === 'completed') {
            return view('attendance.result', ['success' => false, 'message' => 'Attendance is currently CLOSED or the event has ended.']);
        }

        return view('attendance.confirm', compact('event', 'token'));
    }

    public function checkIn(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $user = auth()->user();
        $event = Event::where('qr_token', $request->token)->first();

        if (!$event) {
            return back()->with('error', 'Invalid QR code.');
        }

        if (!$event->is_attendance_open || $event->status === 'completed') {
            return back()->with('error', 'Attendance is currently CLOSED or the event has ended.');
        }

        // Check if user is registered and accepted
        $isRegistered = Participant::where('user_id', $user->id)
            ->where('event_id', $event->id)
            ->where('status', 'accepted')
            ->exists();

        if (!$isRegistered) {
            return view('attendance.result', [
                'success' => false,
                'message' => 'You are not registered or not accepted for this event.',
            ]);
        }

        // Check duplicate
        $alreadyCheckedIn = Attendance::where('user_id', $user->id)
            ->where('event_id', $event->id)
            ->exists();

        if ($alreadyCheckedIn) {
            return view('attendance.result', [
                'success' => false,
                'message' => 'You have already checked in for this event.',
            ]);
        }

        Attendance::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'checked_in_at' => now(),
            'ip_address' => $request->ip(),
        ]);

        // Sequential Certificate Generation based on attendance order
        $certNumber = Certificate::generateCertificateNumber($event->id);

        Certificate::firstOrCreate(
            ['user_id' => $user->id, 'event_id' => $event->id],
            [
                'certificate_number' => $certNumber,
                'status' => 'pending'
            ]
        );

        return view('attendance.result', [
            'success' => true,
            'message' => 'Attendance recorded successfully!',
            'event' => $event,
        ]);
    }

    public function list(Event $event)
    {
        $participants = Participant::with(['user', 'user.attendances' => function($q) use ($event) {
            $q->where('event_id', $event->id);
        }])
        ->where('event_id', $event->id)
        ->where('status', 'accepted')
        ->get();

        return view('attendance.list', compact('event', 'participants'));
    }
}
