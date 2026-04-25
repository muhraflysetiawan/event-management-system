<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Certificate</title>
    <style>
        @font-face {
            font-family: 'Alex Brush';
            src: url('{{ storage_path("fonts/AlexBrush-Regular.ttf") }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @page {
            margin: 0;
            size: A4 landscape;
        }
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            color: #000;
            line-height: 1;
        }
        .container {
            position: relative;
            width: 1122px;
            height: 792px;
            overflow: hidden;
        }
        .background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        /* Logos - Enlarged and Parallel */
        .campus-logo {
            position: absolute;
            top: 40px;
            left: 50px;
            max-height: 100px; /* Enlarged */
            max-width: 180px;
            object-fit: contain;
        }
        .event-logo {
            position: absolute;
            top: 40px;
            right: 50px;
            max-height: 100px; /* Enlarged */
            max-width: 180px;
            object-fit: contain;
        }

        .content {
            text-align: center;
            padding-top: 120px;
        }
        .header {
            font-family: 'Times New Roman', Times, serif;
            font-size: 72px;
            font-weight: bold;
            margin-bottom: 0px;
            text-transform: uppercase;
        }
        .sub-header {
            font-family: 'Times New Roman', Times, serif;
            font-size: 28px;
            margin-top: -5px;
            margin-bottom: 40px;
            font-weight: bold;
        }
        .presented-to {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 18px; 
            color: #444;
            margin-bottom: 25px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .participant-name-wrapper {
            display: inline-block;
            margin-bottom: 25px;
        }
        .participant-name {
            font-size: 110px;
            font-family: 'Alex Brush', cursive !important;
            color: #980517;
            margin-bottom: 2px;
            padding: 0 40px;
        }
        .name-line {
            height: 1.5px;
            background: #000;
            width: 100%;
        }
        .description {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 20px;
            color: #222;
            line-height: 1.5;
            width: 80%;
            margin: 0 auto;
        }
        .event-name {
            font-weight: bold;
            font-size: 24px;
            color: #000;
        }
        
        .signatures-container {
            position: absolute;
            bottom: 60px;
            width: 100%;
        }
        .signature-table {
            width: 85%;
            margin: 0 auto;
        }
        .signature-cell {
            width: 50%;
            text-align: center;
            vertical-align: bottom;
        }
        .signature-label {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 5px;
            color: #444;
        }
        .signature-image-wrapper {
            height: 80px;
            margin-bottom: 2px;
        }
        .signature-img {
            max-height: 80px;
        }
        .signature-line {
            width: 240px;
            border-top: 1.5px solid #000;
            margin: 0 auto 5px;
        }
        .signer-name {
            font-family: Arial, Helvetica, sans-serif;
            font-weight: bold;
            font-size: 15px;
            color: #000;
        }
    </style>
</head>
<body>
    @php
        function getBase64Image($path) {
            if (file_exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                return 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
            return null;
        }
        $campusLogo = getBase64Image(public_path('assets/logo_kampus.png'));
        $eventLogo = null;
        if ($certificate->event->event_logo) {
            $eventLogo = getBase64Image(public_path('assets/' . $certificate->event->event_logo));
        }
    @endphp

    <div class="container">
        <!-- Background -->
        <img class="background" src="{{ public_path('assets/certificates/' . $certificate->event->certificate_template) }}" alt="Background">

        <!-- Logos as Base64 -->
        @if($campusLogo)
            <img class="campus-logo" src="{{ $campusLogo }}" alt="Campus Logo">
        @endif

        @if($eventLogo)
            <img class="event-logo" src="{{ $eventLogo }}" alt="Event Logo">
        @endif

        <div class="content">
            <div class="header">CERTIFICATE</div>
            <div class="sub-header">OF PARTICIPATION</div>
            
            <div class="presented-to">THIS CERTIFICATE IS PROUDLY PRESENTED TO</div>
            
            <div class="participant-name-wrapper">
                <div class="participant-name">{{ $certificate->user->name }}</div>
                <div class="name-line"></div>
            </div>
            
            <div class="description">
                For their active participation and successful completion of the <br>
                <span class="event-name">{{ $certificate->event->title }}</span><br>
                held on {{ $certificate->event->start_date->format('d F Y') }} at {{ $certificate->event->location }}.
            </div>
        </div>

        <div class="signatures-container">
            <table class="signature-table">
                <tr>
                    <td class="signature-cell">
                        <div class="signature-label">Head of Department</div>
                        <div class="signature-image-wrapper">
                            @if($certificate->event->lecturer && $certificate->event->lecturer->signature)
                                <img class="signature-img" src="{{ $certificate->event->lecturer->signature }}">
                            @endif
                        </div>
                        <div class="signature-line"></div>
                        <div class="signer-name">{{ $certificate->event->lecturer->name ?? 'Lecturer Name' }}</div>
                    </td>
                    <td class="signature-cell">
                        <div class="signature-label">General Manager</div>
                        <div class="signature-image-wrapper">
                            @if($certificate->event->organizer_signature)
                                <img class="signature-img" src="{{ $certificate->event->organizer_signature }}">
                            @endif
                        </div>
                        <div class="signature-line"></div>
                        <div class="signer-name">{{ $certificate->event->creator->name ?? 'Organizer Name' }}</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
