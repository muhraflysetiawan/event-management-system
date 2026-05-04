<?php

namespace App\Exports;

use App\Models\Event;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EventReportExport implements \Maatwebsite\Excel\Concerns\FromView, \Maatwebsite\Excel\Concerns\ShouldAutoSize, \Maatwebsite\Excel\Concerns\WithStyles
{
    protected $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
        $this->event->load(['participants.user.role', 'attendances', 'creator']);
    }

    public function view(): \Illuminate\Contracts\View\View
    {
        return view('exports.event_report', [
            'event' => $this->event
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            2 => ['font' => ['bold' => true]],
            3 => ['font' => ['bold' => true]],
            4 => ['font' => ['bold' => true]],
            5 => ['font' => ['bold' => true]],
            6 => ['font' => ['bold' => true]],
            9 => ['font' => ['bold' => true]],
            11 => ['font' => ['bold' => true, 'size' => 12]], // Table Header
        ];
    }
}
