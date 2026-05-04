<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Project Brief - {{ $event->title }}</title>
    <style>
        @page {
            margin: 100px 40px 80px 40px; /* top, right, bottom, left */
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 14px;
            color: #333;
            line-height: 1.6;
        }
        header {
            position: fixed;
            top: -80px;
            left: 0;
            right: 0;
            height: 60px;
            border-bottom: 2px solid #980517;
            display: table;
            width: 100%;
        }
        .header-title {
            display: table-cell;
            vertical-align: bottom;
            padding-bottom: 10px;
            font-size: 18px;
            font-weight: bold;
            color: #980517;
        }
        .header-subtitle {
            display: table-cell;
            vertical-align: bottom;
            padding-bottom: 10px;
            text-align: right;
            font-size: 12px;
            color: #666;
        }
        footer {
            position: fixed; 
            bottom: -60px; 
            left: 0; 
            right: 0;
            height: 40px; 
            border-top: 1px solid #eee;
            font-size: 11px;
            color: #999;
            text-align: center;
            padding-top: 10px;
        }
        .pagenum:before {
            content: counter(page);
        }
        .meta-info {
            background: #f8f9fa;
            border: 1px solid #e2e8f0;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 5px;
        }
        .meta-label {
            font-weight: bold;
            width: 150px;
            color: #473f3d;
        }
        .content-section {
            margin-top: 20px;
        }
        .content-title {
            font-size: 16px;
            font-weight: bold;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
            margin-bottom: 15px;
            color: #473f3d;
        }
        .rich-text {
            line-height: 1.6;
        }
        .rich-text p {
            margin-top: 0;
            margin-bottom: 1em;
        }
        .rich-text ul, .rich-text ol {
            margin-top: 0;
            margin-bottom: 1em;
            padding-left: 20px;
        }
        .rich-text table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1em;
        }
        .rich-text table, .rich-text th, .rich-text td {
            border: 1px solid #ccc;
        }
        .rich-text th, .rich-text td {
            padding: 8px;
            text-align: left;
        }
        .rich-text img {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>

    <header>
        <div class="header-title">Project Brief Document</div>
        <div class="header-subtitle">Horizon Event Management System</div>
    </header>

    <footer>
        <span style="float: left;">{{ $event->title }}</span>
        <span>Page <span class="pagenum"></span></span>
        <span style="float: right;">Generated on {{ now()->format('d M Y H:i') }}</span>
    </footer>

    <main>
        <div style="text-align: center; margin-bottom: 20px;">
            <h1 style="font-size: 24px; color: #980517; margin: 0 0 5px 0;">{{ $event->title }}</h1>
            <p style="font-size: 14px; color: #666; margin: 0;">Official Event Project Brief</p>
        </div>

        <div class="meta-info">
            <table class="meta-table">
                <tr>
                    <td class="meta-label">Event Date</td>
                    <td>: {{ $event->start_date->format('d F Y, H:i') }} - {{ $event->end_date->format('d F Y, H:i') }}</td>
                </tr>
                <tr>
                    <td class="meta-label">Location</td>
                    <td>: {{ $event->location }}</td>
                </tr>
                <tr>
                    <td class="meta-label">Target Audience</td>
                    <td>: {{ collect($event->target_audience)->map(fn($t) => ucfirst($t))->implode(', ') }}</td>
                </tr>
                <tr>
                    <td class="meta-label">Quota</td>
                    <td>: {{ $event->quota }} participants</td>
                </tr>
                <tr>
                    <td class="meta-label">Proposed By</td>
                    <td>: {{ $event->creator->name ?? 'N/A' }} ({{ $event->creator->role->name ?? 'User' }})</td>
                </tr>
                <tr>
                    <td class="meta-label">Date Submitted</td>
                    <td>: {{ $event->created_at->format('d F Y') }}</td>
                </tr>
            </table>
        </div>

        <div class="content-section">
            <div class="content-title">Description</div>
            <div class="rich-text">
                {!! nl2br(e($event->description)) !!}
            </div>
        </div>

        <div style="page-break-after: always;"></div>

        <div class="content-section">
            <div class="content-title">Detailed Project Brief</div>
            <div class="rich-text">
                <!-- Render raw HTML from TinyMCE editor -->
                {!! $event->project_brief !!}
            </div>
        </div>
    </main>

</body>
</html>
