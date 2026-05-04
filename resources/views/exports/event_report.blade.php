<table>
    <thead>
        <tr>
            <th colspan="8" style="font-weight: bold; font-size: 14px; text-align: center;">OVERALL EVENT REPORT</th>
        </tr>
        <tr>
            <th style="font-weight: bold;">Event Title</th>
            <td colspan="7">{{ $event->title }}</td>
        </tr>
        <tr>
            <th style="font-weight: bold;">Date & Location</th>
            <td colspan="7">{{ $event->start_date->format('d M Y') }} to {{ $event->end_date->format('d M Y') }} — {{ $event->location }}</td>
        </tr>
        <tr>
            <th style="font-weight: bold;">Committee (Created By)</th>
            <td colspan="7">{{ $event->creator->name ?? 'Unknown' }}</td>
        </tr>
        <tr>
            <th style="font-weight: bold;">Total Participants</th>
            <td colspan="7">{{ $event->participants->count() }} Registered / {{ $event->attendances->count() ?? 0 }} Attended</td>
        </tr>
        <tr>
            <th style="font-weight: bold;">Certificate Status</th>
            <td colspan="7">{{ $event->certificate_template ? 'Configured & Ready' : 'Not Configured' }}</td>
        </tr>
        <tr>
            <th colspan="8"></th>
        </tr>
        <tr>
            <th style="font-weight: bold;">Event Details</th>
            <td colspan="7">{{ $event->description }}</td>
        </tr>
        <tr>
            <th colspan="8"></th>
        </tr>
        <tr>
            <th style="font-weight: bold; background-color: #4f46e5; color: #ffffff;">No.</th>
            <th style="font-weight: bold; background-color: #4f46e5; color: #ffffff;">Registration Number</th>
            <th style="font-weight: bold; background-color: #4f46e5; color: #ffffff;">Participant Name</th>
            <th style="font-weight: bold; background-color: #4f46e5; color: #ffffff;">Role</th>
            <th style="font-weight: bold; background-color: #4f46e5; color: #ffffff;">Registration Date</th>
            <th style="font-weight: bold; background-color: #4f46e5; color: #ffffff;">Status</th>
            <th style="font-weight: bold; background-color: #4f46e5; color: #ffffff;">Check-in Time</th>
            <th style="font-weight: bold; background-color: #4f46e5; color: #ffffff;">Certificate Number</th>
        </tr>
    </thead>
    <tbody>
        @foreach($event->participants as $index => $participant)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $participant->registration_number ?? '-' }}</td>
            <td>{{ $participant->user->name ?? 'Unknown' }}</td>
            <td>{{ $participant->user->role->name ?? 'Unknown' }}</td>
            <td>{{ $participant->created_at->format('d M Y, H:i') }}</td>
            <td>{{ ucfirst($participant->status) }}</td>
            <td>
                @php
                    $attendance = $event->attendances->where('user_id', $participant->user_id)->first();
                @endphp
                {{ $attendance ? $attendance->checked_in_at->format('H:i') : '-' }}
            </td>
            <td>
                @php
                    $cert = \App\Models\Certificate::where('user_id', $participant->user_id)->where('event_id', $event->id)->first();
                @endphp
                {{ $cert ? $cert->certificate_number : '-' }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
