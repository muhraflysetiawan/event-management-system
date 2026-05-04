<?php

namespace App\Exports;

use App\Models\Report;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FinancialReportExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $report;

    public function __construct(Report $report)
    {
        $this->report = $report;
    }

    public function view(): View
    {
        return view('exports.financial_report', [
            'report' => $this->report,
            'event' => $this->report->event,
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            2 => ['font' => ['bold' => true]],
            5 => ['font' => ['bold' => true]],
            8 => ['font' => ['bold' => true]],
        ];
    }
}
