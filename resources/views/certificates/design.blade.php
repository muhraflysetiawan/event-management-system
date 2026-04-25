@extends('layouts.app')
@section('title', 'Design Certificate — ' . $event->title)

@section('content')
<div class="card">

    <div class="card-body">
        <form id="design-form" method="POST" action="{{ route('certificates.saveDesign', $event) }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="organizer_signature" id="signature-input">

            <div class="form-row">
                <!-- Template Selection -->
                <div class="form-group" style="flex:1;">
                    <label class="form-label">Select Background Template</label>
                    <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:1rem; margin-top:0.5rem;">
                        @foreach($templates as $template)
                        <label style="cursor:pointer; position:relative;">
                            <input type="radio" name="certificate_template" value="{{ $template }}" {{ ($event->certificate_template ?? 'certi1.png') == $template ? 'checked' : '' }} style="position:absolute; opacity:0;">
                            <div class="template-preview" style="border:2px solid var(--border-color); border-radius:var(--radius-md); overflow:hidden; transition:all 0.2s;">
                                <img src="{{ asset('assets/certificates/' . $template) }}" style="width:100%; display:block;">
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <!-- Lecturer Selection -->
                <div class="form-group" style="flex:1;">
                    <label class="form-label">Select Signing Lecturer</label>
                    <select name="lecturer_id" class="form-input" required>
                        <option value="">-- Select Lecturer --</option>
                        @foreach($lecturers as $lecturer)
                            <option value="{{ $lecturer->id }}" {{ $event->lecturer_id == $lecturer->id ? 'selected' : '' }}>
                                {{ $lecturer->name }} {{ !$lecturer->signature ? '(No Signature Uploaded)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    <p style="font-size:0.75rem;color:var(--text-muted);margin-top:0.5rem;">Lecturers must upload their digital signature in their profile.</p>
                </div>
            </div>

            <div class="form-row mt-3">
                <!-- Event Logo -->
                <div class="form-group" style="flex:1;">
                    <label class="form-label">Event Logo (Upload)</label>
                    <input type="file" name="event_logo" class="form-input" accept="image/*">
                    <p style="font-size:0.75rem;color:var(--text-muted);margin-top:0.375rem;">Will be displayed on the top right of the certificate.</p>
                    @if($event->event_logo)
                        <div style="margin-top:0.5rem;">
                            <img src="{{ asset('assets/' . $event->event_logo) }}" style="max-height:50px; border:1px solid var(--border-color); padding:2px;">

                        </div>
                    @endif
                </div>

                <!-- Organizer Signature Pad/Upload -->
                <div class="form-group" style="flex:2;">
                    <label class="form-label">Organizer Signature (Sign or Upload)</label>
                    
                    <div style="display:flex; gap:1rem; margin-bottom:1rem; border-bottom:1px solid var(--border-color); padding-bottom:0.5rem;">
                        <button type="button" class="tab-btn active" onclick="switchTab('draw')">Draw</button>
                        <button type="button" class="tab-btn" onclick="switchTab('upload')">Upload Photo</button>
                    </div>

                    <div id="draw-section">
                        <div style="background:white; border:1px solid var(--border-color); border-radius:var(--radius-md); max-width:400px;">
                            <canvas id="signature-pad" width="400" height="200" style="touch-action: none; cursor: url('data:image/svg+xml;utf8,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; width=&quot;24&quot; height=&quot;24&quot; viewBox=&quot;0 0 24 24&quot;><circle cx=&quot;12&quot; cy=&quot;12&quot; r=&quot;3&quot; fill=&quot;black&quot;/></svg>') 12 12, crosshair !important;"></canvas>
                        </div>
                        <div style="margin-top:0.5rem;">
                            <button type="button" class="btn btn-secondary btn-sm" id="clear-signature">Clear Signature</button>
                        </div>
                    </div>

                    <div id="upload-section" style="display:none;">
                        <input type="file" id="signature-upload" class="form-input" accept="image/*">
                        <p style="font-size:0.75rem;color:var(--text-muted);margin-top:0.5rem;">
                            <i class="fas fa-info-circle"></i> Use a plain white background.
                        </p>
                        <div id="preview-container" style="display:none; margin-top:1rem;">
                            <canvas id="process-canvas" style="border:1px solid var(--border-color); max-width:100%; background:#eee;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="action-group mt-4" style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <a href="{{ route('certificates.manage', $event) }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Design</button>
            </div>
        </form>
    </div>
</div>

<style>
    input[type="radio"]:checked + .template-preview {
        border-color: #980517 !important;
        box-shadow: 0 0 10px rgba(152, 5, 23, 0.2);
    }
    .tab-btn {
        background: none;
        border: none;
        padding: 0.375rem 0.75rem;
        cursor: pointer;
        font-weight: 600;
        color: #980517;
        font-size: 0.875rem;
    }
    .tab-btn.active {
        color: #980517;
        border-bottom: 2px solid #980517;
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

    // Image Processing
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
                const maxWidth = 800;
                const scale = img.width > maxWidth ? maxWidth / img.width : 1;
                processCanvas.width = img.width * scale;
                processCanvas.height = img.height * scale;
                ctx.drawImage(img, 0, 0, processCanvas.width, processCanvas.height);

                const imageData = ctx.getImageData(0, 0, processCanvas.width, processCanvas.height);
                const data = imageData.data;

                for (let i = 0; i < data.length; i += 4) {
                    const brightness = (data[i] + data[i+1] + data[i+2]) / 3;
                    if (brightness > 180) {
                        data[i + 3] = 0;
                    } else {
                        data[i] = 0; data[i+1] = 0; data[i+2] = 0;
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

    document.getElementById('design-form').addEventListener('submit', function (e) {
        const signatureInput = document.getElementById('signature-input');
        if (currentMode === 'draw') {
            if (!signaturePad.isEmpty()) {
                signatureInput.value = signaturePad.toDataURL();
            } else if (!signatureInput.value) { // Only error if nothing is there
                alert('Please provide organizer signature.');
                e.preventDefault();
                return;
            }
        } else {
            if (processedDataUrl) {
                signatureInput.value = processedDataUrl;
            } else if (!signatureInput.value) {
                alert('Please upload a signature photo.');
                e.preventDefault();
                return;
            }
        }
    });
</script>
@endpush
@endsection
