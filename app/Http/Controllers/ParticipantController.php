<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\Event;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    public function index(Request $request)
    {
        $query = Participant::with(['user', 'event']);

        if ($eventId = $request->get('event_id')) {
            $query->where('event_id', $eventId);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $participants = $query->latest()->paginate(20);
        $events = Event::all();
        return view('participants.index', compact('participants', 'events'));
    }

    public function store(Request $request, Event $event)
    {
        $user = auth()->user();

        // Check if already registered
        $exists = Participant::where('user_id', $user->id)
            ->where('event_id', $event->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'You have already registered for this event.');
        }

        // Check quota
        if ($event->isFull()) {
            return back()->with('error', 'This event is full. No more slots available.');
        }

        // Check event status
        if (!in_array($event->status, ['approved', 'published'])) {
            return back()->with('error', 'This event is not open for registration.');
        }

        $participant = Participant::create([
            'registration_number' => Participant::generateRegistrationNumber(),
            'user_id' => $user->id,
            'event_id' => $event->id,
            'status' => 'accepted',
        ]);

        return back()->with('success', "You have successfully joined the event! Your registration number is: {$participant->registration_number}");
    }

    public function updateStatus(Request $request, Participant $participant)
    {
        $request->validate([
            'status' => 'required|in:accepted,rejected',
        ]);

        $participant->update(['status' => $request->status]);

        NotificationService::notifyParticipantStatus(
            $participant->user,
            $participant->event,
            $request->status
        );

        return back()->with('success', "Participant {$request->status} successfully!");
    }

    public function myParticipants()
    {
        $participants = Participant::with('event')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('participants.my', compact('participants'));
    }
}
