<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Event;

class NotificationService
{
    public static function send(User $user, string $title, string $message, string $type = 'info', ?Event $event = null): Notification
    {
        return Notification::create([
            'user_id' => $user->id,
            'event_id' => $event?->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'is_read' => false,
        ]);
    }

    public static function sendToRole(string $roleSlug, string $title, string $message, string $type = 'info', ?Event $event = null): void
    {
        $users = User::whereHas('role', fn($q) => $q->where('slug', $roleSlug))->get();

        foreach ($users as $user) {
            self::send($user, $title, $message, $type, $event);
        }
    }

    public static function sendToMultipleRoles(array $roleSlugs, string $title, string $message, string $type = 'info', ?Event $event = null): void
    {
        foreach ($roleSlugs as $slug) {
            self::sendToRole($slug, $title, $message, $type, $event);
        }
    }

    public static function notifyApprovalRequest(Event $event): void
    {
        $headRoles = $event->required_approval_roles ?? ['admin', 'head_csdl', 'head_baak', 'head_finance', 'head_gsd', 'head_sis', 'head_learning', 'acoo'];
        
        self::sendToMultipleRoles(
            $headRoles,
            'Event Approval Request',
            "A new event \"{$event->title}\" requires your approval. Please review its project brief.",
            'event',
            $event
        );

        $users = User::whereHas('role', fn($q) => $q->whereIn('slug', $headRoles))->get();
        foreach ($users as $user) {
            \Illuminate\Support\Facades\Mail::raw("Hello {$user->name},\n\nA new event requires your approval:\nTitle: {$event->title}\nProject Brief:\n{$event->project_brief}\n\nPlease login to the dashboard to approve or reject it.", function($msg) use ($user, $event) {
                $msg->to($user->email)->subject("Action Required: Event Approval Request - {$event->title}");
            });
        }
    }

    public static function notifyEventPublished(Event $event): void
    {
        self::sendToMultipleRoles(
            $event->target_audience ?? [],
            'New Event Available',
            "A new event \"{$event->title}\" has been published. Register now!",
            'event',
            $event
        );
    }

    public static function notifyEventFullyApproved(Event $event): void
    {
        $creator = User::find($event->created_by);
        if ($creator) {
            self::send(
                $creator,
                'Event Fully Approved!',
                "Your event \"{$event->title}\" has been approved by all required departments. You can now post/publish it.",
                'event',
                $event
            );
            
            \Illuminate\Support\Facades\Mail::raw("Hello {$creator->name},\n\nGood news! Your event \"{$event->title}\" has received all necessary approvals. You can now log in to the dashboard and publish your event to make it visible to participants.", function($msg) use ($creator, $event) {
                $msg->to($creator->email)->subject("Action Required: Your Event is Fully Approved - {$event->title}");
            });
        }
    }

    public static function notifyEventApproved(Event $event): void
    {
        // Notify the creator
        if ($event->creator) {
            self::send(
                $event->creator,
                'Event Approved',
                "Your event \"{$event->title}\" has been approved.",
                'event',
                $event
            );
        }

        // Notify target audience users
        self::sendToMultipleRoles(
            $event->target_audience ?? [],
            'New Event Available',
            "A new event \"{$event->title}\" has just been approved. Register now!",
            'event',
            $event
        );
    }

    public static function notifyParticipantStatus(User $user, Event $event, string $status): void
    {
        $statusText = ucfirst($status);
        self::send(
            $user,
            "Participant {$statusText}",
            "Your participant for \"{$event->title}\" has been {$status}.",
            'participant',
            $event
        );
    }

    public static function notifyCertificateAvailable(User $user, Event $event): void
    {
        self::send(
            $user,
            'Certificate Available',
            "Your certificate for \"{$event->title}\" is now available for download.",
            'certificate',
            $event
        );
    }

    public static function notifyEventReminder(User $user, Event $event): void
    {
        self::send(
            $user,
            'Event Reminder (H-2)',
            "The event \"{$event->title}\" will start in 2 days. Get ready!",
            'event',
            $event
        );
    }
}
