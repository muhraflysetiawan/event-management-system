<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use App\Models\Participant;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function signatureForm()
    {
        $user = auth()->user();
        if (!$user->isLecturer() && !$user->isAdmin()) {
            abort(403);
        }
        return view('profile.signature');
    }

    public function saveSignature(Request $request)
    {
        $user = auth()->user();
        if (!$user->isLecturer() && !$user->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'signature' => 'required|string',
        ]);

        $user->update(['signature' => $request->signature]);

        return back()->with('success', 'Signature saved successfully!');
    }

    public function index()
    {
        $user = auth()->user();
        $data = [];

        if ($user->isAdmin()) {
            $data = [
                'totalEvents' => Event::count(),
                'totalUsers' => User::count(),
                'pendingParticipants' => Participant::where('status', 'pending')->count(),
                'completedEvents' => Event::where('status', 'completed')->count(),
                'ongoingEvents' => Event::where('status', 'ongoing')->count(),
                'recentEvents' => Event::latest()->take(5)->get(),
                'recentParticipants' => Participant::with(['user', 'event'])->latest()->take(5)->get(),
                'monthlyStats' => $this->getMonthlyStats(),
            ];
            return view('dashboard.admin', $data);
        }

        if ($user->isCommittee() || $user->isLecturer() || $user->isStaff() || $user->isExternal()) {
            $data = [
                'myEvents' => Event::where('created_by', $user->id)->latest()->get(),
                'totalCreated' => Event::where('created_by', $user->id)->count(),
                'pendingApprovals' => Participant::whereHas('event', fn($q) => $q->where('created_by', $user->id))
                    ->where('status', 'pending')->count(),
                'waitingApprovals' => Event::where('created_by', $user->id)->where('status', 'pending_approval')->get(),
            ];
            return view('dashboard.committee', $data);
        }

        if ($user->isHeadDepartment() || $user->isACOO()) {
            $data = [
                'pendingApprovals' => Event::where('status', 'pending_approval')
                    ->whereJsonContains('required_approval_roles', $user->role->slug)
                    ->whereJsonDoesntContain('approved_by_roles', $user->role->slug)->count(),
                'eventsToApprove' => Event::where('status', 'pending_approval')
                    ->whereJsonContains('required_approval_roles', $user->role->slug)
                    ->whereJsonDoesntContain('approved_by_roles', $user->role->slug)->latest()->get(),
                'approvalHistory' => \App\Models\ApprovalLog::where('user_id', $user->id)->latest()->take(10)->get(),
            ];
            return view('dashboard.head', $data);
        }

        if ($user->isStudent()) {
            $data = [
                'registeredCount' => Participant::where('user_id', $user->id)->count(),
                'certificatesCount' => Certificate::where('user_id', $user->id)->where('status', 'available')->count(),
                'ongoingEvents' => Event::where('status', 'ongoing')
                    ->whereHas('participants', fn($q) => $q->where('user_id', $user->id)->where('status', 'accepted'))
                    ->get(),
                'attendanceCount' => \App\Models\Attendance::where('user_id', $user->id)->count(),
                'availableEvents' => Event::where('status', 'published')
                    ->whereJsonContains('target_audience', 'student')
                    ->where('start_date', '>', now())->latest()->take(5)->get(),
            ];
            return view('dashboard.student', $data);
        }

        return view('dashboard.lecturer', ['notifications' => $user->notifications()->latest()->take(10)->get()]);
    }

    private function getMonthlyStats(): array
    {
        $stats = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $stats[] = [
                'month' => $date->format('M'),
                'events' => Event::whereMonth('created_at', $date->month)->whereYear('created_at', $date->year)->count(),
                'participants' => Participant::whereMonth('created_at', $date->month)->whereYear('created_at', $date->year)->count(),
            ];
        }
        return $stats;
    }
}
