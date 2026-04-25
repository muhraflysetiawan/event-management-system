<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\Certificate;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $participants = Participant::with(['event'])
            ->where('user_id', $user->id)
            ->where('status', 'accepted')
            ->latest()
            ->get()
            ->map(function ($reg) use ($user) {
                $certificate = Certificate::where('user_id', $user->id)
                    ->where('event_id', $reg->event_id)
                    ->where('status', 'available')
                    ->first();

                $reg->certificate = $certificate;
                $reg->event_status = match (true) {
                    $reg->event->status === 'completed' && $certificate => 'certificate_available',
                    $reg->event->status === 'completed' => 'completed',
                    $reg->event->status === 'ongoing' => 'ongoing',
                    default => $reg->event->status,
                };

                return $reg;
            });

        return view('history.index', compact('participants'));
    }
}
