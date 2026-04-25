<!DOCTYPE html>
<html><head><meta charset="UTF-8">
<title>Report — {{ $report->title }}</title>
<style>
    body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 12px; color: #333; line-height: 1.6; }
    h1 { font-size: 20px; color: #4f46e5; margin-bottom: 5px; }
    h2 { font-size: 14px; color: #6366f1; margin-top: 20px; border-bottom: 2px solid #e0e7ff; padding-bottom: 5px; }
    .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #4f46e5; padding-bottom: 15px; }
    .meta { display: flex; margin-bottom: 20px; }
    .meta-item { margin-right: 30px; }
    .meta-label { font-size: 10px; color: #666; text-transform: uppercase; letter-spacing: 1px; }
    .meta-value { font-size: 14px; font-weight: bold; margin-top: 3px; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 11px; }
    th { background: #4f46e5; color: white; padding: 8px; text-align: left; font-size: 10px; text-transform: uppercase; }
    td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; }
    tr:nth-child(even) td { background: #f9fafb; }
    .content { margin-top: 15px; line-height: 1.8; }
    .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #e5e7eb; padding-top: 10px; }
</style>
</head><body>
<div class="header">
    <h1>Event Report</h1>
    <p style="color:#666;">{{ $report->title }}</p>
</div>

<table style="margin-bottom:20px;width:auto;">
    <tr><td style="border:none;padding:3px 20px 3px 0;"><strong>Event:</strong></td><td style="border:none;padding:3px;">{{ $report->event->title }}</td></tr>
    <tr><td style="border:none;padding:3px 20px 3px 0;"><strong>Date:</strong></td><td style="border:none;padding:3px;">{{ $report->event->start_date->format('d M Y') }} — {{ $report->event->end_date->format('d M Y') }}</td></tr>
    <tr><td style="border:none;padding:3px 20px 3px 0;"><strong>Location:</strong></td><td style="border:none;padding:3px;">{{ $report->event->location }}</td></tr>
    <tr><td style="border:none;padding:3px 20px 3px 0;"><strong>Created By:</strong></td><td style="border:none;padding:3px;">{{ $report->creator->name }}</td></tr>
    <tr><td style="border:none;padding:3px 20px 3px 0;"><strong>Total Participants:</strong></td><td style="border:none;padding:3px;">{{ $report->total_participants }}</td></tr>
    <tr><td style="border:none;padding:3px 20px 3px 0;"><strong>Total Attended:</strong></td><td style="border:none;padding:3px;">{{ $report->total_attended }}</td></tr>
</table>

@if($report->summary)
<h2>Summary</h2>
<p>{{ $report->summary }}</p>
@endif

<h2>Report Content</h2>
<div class="content">{!! nl2br(e($report->content)) !!}</div>

<h2>Participants</h2>
<table>
    <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Reg Number</th><th>Status</th></tr></thead>
    <tbody>
    @foreach($participants as $i => $reg)
    <tr><td>{{ $i+1 }}</td><td>{{ $reg->user->name }}</td><td>{{ $reg->user->email }}</td><td>{{ $reg->participant_number }}</td><td>{{ ucfirst($reg->status) }}</td></tr>
    @endforeach
    </tbody>
</table>

<h2>Attendance</h2>
<table>
    <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Checked In At</th></tr></thead>
    <tbody>
    @foreach($attendances as $i => $att)
    <tr><td>{{ $i+1 }}</td><td>{{ $att->user->name }}</td><td>{{ $att->user->email }}</td><td>{{ $att->checked_in_at->format('d M Y H:i:s') }}</td></tr>
    @endforeach
    </tbody>
</table>

<div class="footer">Generated on {{ now()->format('d M Y H:i:s') }} — Event Management System</div>
</body></html>
