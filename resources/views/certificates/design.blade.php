@extends('layouts.app')
@section('title', 'Design Certificate — ' . $event->title)

@section('content')
<div class="card">
    <div class="card-body">
        <div class="certificate-layout">
            <!-- Live Preview -->
            <div class="preview-column">
                <label class="form-label">Live Preview</label>
                <div id="certificate-preview-container" style="width:100%; aspect-ratio: 1.414 / 1; background:#f8f9fa; border:1px solid var(--border-color); border-radius:var(--radius-md); position:relative; overflow:hidden; box-shadow:0 10px 30px rgba(0,0,0,0.1); container-type: inline-size;">
                    <!-- Background Template -->
                    <img id="preview-template" src="{{ asset('assets/certificates/' . ($event->certificate_template ?? 'certi1.png')) }}" style="width:100%; height:100%; object-fit:cover; position:absolute; top:0; left:0;">
                    
                    <!-- Certificate Content Overlays -->
                    <div style="position:relative; z-index:1; height:100%; display:flex; flex-direction:column; align-items:center; text-align:center;">
                        <img id="preview-logo" src="{{ $event->event_logo ? asset('assets/' . $event->event_logo) : '' }}" style="position:absolute; top:4cqw; right:4cqw; max-height:8.9cqw; {{ $event->event_logo ? '' : 'display:none;' }}">
                        
                        <div style="margin-top:10.6cqw; width:100%;">
                            <div style="font-family:'Times New Roman', Times, serif; font-size:6.4cqw; font-weight:bold; margin-bottom:0; text-transform:uppercase; color:#000; line-height:1;">CERTIFICATE</div>
                            <div style="font-family:'Times New Roman', Times, serif; font-size:2.5cqw; font-weight:bold; margin-top:0; margin-bottom:3.5cqw; color:#000;">OF PARTICIPATION</div>
                            
                            <div style="font-family:Arial, Helvetica, sans-serif; font-size:1.6cqw; color:#444; margin-bottom:2.2cqw; text-transform:uppercase; letter-spacing:1px;">THIS CERTIFICATE IS PROUDLY PRESENTED TO</div>
                            
                            <div style="display:inline-block; margin-bottom:2.2cqw;">
                                <div style="font-family:'Alex Brush', cursive, serif; font-size:7.5cqw; color:#980517; margin-bottom:0.2cqw; padding:0 3.5cqw; line-height:1;">[Participant Name]</div>
                                <div style="height:1.5px; background:#000; width:100%;"></div>
                            </div>
                            
                            <div style="font-family:Arial, Helvetica, sans-serif; font-size:1.78cqw; color:#222; line-height:1.5; width:80%; margin:0 auto;">
                                For their active participation and successful completion of the <br>
                                <span style="font-weight:bold; font-size:2.13cqw; color:#000;">{{ $event->title }}</span><br>
                                held on {{ \Carbon\Carbon::parse($event->start_date)->format('d F Y') }} at {{ $event->location }}.
                            </div>
                        </div>

                        <div style="display:flex; justify-content:space-between; width:85%; position:absolute; bottom:5.3cqw; left:7.5cqw;">
                            <!-- Lecturer Signature -->
                            <div style="text-align:center; width:45%; display:flex; flex-direction:column; justify-content:flex-end; align-items:center;">
                                <div style="font-family:Arial, Helvetica, sans-serif; font-size:0.98cqw; text-transform:uppercase; font-weight:bold; color:#444; margin-bottom:0.4cqw;">Head of Department</div>
                                <div style="height:7.13cqw; display:flex; align-items:flex-end; justify-content:center; margin-bottom:0.2cqw;">
                                    <img id="preview-lecturer-sig" src="" style="max-height:100%; display:none;">
                                </div>
                                <div style="border-top:1.5px solid #000; width:21.3cqw; margin:0 auto 0.4cqw;"></div>
                                <div style="font-family:Arial, Helvetica, sans-serif; font-size:1.33cqw; font-weight:bold; color:#000;" id="preview-lecturer-name">Lecturer Name</div>
                            </div>

                            <!-- Organizer Signature -->
                            <div style="text-align:center; width:45%; display:flex; flex-direction:column; justify-content:flex-end; align-items:center;">
                                <div style="font-family:Arial, Helvetica, sans-serif; font-size:0.98cqw; text-transform:uppercase; font-weight:bold; color:#444; margin-bottom:0.4cqw;">General Manager</div>
                                <div style="height:7.13cqw; display:flex; align-items:flex-end; justify-content:center; margin-bottom:0.2cqw;">
                                    <img id="preview-organizer-sig" src="" style="max-height:100%; display:none;">
                                </div>
                                <div style="border-top:1.5px solid #000; width:21.3cqw; margin:0 auto 0.4cqw;"></div>
                                <div style="font-family:Arial, Helvetica, sans-serif; font-size:1.33cqw; font-weight:bold; color:#000;">{{ $event->creator->name ?? 'Organizer Name' }}</div>
                            </div>
                        </div>
                    </div>

                </div>
                <p style="font-size:0.75rem; color:var(--text-muted); margin-top:1rem; text-align:center;">
                    <i class="fas fa-info-circle"></i> This is a simplified preview. The final PDF will have higher quality and precise layout.
                </p>
            </div>

            <!-- Form -->
            <div class="form-column">
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
                                    <input type="radio" name="certificate_template" value="{{ $template }}" {{ ($event->certificate_template ?? 'certi1.png') == $template ? 'checked' : '' }} style="position:absolute; opacity:0;" onchange="updatePreviewTemplate(this.value)">
                                    <div class="template-preview" style="border:2px solid var(--border-color); border-radius:var(--radius-md); overflow:hidden; transition:all 0.2s;">
                                        <img src="{{ asset('assets/certificates/' . $template) }}" style="width:100%; display:block;">
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="form-row mt-3">
                        <!-- Lecturer Selection -->
                        <div class="form-group">
                            <label class="form-label">Select Signing Lecturer</label>
                            <select name="lecturer_id" class="form-input" required id="lecturer-select" onchange="updatePreviewLecturer(this)">
                                <option value="">-- Select Lecturer --</option>
                                @foreach($lecturers as $lecturer)
                                    <option value="{{ $lecturer->id }}" 
                                            data-name="{{ $lecturer->name }}" 
                                            data-sig="{{ $lecturer->signature ?? '' }}"
                                            {{ $event->lecturer_id == $lecturer->id ? 'selected' : '' }}>
                                        {{ $lecturer->name }} {{ !$lecturer->signature ? '(No Signature Uploaded)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Event Logo -->
                        <div class="form-group">
                            <label class="form-label">Event Logo (Upload)</label>
                            <input type="file" name="event_logo" class="form-input" accept="image/*" onchange="updatePreviewLogo(this)">
                            <p style="font-size:0.75rem;color:var(--text-muted);margin-top:0.375rem;">Will be displayed on the top right.</p>
                        </div>
                    </div>

                    <!-- Organizer Signature Pad/Upload -->
                    <div class="form-group mt-3">
                        <label class="form-label">Organizer Signature (Sign or Upload)</label>
                        
                        <div style="display:flex; gap:1rem; margin-bottom:1rem; border-bottom:1px solid var(--border-color); padding-bottom:0.5rem;">
                            <button type="button" class="tab-btn active" onclick="switchTab('draw')">Draw</button>
                            <button type="button" class="tab-btn" onclick="switchTab('upload')">Upload Photo</button>
                        </div>

                        <div id="draw-section">
                            <div style="background:white; border:1px solid var(--border-color); border-radius:var(--radius-md); max-width:400px;">
                                <canvas id="signature-pad" width="400" height="200" style="touch-action: none; cursor: default;"></canvas>
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

                </form>
            </div>
        </div>

        <div class="action-group" style="display: flex; justify-content: flex-end; width: 100%; border-top: 1px solid var(--border-color); padding-top: 1.5rem; margin-top: 1.5rem;">
            <div style="display: flex; gap: 0.75rem;">
                <a href="{{ route('certificates.manage', $event) }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
                <button type="submit" form="design-form" class="btn btn-primary"><i class="fas fa-save"></i> Save Design</button>
            </div>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Alex+Brush&display=swap');
    
    .certificate-layout {
        display: flex;
        gap: 2rem;
        flex-direction: row-reverse;
    }
    .preview-column {
        flex: 1;
        position: sticky;
        top: 1rem;
        align-self: flex-start;
    }
    .form-column {
        flex: 1.2;
    }
    
    input[type="radio"]:checked + .template-preview {
        border-color: #980517 !important;
        box-shadow: 0 0 10px rgba(152, 5, 23, 0.2);
    }
    .tab-btn {
        background: none; border: none; padding: 0.375rem 0.75rem;
        cursor: pointer; font-weight: 600; color: #980517; font-size: 0.875rem;
    }
    .tab-btn.active { color: #980517; border-bottom: 2px solid #980517; }

    @media (max-width: 1024px) {
        .certificate-layout { flex-direction: column; gap: 1.5rem; }
        .preview-column { position: relative; top: 0; }
        #signature-pad { width: 100% !important; height: auto !important; }
        .form-column { width: 100%; }
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

    function updatePreviewTemplate(val) {
        document.getElementById('preview-template').src = `{{ asset('assets/certificates/') }}/${val}`;
    }

    function updatePreviewLecturer(select) {
        const option = select.options[select.selectedIndex];
        const name = option.getAttribute('data-name');
        const sig = option.getAttribute('data-sig');
        
        document.getElementById('preview-lecturer-name').innerText = name || 'Lecturer Name';
        const sigImg = document.getElementById('preview-lecturer-sig');
        if (sig) {
            sigImg.src = sig;
            sigImg.style.display = 'block';
        } else {
            sigImg.style.display = 'none';
        }
    }

    function updatePreviewLogo(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('preview-logo');
                img.src = e.target.result;
                img.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function updatePreviewOrganizer(dataUrl) {
        const img = document.getElementById('preview-organizer-sig');
        if (dataUrl) {
            img.src = dataUrl;
            img.style.display = 'block';
        } else {
            img.style.display = 'none';
        }
    }

    // Initialize preview
    window.addEventListener('load', () => {
        updatePreviewLecturer(document.getElementById('lecturer-select'));
    });

    document.getElementById('clear-signature').addEventListener('click', () => {
        signaturePad.clear();
        updatePreviewOrganizer(null);
    });

    signaturePad.onEnd = function() {
        updatePreviewOrganizer(signaturePad.toDataURL());
    };

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
                updatePreviewOrganizer(processedDataUrl);
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
