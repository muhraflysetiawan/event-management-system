<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Certificate;
use Barryvdh\DomPDF\Facade\Pdf;

function getOptimizedBase64Image($pathOrBase64, $maxWidth = 800) {
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

try {
    $certificate = Certificate::with(['user', 'event'])->find(1);
    if (!$certificate) {
        die("Certificate not found\n");
    }

    // Pass the function as closure or let Blade define it inside @php
    
    ini_set('memory_limit', '1G');
    echo "Memory limit: " . ini_get('memory_limit') . "\n";

    $pdf = Pdf::loadView('certificates.pdf', compact('certificate'))
        ->setPaper('a4', 'landscape');
    
    $output = $pdf->output();
    echo "SUCCESS: Generated " . strlen($output) . " bytes\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
