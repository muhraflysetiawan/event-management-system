<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Participant;
use App\Models\Event;
use App\Models\Attendance;
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
        $signingOfficials = User::whereHas('role', function($q) {
            $q->whereIn('slug', ['lecturer', 'acoo'])
              ->orWhere('slug', 'like', 'head_%');
        })->get();

        $templates = ['certi1.png', 'certi2.png', 'certi3.png'];

        $event->load(['lecturer', 'creator']);

        return view('certificates.design', compact('event', 'signingOfficials', 'templates'));
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

        if (!$event->certificate_template || !$event->lecturer_id) {
            return back()->with('error', 'Please configure the certificate design (Template and Lecturer) first.');
        }

        $acceptedParticipants = Participant::with('user')
            ->where('event_id', $event->id)
            ->where('status', 'accepted')
            ->get();

        if ($acceptedParticipants->isEmpty()) {
            return back()->with('error', 'No accepted participants found for this event. Certificates cannot be generated.');
        }

        // Update all pending certificates for this event to available
        Certificate::where('event_id', $event->id)
            ->where('status', 'pending')
            ->update(['status' => 'available']);

        // Notify participants
        foreach ($acceptedParticipants as $participant) {
            NotificationService::notifyCertificateAvailable($participant->user, $event);
        }

        return back()->with('success', 'Certificates activated and notifications sent!');
    }

    public function download(Certificate $certificate)
    {
        $user = auth()->user();
        
        // Allow if: owner, admin, committee, or head department
        $isOwner = $certificate->user_id === $user->id;
        $isManagement = $user->isAdmin() || $user->isCommittee() || $user->isHeadDepartment() || $user->isExternal();

        if (!$isOwner && !$isManagement) {
            abort(403);
        }

        // Check if event has a survey and user has not filled it
        if ($isOwner && !$isManagement) {
            $survey = $certificate->event->survey;
            if ($survey) {
                $hasResponded = \App\Models\SurveyResponse::where('survey_id', $survey->id)
                    ->where('user_id', $user->id)
                    ->exists();
                
                if (!$hasResponded) {
                    return redirect()->route('surveys.show', $certificate->event_id)
                        ->with('info', 'Please complete the satisfaction survey before downloading your certificate.');
                }
            }
        }

        $certificate->load(['user', 'event.lecturer.role', 'event.creator']);

        if (!$certificate->event->certificate_template) {
            return back()->with('error', 'Certificate template is not configured.');
        }

        ini_set('memory_limit', '1G');
        ini_set('max_execution_time', 300);

        $pdf = Pdf::loadView('certificates.pdf', compact('certificate'))
            ->setPaper('a4', 'landscape');

        $safeNumber = str_replace(['/', '\\'], '_', $certificate->certificate_number);

        return $pdf->download("certificate-{$safeNumber}-" . time() . ".pdf", [
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
    public function showCustomForm(Participant $participant)
    {
        $event = $participant->event;
        $user = $participant->user;
        
        $types = [
            'winner' => 'Winner',
            'best_participant' => 'Best Participant',
            'runner_up' => 'Runner Up',
            'honorable_mention' => 'Honorable Mention',
            'speaker' => 'Speaker',
            'moderator' => 'Moderator',
        ];

        return view('certificates.custom', compact('participant', 'event', 'user', 'types'));
    }

    public function storeCustom(Request $request, Participant $participant)
    {
        $request->validate([
            'type' => 'required|string',
            'achievement_title' => 'nullable|string|max:255',
        ]);

        $event = $participant->event;
        $user = $participant->user;

        // Check if event has certificate design
        if (!$event->certificate_template || !$event->lecturer_id || !$event->organizer_signature) {
            return back()->with('error', 'Please configure the certificate design for this event first.');
        }

        // Generate certificate number with type suffix to avoid duplication in number if needed
        // but here we just use the default logic and it will be unique because of the type in DB
        $certificateNumber = Certificate::generateCertificateNumber($event->id);
        
        // Add a suffix for custom certificates
        $typeSuffix = strtoupper(substr($request->type, 0, 3));
        $certificateNumber .= "/{$typeSuffix}";

        $certificate = Certificate::updateOrCreate(
            [
                'user_id' => $user->id,
                'event_id' => $event->id,
                'type' => $request->type,
            ],
            [
                'certificate_number' => $certificateNumber,
                'achievement_title' => $request->achievement_title,
                'status' => 'available',
            ]
        );

        NotificationService::notifyCustomCertificate($user, $event, $request->type);

        return redirect()->route('participants.index', ['event_id' => $event->id])
            ->with('success', "Custom certificate ({$request->type}) issued to {$user->name} successfully!");
    }
}
