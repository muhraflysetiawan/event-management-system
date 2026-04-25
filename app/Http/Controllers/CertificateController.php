<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Participant;
use App\Models\Event;
use App\Services\NotificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\User;

class CertificateController extends Controller
{
    public function index()
    {
        $certificates = Certificate::with('event')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('certificates.index', compact('certificates'));
    }

    public function manage(Event $event)
    {
        $certificates = Certificate::with('user')
            ->where('event_id', $event->id)
            ->get();

        $acceptedParticipants = Participant::with('user')
            ->where('event_id', $event->id)
            ->where('status', 'accepted')
            ->get();

        return view('certificates.manage', compact('event', 'certificates', 'acceptedParticipants'));
    }

    public function design(Event $event)
    {
        $lecturers = User::whereHas('role', function($q) {
            $q->where('slug', 'lecturer');
        })->get();

        $templates = ['certi1.png', 'certi2.png', 'certi3.png'];

        return view('certificates.design', compact('event', 'lecturers', 'templates'));
    }

    public function saveDesign(Request $request, Event $event)
    {
        $request->validate([
            'certificate_template' => 'required|string',
            'lecturer_id' => 'required|exists:users,id',
            'organizer_signature' => 'required|string', // Base64
            'event_logo' => 'nullable|image|mimes:png,jpg,jpeg|max:1024',
        ]);

        $data = [
            'certificate_template' => $request->certificate_template,
            'lecturer_id' => $request->lecturer_id,
            'organizer_signature' => $request->organizer_signature,
        ];

        if ($request->hasFile('event_logo')) {
            $path = $request->file('event_logo')->store('event_logos', 'public_assets');
            $data['event_logo'] = $path;
        }

        $event->update($data);

        return redirect()->route('certificates.manage', $event)
            ->with('success', 'Certificate design saved successfully!');
    }

    public function activate(Event $event)
    {
        if ($event->status !== 'completed') {
            return back()->with('error', 'Certificates can only be generated after the event is completed.');
        }

        if (!$event->certificate_template || !$event->lecturer_id || !$event->organizer_signature) {
            return back()->with('error', 'Please configure the certificate design first.');
        }

        $acceptedParticipants = Participant::with('user')
            ->where('event_id', $event->id)
            ->where('status', 'accepted')
            ->get();

        foreach ($acceptedParticipants as $participant) {
            $existing = Certificate::where('user_id', $participant->user_id)
                ->where('event_id', $event->id)
                ->first();

            if (!$existing) {
                Certificate::create([
                    'certificate_number' => Certificate::generateCertificateNumber(),
                    'user_id' => $participant->user_id,
                    'event_id' => $event->id,
                    'status' => 'available',
                ]);

                NotificationService::notifyCertificateAvailable($participant->user, $event);
            }
        }

        return back()->with('success', 'Certificates activated and notifications sent!');
    }

    public function download(Certificate $certificate)
    {
        $user = auth()->user();
        
        // Allow if: owner, admin, committee, or head department
        $isOwner = $certificate->user_id === $user->id;
        $isManagement = $user->isAdmin() || $user->isCommittee() || $user->isHeadDepartment();

        if (!$isOwner && !$isManagement) {
            abort(403);
        }

        $certificate->load(['user', 'event']);

        $pdf = Pdf::loadView('certificates.pdf', compact('certificate'))
            ->setPaper('a4', 'landscape');

        return $pdf->download("certificate-{$certificate->certificate_number}-" . time() . ".pdf", [
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
