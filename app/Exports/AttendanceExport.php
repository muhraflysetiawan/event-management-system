<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;

class AttendanceExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $eventId;

    public function __construct($eventId = null)
    {
        $this->eventId = $eventId;
    }

    public function query()
    {
        $query = Attendance::query()->with(['user', 'event']);

        if ($this->eventId) {
            $query->where('event_id', $this->eventId);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Student Name',
            'Email',
            'Event Title',
            'Checked In At',
        ];
    }

    public function map($attendance): array
    {
        return [
            $attendance->id,
            $attendance->user->name,
            $attendance->user->email,
            $attendance->event->title,
            $attendance->checked_in_at->format('Y-m-d H:i:s'),
        ];
    }
}
