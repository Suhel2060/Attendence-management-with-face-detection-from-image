@extends('layouts.app')

@section('title', 'My Attendance')

@section('content')
<div class="container py-5">
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary">My Attendance</h2>
        <button id="clockButton" class="btn btn-lg btn-success shadow-sm">
            <i class="fas fa-clock me-2"></i>{{ $clockbutton }}
        </button>
    </div>

    {{-- Attendance Table --}}
    <div class="card border-0 shadow-lg overflow-hidden" id="attendanceTable">
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
    </div>
</div>

{{-- Camera Modal --}}
<div id="cameraSection" class="card shadow-lg border-0 mx-auto" style="display:none; max-width:480px;">
    <div class="card-header bg-primary text-white">
        <h5 class="card-title mb-0">Capture Verification Photo</h5>
    </div>
    <div class="card-body p-4">
        <div class="ratio ratio-4x3">
            <video id="video" class="rounded-3" autoplay muted playsinline></video>
            <canvas id="cameraCanvas" class="rounded-3 d-none"></canvas>
        </div>
        <div class="d-grid gap-2 mt-4">
            <button id="captureBtn" class="btn btn-primary">
                <i class="fas fa-camera me-2"></i>Capture
            </button>
            <button id="finishBtn" class="btn btn-success d-none" >
                <i class="fas fa-check me-2"></i>
                <span class="submit-text">Submit</span>
                <span class="spinner-border spinner-border-sm d-none" role="status"></span>
            </button>
            <button id="cancelBtn" class="btn btn-outline-secondary">
                <i class="fas fa-times me-2"></i>Cancel
            </button>
        </div>
    </div>
</div>

<style>
    #cameraSection {
        transition: transform 0.2s ease;
    }
    .ratio-4x3 {
        aspect-ratio: 4 / 3;
    }
    video, canvas {
        background: #f8f9fa;
        object-fit: cover;
    }
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const elements = {
        clockButton: document.getElementById('clockButton'),
        cameraSection: document.getElementById('cameraSection'),
        attendanceDiv: document.getElementById('attendanceTable'),
        video: document.getElementById('video'),
        canvas: document.getElementById('cameraCanvas'),
        captureBtn: document.getElementById('captureBtn'),
        finishBtn: document.getElementById('finishBtn'),
        cancelBtn: document.getElementById('cancelBtn')
    };

    let stream = null;

    // Toggle UI Elements
    const toggleUI = (showCamera) => {
        elements.clockButton.style.display = showCamera ? 'none' : 'inline-flex';
        elements.attendanceDiv.style.display = showCamera ? 'none' : 'block';
        elements.cameraSection.style.display = showCamera ? 'block' : 'none';
    };

    // Initialize Camera
    const initializeCamera = async () => {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: true });
            elements.video.srcObject = stream;
            await elements.video.play();
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Camera Error',
                text: `Could not access camera: ${error.message}`,
                willClose: () => location.reload()
            });
        }
    };

    // Handle Clock Button Click
    elements.clockButton.addEventListener('click', async () => {
        toggleUI(true);
        await initializeCamera();
    });

    // Capture Photo
    elements.captureBtn.addEventListener('click', () => {
        elements.canvas.width = elements.video.videoWidth;
        elements.canvas.height = elements.video.videoHeight;
        elements.canvas.getContext('2d').drawImage(elements.video, 0, 0);
        
        elements.video.classList.add('d-none');
        elements.canvas.classList.remove('d-none');
        elements.captureBtn.classList.add('d-none');
        elements.finishBtn.classList.remove('d-none');
    });

    // // Submit Attendance
    // elements.finishBtn.addEventListener('click', async () => {
    //     const imageData = elements.canvas.toDataURL('image/png');
        
    //     try {
    //         await $.ajax({
    //             url: "{{ route('attendance.store') }}",
    //             method: 'POST',
    //             headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
    //             data: { image: imageData }
    //         });
            
    //         await Swal.fire({
    //             icon: 'success',
    //             title: 'Success',
    //             text: 'Attendance recorded successfully',
    //             willClose: () => location.reload()
    //         });
    //     } catch (error) {
    //         Swal.fire({
    //             icon: 'error',
    //             title: 'Submission Error',
    //             text: 'Could not record attendance',
    //             willClose: () => location.reload()
    //         });
    //     }
    // });


    // Submit Attendance
elements.finishBtn.addEventListener('click', async () => {
    try {
        // Disable button and show loading
        elements.finishBtn.disabled = true;  // This is the correct place to disable
        elements.finishBtn.querySelector('.submit-text').textContent = 'Processing...';
        elements.finishBtn.querySelector('.fa-check').classList.add('d-none');
        elements.finishBtn.querySelector('.spinner-border').classList.remove('d-none');

        const imageData = elements.canvas.toDataURL('image/png');
        
        await $.ajax({
            url: "{{ route('attendance.store') }}",
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            data: { image: imageData }
        });
        
        await Swal.fire({
            icon: 'success',
            title: 'Success',
            text: 'Attendance recorded successfully',
            willClose: () => location.reload()
        });
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Submission Error',
            text: 'Could not record attendance',
            willClose: () => location.reload()
        });
    } finally {
        // Re-enable button regardless of success/error
        elements.finishBtn.disabled = false;
        elements.finishBtn.querySelector('.submit-text').textContent = 'Submit';
        elements.finishBtn.querySelector('.fa-check').classList.remove('d-none');
        elements.finishBtn.querySelector('.spinner-border').classList.add('d-none');
    }
});
    // Cancel Process
    elements.cancelBtn.addEventListener('click', () => {
        if (stream) stream.getTracks().forEach(track => track.stop());
        
        elements.video.classList.remove('d-none');
        elements.canvas.classList.add('d-none');
        elements.captureBtn.classList.remove('d-none');
        elements.finishBtn.classList.add('d-none');
        
        toggleUI(false);
        elements.canvas.getContext('2d').clearRect(0, 0, elements.canvas.width, elements.canvas.height);
    });
});
</script>
@endsection