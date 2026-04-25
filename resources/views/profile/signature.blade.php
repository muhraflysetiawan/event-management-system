@extends('layouts.app')
@section('title', 'Lecturer Signature')

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-pen-nib" style="color:var(--primary-400);margin-right:0.5rem;"></i> My Signature</h3>
    </div>
    <div class="card-body">
        <p style="color:var(--text-muted);margin-bottom:1.5rem;">
            Provide your digital signature. You can either draw it directly or upload a photo of your signature.
        </p>

        @if(auth()->user()->signature)
        <div style="margin-bottom:2rem; text-align:center;">
            <div style="font-size:0.8125rem;color:var(--text-muted);margin-bottom:0.75rem;">Current Signature:</div>
            <div style="background:white; padding:1rem; border:1px solid var(--border-color); border-radius:var(--radius-md); display:inline-block;">
                <img src="{{ auth()->user()->signature }}" alt="Signature" style="max-height:150px;">
            </div>
        </div>
        @endif

        <!-- Tabs for Signature Options -->
        <div style="display:flex; gap:1rem; margin-bottom:1.5rem; border-bottom:1px solid var(--border-color); padding-bottom:0.5rem;">
            <button type="button" class="tab-btn active" onclick="switchTab('draw')">Draw Signature</button>
            <button type="button" class="tab-btn" onclick="switchTab('upload')">Upload Photo</button>
        </div>

        <form id="signature-form" method="POST" action="{{ route('profile.signature.save') }}">
            @csrf
            <input type="hidden" name="signature" id="signature-input">

            <!-- Draw Section -->
            <div id="draw-section">
                <div class="form-group">
                    <label class="form-label">Sign Below</label>
                    <div style="background:white; border:1px solid var(--border-color); border-radius:var(--radius-md); max-width:400px; margin:0 auto;">
                        <canvas id="signature-pad" width="400" height="200" style="touch-action: none; cursor: url('data:image/svg+xml;utf8,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; width=&quot;24&quot; height=&quot;24&quot; viewBox=&quot;0 0 24 24&quot;><circle cx=&quot;12&quot; cy=&quot;12&quot; r=&quot;3&quot; fill=&quot;black&quot;/></svg>') 12 12, crosshair !important;"></canvas>
                    </div>
                    <div style="margin-top:1rem; text-align:center;">
                        <button type="button" class="btn btn-secondary btn-sm" id="clear-signature">Clear Signature</button>
                    </div>
                </div>
            </div>

            <!-- Upload Section -->
            <div id="upload-section" style="display:none;">
                <div class="form-group">
                    <label class="form-label">Upload Signature Photo</label>
                    <input type="file" id="signature-upload" class="form-input" accept="image/*">
                    <p style="font-size:0.75rem;color:var(--text-muted);margin-top:0.5rem;">
                        <i class="fas fa-info-circle"></i> Use a plain white background. The system will automatically remove the background and darken the ink.
                    </p>
                    <div id="preview-container" style="display:none; margin-top:1rem; text-align:center;">
                        <p style="font-size:0.75rem;color:var(--text-muted);margin-bottom:0.5rem;">Processed Preview:</p>
                        <canvas id="process-canvas" style="border:1px solid var(--border-color); max-width:100%; background:#eee;"></canvas>
                    </div>
                </div>
            </div>

            <div class="action-group mt-3" style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Signature</button>
            </div>
        </form>
    </div>
</div>

<style>
    .tab-btn {
        background: none;
        border: none;
        padding: 0.5rem 1rem;
        cursor: pointer;
        font-weight: 600;
        color: var(--text-muted);
        border-radius: var(--radius-md);
    }
    .tab-btn.active {
        color: var(--primary-400);
        background: rgba(152, 5, 23, 0.05);
    }
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
    let currentMode = 'draw';
    const canvas = document.getElementById('signature-pad');
    const signaturePad = new SignaturePad(canvas, {
        minWidth: 2,
        maxWidth: 4,
        penColor: "rgb(0, 0, 0)"
    });

    function switchTab(mode) {
        currentMode = mode;
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');
        
        if (mode === 'draw') {
            document.getElementById('draw-section').style.display = 'block';
            document.getElementById('upload-section').style.display = 'none';
        } else {
            document.getElementById('draw-section').style.display = 'none';
            document.getElementById('upload-section').style.display = 'block';
        }
    }

    document.getElementById('clear-signature').addEventListener('click', () => {
        signaturePad.clear();
    });

    // Image Processing for Upload
    const uploadInput = document.getElementById('signature-upload');
    const processCanvas = document.getElementById('process-canvas');
    const ctx = processCanvas.getContext('2d');
    let processedDataUrl = null;

    uploadInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(event) {
            const img = new Image();
            img.onload = function() {
                // Set canvas size
                const maxWidth = 800;
                const scale = img.width > maxWidth ? maxWidth / img.width : 1;
                processCanvas.width = img.width * scale;
                processCanvas.height = img.height * scale;

                // Draw image
                ctx.drawImage(img, 0, 0, processCanvas.width, processCanvas.height);

                // Process Pixels: Remove background (Thresholding)
                const imageData = ctx.getImageData(0, 0, processCanvas.width, processCanvas.height);
                const data = imageData.data;

                for (let i = 0; i < data.length; i += 4) {
                    const r = data[i];
                    const g = data[i + 1];
                    const b = data[i + 2];
                    
                    // Simple threshold: if bright (white/light), make transparent
                    // Also darken the rest to black
                    const brightness = (r + g + b) / 3;
                    if (brightness > 180) { // Adjust threshold if needed
                        data[i + 3] = 0; // Alpha
                    } else {
                        // Make ink pure black
                        data[i] = 0;
                        data[i + 1] = 0;
                        data[i + 2] = 0;
                    }
                }
                ctx.putImageData(imageData, 0, 0);
                processedDataUrl = processCanvas.toDataURL();
                document.getElementById('preview-container').style.display = 'block';
            };
            img.src = event.target.result;
        };
        reader.readAsDataURL(file);
    });

    document.getElementById('signature-form').addEventListener('submit', function (e) {
        const signatureInput = document.getElementById('signature-input');
        
        if (currentMode === 'draw') {
            if (signaturePad.isEmpty()) {
                alert('Please provide your signature.');
                e.preventDefault();
                return;
            }
            signatureInput.value = signaturePad.toDataURL();
        } else {
            if (!processedDataUrl) {
                alert('Please upload a signature photo.');
                e.preventDefault();
                return;
            }
            signatureInput.value = processedDataUrl;
        }
    });
</script>
@endpush
@endsection
