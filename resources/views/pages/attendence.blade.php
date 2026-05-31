@extends('layouts.attendence')

@section('title', 'My Attendance')

@section('content')
<div class="container py-5">
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary">My Attendance</h2>
        {{-- <button id="clockButton" class="btn btn-lg btn-success shadow-sm">
            <i class="fas fa-clock me-2"></i>{{ $clockbutton }}
        </button> --}}
    </div>

    {{-- Attendance Table --}}
    {{-- <div class="card border-0 shadow-lg overflow-hidden" id="attendanceTable">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Date</th>
                        <th>Status</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $record)
                    <tr>
                        <td class="ps-4 fw-medium">{{ $record->date }}</td>
                        <td>
                            <span class="badge bg-{{
                                $record->status === 'present' ? 'success' : 
                                ($record->status === 'absent' ? 'danger' : 'warning')
                            }} bg-opacity-10 text-{{
                                $record->status === 'present' ? 'success' : 
                                ($record->status === 'absent' ? 'danger' : 'warning')
                            }}">
                                {{ ucfirst($record->status) }}
                            </span>
                        </td>
                        <td>{{ $record->check_in ?? '—' }}</td>
                        <td>{{ $record->check_out ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div> --}}
</div>

{{-- Camera Section --}}
<div id="cameraSection" class="card shadow-lg border-0 mx-auto" style="display:block; max-width:480px;">
    <div class="card-header bg-primary text-white">
        <h5 class="card-title mb-0">Attendance Verification</h5>
    </div>
    <div class="card-body p-4">
        <div class="position-relative mx-auto" style="max-width:640px;">
            <video id="video" autoplay muted playsinline style="width:100%;border-radius:8px;display:block;"></video>
            <canvas id="faceOverlay" style="position:absolute;top:0;left:0;"></canvas>
        </div>
        <div id="faceStatus" class="text-center mt-3">
            <span class="badge bg-secondary"><i class="fas fa-spinner fa-spin me-1"></i>Loading face detection model...</span>
        </div>
        <div id="welcomeSection" class="text-center mt-3 d-none">
            <div class="fs-5 fw-bold text-success"><i class="fas fa-smile me-1"></i><span id="welcomeName"></span></div>
            <small class="text-muted" id="welcomeId"></small>
        </div>
        <div class="d-grid gap-2 mt-4">
            <button id="clockInBtn" class="btn btn-success btn-lg d-none">
                <i class="fas fa-sign-in-alt me-2"></i>Clock In
            </button>
            <button id="clockOutBtn" class="btn btn-warning text-dark btn-lg d-none">
                <i class="fas fa-sign-out-alt me-2"></i>Clock Out
            </button>
            <div id="alreadyMarked" class="alert alert-success d-none text-center mb-0">
                <i class="fas fa-check-circle me-1"></i>Attendance already marked for today
            </div>
            <div id="notRecognized" class="alert alert-danger d-none text-center mb-0">
                <i class="fas fa-exclamation-circle me-1"></i>Face not recognized. Please ensure you are enrolled.
            </div>
        </div>
    </div>
</div>

<style>
    #cameraSection {
        transition: transform 0.2s ease;
    }
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
</style>

<script type="module">
import { FilesetResolver, FaceDetector } from 'https://cdn.jsdelivr.net/npm/@mediapipe/tasks-vision@0.10.3/vision_bundle.mjs';

document.addEventListener('DOMContentLoaded', async () => {
    const elements = {
        video: document.getElementById('video'),
        faceOverlay: document.getElementById('faceOverlay'),
        faceStatus: document.getElementById('faceStatus'),
        welcomeSection: document.getElementById('welcomeSection'),
        welcomeName: document.getElementById('welcomeName'),
        welcomeId: document.getElementById('welcomeId'),
        clockInBtn: document.getElementById('clockInBtn'),
        clockOutBtn: document.getElementById('clockOutBtn'),
        alreadyMarked: document.getElementById('alreadyMarked'),
        notRecognized: document.getElementById('notRecognized'),
    };

    let stream = null;
    let faceDetector = null;
    let faceDetected = false;
    let animationFrameId = null;
    let identifiedEmployee = null;
    let identifiedAction = null;
    let identifying = false;
    let processing = false;

    const speak = (text) => {
        if ('speechSynthesis' in window) {
            speechSynthesis.cancel();
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.rate = 1.0;
            utterance.pitch = 1.0;
            utterance.volume = 1.0;
            speechSynthesis.speak(utterance);
        }
    };

    const captureFrame = () => {
        const canvas = document.createElement('canvas');
        canvas.width = elements.video.videoWidth;
        canvas.height = elements.video.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(elements.video, 0, 0);
        return canvas.toDataURL('image/jpeg', 0.9);
    };

    const identifyFace = async () => {
        if (identifying || processing) return;
        identifying = true;

        try {
            const imageBase64 = captureFrame();
            const res = await fetch('{{ route("attendance.identify") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({ image: imageBase64 })
            });

            const data = await res.json();

            if (data.recognized) {
                identifiedEmployee = data.employee_id;
                identifiedAction = data.action;
                elements.welcomeName.textContent = data.name;
                elements.welcomeId.textContent = data.employee_id;
                elements.welcomeSection.classList.remove('d-none');
                elements.notRecognized.classList.add('d-none');
                elements.faceStatus.innerHTML = '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Identified</span>';

                elements.clockInBtn.classList.add('d-none');
                elements.clockOutBtn.classList.add('d-none');
                elements.alreadyMarked.classList.add('d-none');

                if (data.action === 'clock_in') {
                    elements.clockInBtn.classList.remove('d-none');
                    speak('Welcome ' + data.name + '. Ready to clock in.');
                } else if (data.action === 'clock_out') {
                    elements.clockOutBtn.classList.remove('d-none');
                    speak('Welcome ' + data.name + '. Ready to clock out.');
                } else if (data.action === 'finished') {
                    elements.alreadyMarked.classList.remove('d-none');
                    speak(data.name + ' already marked for today.');
                }
            } else {
                identifiedEmployee = null;
                identifiedAction = null;
                elements.welcomeSection.classList.add('d-none');
                elements.notRecognized.classList.remove('d-none');
                elements.clockInBtn.classList.add('d-none');
                elements.clockOutBtn.classList.add('d-none');
                elements.alreadyMarked.classList.add('d-none');
                elements.faceStatus.innerHTML = '<span class="badge bg-danger"><i class="fas fa-exclamation-circle me-1"></i>Not recognized</span>';
            }
        } catch (err) {
            console.error('Identify error:', err);
        } finally {
            identifying = false;
        }
    };

    const submitAttendance = async () => {
        if (processing) return;
        processing = true;

        try {
            const imageBase64 = captureFrame();
            const res = await fetch('{{ route("attendance.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({ image: imageBase64 })
            });

            const data = await res.json();

            if (res.ok) {
                stopCamera();
                speak('Attendance complete');
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: data.message,
                    confirmButtonColor: '#28a745'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: data.message || 'Unable to process attendance',
                    confirmButtonColor: '#dc3545'
                });
            }
        } catch (err) {
            console.error('Attendance submission error:', err);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Network error. Please try again.',
                confirmButtonColor: '#dc3545'
            });
        } finally {
            processing = false;
        }
    };

    const stopCamera = () => {
        if (animationFrameId) {
            cancelAnimationFrame(animationFrameId);
            animationFrameId = null;
        }
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
        elements.clockInBtn.classList.add('d-none');
        elements.clockOutBtn.classList.add('d-none');
        elements.faceStatus.innerHTML = '<span class="badge bg-secondary"><i class="fas fa-check-circle me-1"></i>Camera stopped</span>';
    };

    const initializeCamera = async () => {
        try {
            console.log('Requesting camera access...');
            stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } }
            });
            console.log('Camera access granted');
            elements.video.srcObject = stream;

            elements.video.addEventListener('play', () => {
                const displayWidth = elements.video.clientWidth;
                const displayHeight = elements.video.clientHeight;
                elements.faceOverlay.width = displayWidth;
                elements.faceOverlay.height = displayHeight;
                elements.faceOverlay.style.width = displayWidth + 'px';
                elements.faceOverlay.style.height = displayHeight + 'px';
                console.log('Video playing, canvas sized to:', displayWidth, 'x', displayHeight);
                startFaceDetection();
            });
        } catch (error) {
            console.error('Camera error:', error);
            elements.faceStatus.innerHTML = '<span class="badge bg-danger"><i class="fas fa-exclamation-circle me-1"></i>Camera access denied</span>';
        }
    };

    const loadMediaPipe = async () => {
        try {
            elements.faceStatus.innerHTML = '<span class="badge bg-secondary"><i class="fas fa-spinner fa-spin me-1"></i>Loading MediaPipe model...</span>';
            const vision = await FilesetResolver.forVisionTasks('https://cdn.jsdelivr.net/npm/@mediapipe/tasks-vision@0.10.3/wasm');

            faceDetector = await FaceDetector.createFromOptions(vision, {
                baseOptions: {
                    modelAssetPath: '{{ asset("models/blaze_face_short_range.tflite") }}'
                },
                runningMode: 'VIDEO'
            });
            console.log('MediaPipe loaded with local model');
            elements.faceStatus.innerHTML = '<span class="badge bg-secondary"><i class="fas fa-spinner fa-spin me-1"></i>Model ready - Starting camera...</span>';
            await initializeCamera();
        } catch (error) {
            console.error('MediaPipe failed:', error);
            elements.faceStatus.innerHTML = '<span class="badge bg-danger"><i class="fas fa-exclamation-circle me-1"></i>Failed to load detection model</span>';
        }
    };

    let identifyTimeout = null;

    const startFaceDetection = () => {
        const detectFrame = () => {
            if (elements.video.paused || elements.video.ended) {
                animationFrameId = requestAnimationFrame(detectFrame);
                return;
            }

            const displayWidth = elements.video.clientWidth;
            const displayHeight = elements.video.clientHeight;
            if (displayWidth === 0 || displayHeight === 0) {
                animationFrameId = requestAnimationFrame(detectFrame);
                return;
            }

            try {
                const results = faceDetector.detectForVideo(elements.video, performance.now());

                const ctx = elements.faceOverlay.getContext('2d');
                ctx.clearRect(0, 0, elements.faceOverlay.width, elements.faceOverlay.height);
                ctx.strokeStyle = '#00FF00';
                ctx.lineWidth = 2;
                ctx.fillStyle = 'rgba(0, 255, 0, 0.1)';

                let facesFound = false;

                results.detections.forEach(detection => {
                    facesFound = true;
                    const box = detection.boundingBox;
                    const x = (box.originX / elements.video.videoWidth) * displayWidth;
                    const y = (box.originY / elements.video.videoHeight) * displayHeight;
                    const w = (box.width / elements.video.videoWidth) * displayWidth;
                    const h = (box.height / elements.video.videoHeight) * displayHeight;

                    ctx.strokeRect(x, y, w, h);
                    ctx.fillRect(x, y, w, h);
                });

                if (facesFound) {
                    if (!faceDetected) {
                        faceDetected = true;
                        speak("Face detected. Identifying...");
                        elements.faceStatus.innerHTML = '<span class="badge bg-info"><i class="fas fa-spinner fa-spin me-1"></i>Identifying...</span>';
                        identifyTimeout = setTimeout(identifyFace, 500);
                    }
                } else {
                    if (faceDetected) {
                        faceDetected = false;
                        identifiedEmployee = null;
                        identifiedAction = null;
                        clearTimeout(identifyTimeout);
                        speak("Face not detected. Please position yourself in front of camera");
                        elements.faceStatus.innerHTML = '<span class="badge bg-warning text-dark"><i class="fas fa-exclamation-triangle me-1"></i>No face detected</span>';
                        elements.welcomeSection.classList.add('d-none');
                        elements.notRecognized.classList.add('d-none');
                        elements.clockInBtn.classList.add('d-none');
                        elements.clockOutBtn.classList.add('d-none');
                        elements.alreadyMarked.classList.add('d-none');
                    }
                }
            } catch (err) {
                console.error('Detection error:', err);
            }

            animationFrameId = requestAnimationFrame(detectFrame);
        };

        animationFrameId = requestAnimationFrame(detectFrame);
    };

    elements.clockInBtn.addEventListener('click', submitAttendance);
    elements.clockOutBtn.addEventListener('click', submitAttendance);

    await loadMediaPipe();
});
</script>
@endsection
