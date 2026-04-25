<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AttendanceController extends Controller
{
    public function generate(Event $event)
    {
        return view('attendance.generate', compact('event'));
    }

    public function generateQR(Request $request, Event $event)
    {
        $request->validate([
            'duration' => 'required|integer|min:5|max:480',
        ]);

        $token = Str::random(32);
        $event->update([
            'qr_token' => $token,
            'qr_expires_at' => now()->addMinutes((int) $request->duration),
        ]);

        $qrUrl = route('attendance.checkin.form', ['token' => $token]);

        return back()->with([
            'qr_generated' => true,
            'qr_url' => $qrUrl,
            'qr_token' => $token,
            'expires_at' => $event->fresh()->qr_expires_at->toIso8601String(),
        ]);
    }

    public function showScanner()
    {
        return view('attendance.scan');
    }

    public function checkinForm(Request $request)
    {
        $token = $request->get('token');
        $event = Event::where('qr_token', $token)->first();

        if (!$event) {
            return view('attendance.result', ['success' => false, 'message' => 'Invalid QR code.']);
        }

        if (!$event->isQrValid()) {
            return view('attendance.result', ['success' => false, 'message' => 'QR code has expired.']);
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

        if (!$event->isQrValid()) {
            return back()->with('error', 'QR code has expired.');
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

        return view('attendance.result', [
            'success' => true,
            'message' => 'Attendance recorded successfully!',
            'event' => $event,
        ]);
    }

    public function list(Event $event)
    {
        $attendances = Attendance::with('user')
            ->where('event_id', $event->id)
            ->orderBy('checked_in_at')
            ->get();

        return view('attendance.list', compact('event', 'attendances'));
    }
}
