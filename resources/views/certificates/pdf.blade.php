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
            height: 100px;
            width: auto;
        }
        .event-logo {
            position: absolute;
            top: 40px;
            right: 50px;
            height: 100px;
            width: auto;
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
        if (!function_exists('getOptimizedBase64Image')) {
            function getOptimizedBase64Image($pathOrBase64, $maxWidth = 800) {
                if (!$pathOrBase64) return null;

                if (str_starts_with($pathOrBase64, 'data:image')) {
                    list($type, $data) = explode(';', $pathOrBase64);
                    list(, $data)      = explode(',', $data);
                    $data = base64_decode($data);
                } else {
                    if (!file_exists($pathOrBase64)) return null;
                    $data = file_get_contents($pathOrBase64);
                }
                
                $image = @imagecreatefromstring($data);
                if (!$image) return null;
                
                $width = imagesx($image);
                $height = imagesy($image);
                
                if ($width > $maxWidth) {
                    $newWidth = $maxWidth;
                    $newHeight = floor($height * ($maxWidth / $width));
                    
                    $newImage = imagecreatetruecolor($newWidth, $newHeight);
                    
                    imagealphablending($newImage, false);
                    imagesavealpha($newImage, true);
                    $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
                    imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
                    
                    imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                    
                    ob_start();
                    imagepng($newImage);
                    $data = ob_get_clean();
                    imagedestroy($newImage);
                }
                imagedestroy($image);
                
                return 'data:image/png;base64,' . base64_encode($data);
            }
        }

        $bgImage = getOptimizedBase64Image(public_path('assets/certificates/' . $certificate->event->certificate_template), 1200);
        $campusLogo = getOptimizedBase64Image(public_path('assets/logo_kampus.png'), 300);
        $eventLogo = null;
        if ($certificate->event->event_logo) {
            $eventLogo = getOptimizedBase64Image(public_path('assets/' . $certificate->event->event_logo), 300);
        }
    @endphp

    <div class="container">
        <!-- Background -->
        @if($bgImage)
            <img class="background" src="{{ $bgImage }}" alt="Background">
        @endif

        <!-- Logos -->
        @if($campusLogo)
            <img class="campus-logo" src="{{ $campusLogo }}" alt="Campus Logo">
        @endif

        @if($eventLogo)
            <img class="event-logo" src="{{ $eventLogo }}" alt="Event Logo">
        @endif

        <div class="content">
            <div class="header">CERTIFICATE</div>
            <div class="sub-header">
                @if($certificate->type === 'winner')
                    OF EXCELLENCE
                @elseif($certificate->type === 'best_participant')
                    OF BEST PARTICIPANT
                @elseif($certificate->type === 'speaker')
                    OF APPRECIATION
                @elseif($certificate->type === 'moderator')
                    OF APPRECIATION
                @else
                    OF PARTICIPATION
                @endif
            </div>
            
            <div class="presented-to">THIS CERTIFICATE IS PROUDLY PRESENTED TO</div>
            
            <div class="participant-name-wrapper">
                @php
                    $words = explode(' ', $certificate->user->name);
                    if (count($words) > 2) {
                        $formattedName = $words[0] . ' ' . $words[1];
                        for ($i = 2; $i < count($words); $i++) {
                            $formattedName .= ' ' . strtoupper(substr($words[$i], 0, 1)) . '.';
                        }
                    } else {
                        $formattedName = $certificate->user->name;
                    }
                @endphp
                <div class="participant-name">{{ $formattedName }}</div>
                <div class="name-line"></div>
            </div>
            
            <div class="description">
                @if($certificate->type === 'winner')
                    For their outstanding performance and being awarded as the <strong>{{ $certificate->achievement_title ?? 'Winner' }}</strong> of the <br>
                @elseif($certificate->type === 'best_participant')
                    For their exceptional engagement and being awarded as the <strong>{{ $certificate->achievement_title ?? 'Best Participant' }}</strong> of the <br>
                @elseif($certificate->type === 'speaker')
                    For their valuable contribution as a <strong>{{ $certificate->achievement_title ?? 'Speaker' }}</strong> in the <br>
                @elseif($certificate->type === 'moderator')
                    For their valuable contribution as a <strong>{{ $certificate->achievement_title ?? 'Moderator' }}</strong> in the <br>
                @elseif($certificate->achievement_title)
                    For their recognition as <strong>{{ $certificate->achievement_title }}</strong> in the <br>
                @else
                    For their active participation and successful completion of the <br>
                @endif
                <span class="event-name">{{ $certificate->event->title }}</span><br>
                <span style="font-weight: normal; font-size: 16px;">{{ $certificate->certificate_number }}</span><br>
                held on {{ $certificate->event->start_date->format('d F Y') }}.
            </div>
        </div>

        <div class="signatures-container">
            <table class="signature-table">
                <tr>
                    <td class="signature-cell">
                        <div class="signature-label">Head of Department</div>
                        <div class="signature-image-wrapper">
                            @if($certificate->event->lecturer && $certificate->event->lecturer->signature)
                                <img class="signature-img" src="{{ getOptimizedBase64Image($certificate->event->lecturer->signature, 300) }}">
                            @endif
                        </div>
                        <div class="signature-line"></div>
                        <div class="signer-name">{{ $certificate->event->lecturer->name ?? 'Lecturer Name' }}</div>
                    </td>
                    <td class="signature-cell">
                        <div class="signature-label">General Manager</div>
                        <div class="signature-image-wrapper">
                            @if($certificate->event->organizer_signature)
                                <img class="signature-img" src="{{ getOptimizedBase64Image($certificate->event->organizer_signature, 300) }}">
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
