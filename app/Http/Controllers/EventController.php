<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\NotificationService;
use App\Helpers\AuditHelper;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with('creator')->withCount('participants');

        if ($search = $request->get('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $events = $query->latest()->paginate(12);

        $joinedEventIds = [];
        if (auth()->check()) {
            $joinedEventIds = auth()->user()->participants()->pluck('event_id')->toArray();
        }

        return view('events.index', compact('events', 'joinedEventIds'));
    }

    public function create()
    {
        return view('events.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $date = Carbon::parse($value);
                    $minDate = Carbon::now()->addDays(7);
                    if ($date->lt($minDate)) {
                        $fail('Event must be scheduled at least 7 days in advance.');
                    }
                },
            ],
            'end_date' => 'required|date|after:start_date',
            'location' => 'required|string|max:255',
            'quota' => 'required|integer|min:1',
            'status' => 'required|in:draft,pending_approval',
            'materials' => 'nullable|array',
            'materials.*.title' => 'nullable|string|max:255',
            'materials.*.description' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();
        $event = Event::create($validated);

        AuditHelper::log('create', $event, null, $event->toArray());

        if ($request->has('materials')) {
            foreach ($request->materials as $material) {
                if (!empty($material['title'])) {
                    $event->materials()->create($material);
                }
            }
        }

        if ($event->status === 'pending_approval') {
            // Notify Head Department here (simplified)
            // NotificationService::notifyApprovalRequest($event);
        }

        return redirect()->route('events.show', $event)
            ->with('success', 'Event created successfully!');
    }

    public function show(Event $event)
    {
        $event->load(['creator', 'participants.user', 'attendances.user']);
        $userParticipant = null;
        if (auth()->check()) {
            $userParticipant = $event->participants()->where('user_id', auth()->id())->first();
        }
        return view('events.show', compact('event', 'userParticipant'));
    }

    public function edit(Event $event)
    {
        $this->authorizeEvent($event);
        return view('events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $this->authorizeEvent($event);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($event) {
                    $date = Carbon::parse($value);
                    // Only enforce 7-day rule if start_date is being changed
                    if ($event->start_date->ne($date)) {
                        $minDate = Carbon::now()->addDays(7);
                        if ($date->lt($minDate)) {
                            $fail('Event must be scheduled at least 7 days in advance.');
                        }
                    }
                },
            ],
            'end_date' => 'required|date|after:start_date',
            'location' => 'required|string|max:255',
            'quota' => 'required|integer|min:1',
            'status' => [
                'required',
                'in:draft,pending_approval,approved,published,ongoing,completed,cancelled',
                function ($attribute, $value, $fail) use ($event) {
                    if ($value === 'cancelled' && $event->status !== 'cancelled') {
                        $maxCancelDate = $event->start_date->copy()->subDay();
                        if (Carbon::now()->gt($maxCancelDate)) {
                            $fail('Event can only be cancelled at least 1 day before the event date.');
                        }
                    }
                },
            ],
        ]);

        $oldData = $event->toArray();
        $wasNotPublished = $event->status !== 'published';
        $event->update($validated);

        $action = $validated['status'] === 'cancelled' && $oldData['status'] !== 'cancelled' ? 'cancel' : 'update';
        AuditHelper::log($action, $event, $oldData, $event->toArray());

        if ($wasNotPublished && $event->status === 'published') {
            NotificationService::notifyEventPublished($event);
        }

        return redirect()->route('events.show', $event)
            ->with('success', 'Event updated successfully!');
    }

    public function destroy(Event $event)
    {
        $this->authorizeEvent($event);
        $oldData = $event->toArray();
        $event->delete();
        AuditHelper::log('delete', $event, $oldData, null);
        return redirect()->route('events.index')
            ->with('success', 'Event deleted successfully!');
    }

    private function authorizeEvent(Event $event)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->isCommittee() && !$user->isHeadDepartment()) {
            abort(403);
        }
        if ($user->isCommittee() && $event->created_by !== $user->id) {
            abort(403);
        }
    }
}
