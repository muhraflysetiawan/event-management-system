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

    public static function notifyEventPublished(Event $event): void
    {
        self::sendToMultipleRoles(
            ['student', 'lecturer'],
            'New Event Available',
            "A new event \"{$event->title}\" has been published. Register now!",
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
}
