<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\NotificationService;
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

        if (auth()->check()) {
            $user = auth()->user();
            if (!$user->isAdmin() && !$user->isCommittee() && !$user->isHeadDepartment() && !$user->isACOO()) {
                if ($user->role) {
                    $query->whereJsonContains('target_audience', $user->role->slug);
                }
            }

            // Filter pending_approval events for Head roles/Admins
            if ($user->isAdmin() || $user->isHeadDepartment() || $user->isACOO()) {
                $query->where(function($q) use ($user) {
                    $q->where('status', '!=', 'pending_approval')
                      ->orWhereJsonContains('required_approval_roles', $user->role->slug)
                      ->orWhere('created_by', $user->id); // Always show if I created it
                });
            }
        }

        $events = $query->latest()->paginate(12);

        $userParticipants = collect();
        if (auth()->check()) {
            $userParticipants = auth()->user()->participants->keyBy('event_id');
        }

        return view('events.index', compact('events', 'userParticipants'));
    }

    public function create()
    {
        return view('events.create');
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if ($user->role->slug === 'student') {
            abort(403, 'Students cannot create events.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'project_brief_type' => 'required|in:text,pdf',
            'project_brief' => 'required_if:project_brief_type,text|nullable|string',
            'project_brief_pdf' => 'required_if:project_brief_type,pdf|nullable|file|mimes:pdf|max:10240',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'location' => 'required|string|max:255',
            'quota' => 'required|integer|min:1',
            'target_audience' => 'required|array|min:1',
            'target_audience.*' => 'string|in:student,lecturer,staff,external',
            'required_approval_roles' => 'required|array|min:1',
            'required_approval_roles.*' => 'string|in:admin,head_csdl,head_baak,head_finance,head_gsd,head_sis,head_learning,acoo',
            'status' => 'required|in:draft,pending_approval',
            'materials' => 'nullable|array',
            'materials.*.title' => 'nullable|string|max:255',
            'materials.*.description' => 'nullable|string',
        ]);

        if ($request->hasFile('project_brief_pdf')) {
            $validated['project_brief_pdf'] = $request->file('project_brief_pdf')->store('project_briefs', 'public');
        }

        $validated['created_by'] = $user->id;
        $event = Event::create($validated);

        if ($request->has('materials')) {
            foreach ($request->materials as $material) {
                if (!empty($material['title'])) {
                    $event->materials()->create($material);
                }
            }
        }

        if ($event->status === 'pending_approval') {
            \App\Services\NotificationService::notifyApprovalRequest($event);
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
        
        $user = auth()->user();
        if (!$user->isAdmin() && $event->status !== 'draft') {
            return redirect()->route('events.show', $event)->with('error', 'You can only edit events that are still in draft status.');
        }

        return view('events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $this->authorizeEvent($event);

        $user = auth()->user();
        if (!$user->isAdmin() && $event->status !== 'draft') {
            return redirect()->route('events.show', $event)->with('error', 'You can only edit events that are still in draft status.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'project_brief_type' => 'required|in:text,pdf',
            'project_brief' => 'required_if:project_brief_type,text|nullable|string',
            'project_brief_pdf' => 'nullable|file|mimes:pdf|max:10240',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'location' => 'required|string|max:255',
            'quota' => 'required|integer|min:1',
            'target_audience' => 'required|array|min:1',
            'target_audience.*' => 'string|in:student,lecturer,staff,external',
            'required_approval_roles' => 'required|array|min:1',
            'required_approval_roles.*' => 'string|in:admin,head_csdl,head_baak,head_finance,head_gsd,head_sis,head_learning,acoo',
            'status' => 'required|in:draft,pending_approval,approved,published,ongoing,completed,cancelled',
        ]);

        if (!$user->isAdmin()) {
            if (!in_array($validated['status'], ['draft', 'pending_approval'])) {
                $validated['status'] = $event->status; // Revert to current if unauthorized
            }
        }

        if ($request->hasFile('project_brief_pdf')) {
            $validated['project_brief_pdf'] = $request->file('project_brief_pdf')->store('project_briefs', 'public');
        }

        $oldData = $event->toArray();
        $wasNotPublished = $event->status !== 'published';
        $event->update($validated);

        if ($wasNotPublished && $event->status === 'published') {
            NotificationService::notifyEventPublished($event);
        }

        if ($oldData['status'] !== 'approved' && $event->status === 'approved') {
            NotificationService::notifyEventApproved($event);
        }

        if ($oldData['status'] !== 'pending_approval' && $event->status === 'pending_approval') {
            NotificationService::notifyApprovalRequest($event);
        }

        return redirect()->route('events.show', $event)
            ->with('success', 'Event updated successfully!');
    }

    public function destroy(Event $event)
    {
        $this->authorizeEvent($event);
        $oldData = $event->toArray();
        $event->delete();
        return redirect()->route('events.index')
            ->with('success', 'Event deleted successfully!');
    }

    public function exportProjectBriefPdf(Event $event)
    {
        $user = auth()->user();
        
        // Add auth check that allows creators or roles with permission
        if (!$user->isAdmin() && !$user->isCommittee() && !$user->isHeadDepartment() && !$user->isACOO() && $event->created_by !== $user->id) {
            abort(403, 'You do not have permission to view this document.');
        }

        if ($event->project_brief_type === 'pdf' && $event->project_brief_pdf) {
            $filePath = storage_path('app/public/' . $event->project_brief_pdf);
            if (!file_exists($filePath)) {
                abort(404, 'PDF file not found.');
            }
            return response()->file($filePath);
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('events.project_brief_pdf', compact('event'));
        return $pdf->download("Project_Brief_{$event->id}.pdf");
    }

    public function approve(Event $event)
    {
        $user = auth()->user();
        $roleSlug = $user->role->slug ?? '';
        
        if (!in_array($roleSlug, $event->required_approval_roles ?? [])) {
            abort(403, 'Unauthorized to approve this event.');
        }

        $approvedBy = $event->approved_by_roles ?? [];
        if (!in_array($roleSlug, $approvedBy)) {
            $approvedBy[] = $roleSlug;
            $event->update(['approved_by_roles' => $approvedBy]);
            
            if ($event->isFullyApproved()) {
                // All roles have approved, notify the creator
                NotificationService::notifyEventFullyApproved($event);
            }
            
            return back()->with('success', 'You have approved this event.');
        }

        return back()->with('info', 'You have already approved this event.');
    }

    public function publish(Event $event)
    {
        $user = auth()->user();
        if ($event->created_by !== $user->id && !$user->isAdmin()) {
            abort(403);
        }

        if ($event->isFullyApproved() && $event->status === 'pending_approval') {
            $event->update(['status' => 'published']); // Changed from 'approved' to 'published'
            return back()->with('success', 'Event published successfully!');
        }

        return back()->with('error', 'Event cannot be published yet.');
    }

    public function updateStatus(Request $request, Event $event)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && $event->created_by !== $user->id) {
            abort(403);
        }

        $nextStatus = null;
        if ($event->status === 'published') {
            $nextStatus = 'ongoing';
        } elseif ($event->status === 'ongoing') {
            $nextStatus = 'completed';
        }

        if ($nextStatus) {
            $event->update(['status' => $nextStatus]);
            return back()->with('success', "Event status updated to " . ucfirst($nextStatus));
        }

        return back()->with('error', 'Status transition not allowed.');
    }

    public function approvals()
    {
        $user = auth()->user();
        if (!$user->isHeadDepartment() && !$user->isAdmin() && !$user->isACOO()) {
            abort(403);
        }

        $events = Event::where('status', 'pending_approval')
            ->whereJsonContains('required_approval_roles', $user->role->slug)
            ->where(function($q) use ($user) {
                $q->whereNull('approved_by_roles')
                  ->orWhereJsonDoesntContain('approved_by_roles', $user->role->slug);
            })
            ->latest()
            ->paginate(10);

        return view('events.approvals', compact('events'));
    }

    public function reject(Event $event)
    {
        $user = auth()->user();
        if (!in_array($user->role->slug ?? '', $event->required_approval_roles ?? ['admin', 'head_csdl', 'acoo'])) {
            abort(403, 'Unauthorized to reject events.');
        }

        if ($event->status === 'pending_approval') {
            $event->update(['status' => 'draft']);
            return back()->with('success', 'Event rejected and returned to draft status.');
        }

        return back()->with('error', 'Event is not pending approval.');
    }

    private function authorizeEvent(Event $event)
    {
        $user = auth()->user();
        if ($user->isAdmin()) return;
        
        if ($user->role->slug === 'student') {
            abort(403, 'Students cannot manage events.');
        }

        if ($event->created_by !== $user->id) {
            abort(403, 'You can only manage your own events.');
        }
    }
}
