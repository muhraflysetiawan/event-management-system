<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h2 {
            margin: 0;
            color: #1f2937;
        }
        .header p {
            margin: 5px 0 0;
            color: #6b7280;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #e5e7eb;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f9fafb;
            font-weight: bold;
            color: #374151;
        }
        tr:nth-child(even) {
            background-color: #f3f4f6;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $title }}</h2>
        <p>Generated on {{ now()->format('d M Y, H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Student Name</th>
                <th>Email</th>
                <th>Event</th>
                <th>Checked In At</th>
                <th>IP Address</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendances as $i => $att)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $att->user->name }}</td>
                <td>{{ $att->user->email }}</td>
                <td>{{ $att->event->title }}</td>
                <td>{{ $att->checked_in_at->format('d M Y, H:i') }}</td>
                <td>{{ $att->ip_address }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Horizon Event Management System</p>
    </div>
</body>
</html>
