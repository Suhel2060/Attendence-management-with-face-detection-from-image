@extends('layouts.app')
@section('title', 'Face Enrollment')

@section('styles')
<style>
    .enrollment-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 2rem;
        border-radius: 16px;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 8px 32px rgba(102, 126, 234, 0.3);
    }

    .enrollment-header h2 {
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .enrollment-header p {
        opacity: 0.9;
        margin-bottom: 0;
    }

    .card-enroll {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        overflow: hidden;
        transition: transform 0.2s;
        height: 100%;
    }

    .card-enroll:hover {
        transform: translateY(-2px);
    }

    .card-enroll .card-header {
        border-bottom: none;
        padding: 1.25rem 1.5rem;
        font-weight: 600;
    }

    .card-enroll .card-body {
        padding: 1.75rem;
    }

    .form-select, .form-control {
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 0.65rem 1rem;
        transition: all 0.3s ease;
    }

    .form-select:focus, .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
    }

    .form-label {
        font-weight: 600;
        color: #2d3436;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .upload-zone {
        border: 2px dashed #d1d5db;
        border-radius: 12px;
        padding: 2.5rem 1.5rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background: #fafbfc;
    }

    .upload-zone:hover {
        border-color: #667eea;
        background: rgba(102, 126, 234, 0.03);
    }

    .upload-zone.dragover {
        border-color: #667eea;
        background: rgba(102, 126, 234, 0.08);
    }

    .upload-zone i {
        font-size: 2.5rem;
        color: #667eea;
        margin-bottom: 0.75rem;
    }

    .upload-zone h6 {
        font-weight: 600;
        color: #374151;
    }

    .upload-zone small {
        color: #9ca3af;
    }

    .upload-section {
        transition: all 0.3s ease;
    }

    .upload-section.hidden {
        display: none !important;
    }

    .preview-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
        margin-top: 1rem;
        max-height: 600px;
        overflow-y: auto;
        padding: 0.25rem;
    }

    .preview-item {
        position: relative;
        border-radius: 10px;
        overflow: hidden;
        aspect-ratio: 4 / 3;
        min-height: 200px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        border: 2px solid #e9ecef;
        background: #1a1a2e;
        background-size: contain;
        background-position: center;
        background-repeat: no-repeat;
    }

    .preview-item .badge-count {
        position: absolute;
        top: 4px;
        left: 4px;
        background: rgba(102, 126, 234, 0.85);
        color: white;
        font-size: 0.7rem;
        padding: 2px 8px;
        border-radius: 6px;
        line-height: 1.4;
        font-weight: 600;
    }

    .preview-item .remove-img {
        position: absolute;
        top: 4px;
        right: 4px;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: rgba(220, 53, 69, 0.85);
        color: white;
        border: none;
        font-size: 0.75rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        line-height: 1;
    }

    .preview-item .remove-img:hover {
        background: #dc3545;
        transform: scale(1.1);
    }

    .btn-enroll {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
        color: white;
        width: 100%;
    }

    .btn-enroll:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 24px rgba(102, 126, 234, 0.35);
        color: white;
    }

    .btn-enroll:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }

    .table-enrolled {
        margin-bottom: 0;
    }

    .table-enrolled thead th {
        background: #f8f9fa;
        color: #495057;
        font-weight: 600;
        padding: 0.9rem 1rem;
        border-bottom: 2px solid #e9ecef;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table-enrolled tbody td {
        padding: 0.8rem 1rem;
        vertical-align: middle;
        color: #4a4a4a;
    }

    .table-enrolled tbody tr {
        transition: all 0.2s;
    }

    .table-enrolled tbody tr:hover {
        background: rgba(102, 126, 234, 0.03);
    }

    .badge-enrolled {
        background: rgba(16, 185, 129, 0.1);
        color: #059669;
        padding: 0.35rem 0.8rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
    }

    .empty-state i {
        font-size: 3rem;
        color: #d1d5db;
        margin-bottom: 1rem;
    }

    .empty-state h6 {
        color: #6b7280;
        font-weight: 500;
    }

    .counter-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #667eea;
        color: white;
        font-size: 0.75rem;
        font-weight: 700;
        margin-left: 0.5rem;
    }

    .btn-remove {
        border-radius: 8px;
        padding: 0.35rem 0.85rem;
        font-size: 0.8rem;
        transition: all 0.2s;
    }

    .btn-remove:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
    }

    .employee-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 0.75rem;
    }

    .employee-avatar-placeholder {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #e9ecef;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.75rem;
        color: #6c757d;
        font-weight: 600;
        font-size: 0.85rem;
    }

    .employee-info {
        display: flex;
        align-items: center;
    }

    .employee-info strong {
        font-size: 0.9rem;
    }

    .employee-info small {
        display: block;
        color: #6b7280;
        font-size: 0.75rem;
    }

    @media (max-width: 768px) {
        .enrollment-header {
            padding: 1.25rem;
        }

        .card-enroll .card-body {
            padding: 1.25rem;
        }

        .preview-grid {
            grid-template-columns: 1fr;
        }
        .preview-item {
            min-height: 240px;
        }

        .table-enrolled thead {
            display: none;
        }

        .table-enrolled tbody td {
            display: block;
            text-align: right;
            padding: 0.5rem 0.75rem;
        }

        .table-enrolled tbody td::before {
            content: attr(data-label);
            float: left;
            font-weight: 600;
            color: #6c757d;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4">
    <div class="enrollment-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="bi bi-camera-fill me-2"></i>Face Enrollment</h2>
                <p>Register employee faces for the recognition system. Upload 5+ images per person.</p>
            </div>
            <div class="d-none d-md-block">
                <span class="badge badge-enrolled">
                    <i class="bi bi-people-fill me-1"></i> {{ count($enrolled) }} enrolled
                </span>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Enrollment Form -->
        <div class="col-lg-5">
            <div class="card-enroll card">
                <div class="card-header bg-white">
                    <i class="bi bi-person-plus-fill me-2 text-primary"></i>New Enrollment
                </div>
                <div class="card-body">
                    <form action="{{ route('enrollment.store') }}" method="POST" enctype="multipart/form-data" id="enrollForm">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label">
                                <i class="bi bi-person-badge me-1"></i>Select Employee
                            </label>
                            <select name="employee_id" class="form-select" id="employeeSelect" required>
                                <option value="" disabled selected>-- Choose an employee --</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->employee_id }}" {{ in_array($emp->employee_id, $enrolled) ? 'disabled' : '' }}>
                                        {{ $emp->employee_id }} — {{ $emp->name }}
                                        @if(in_array($emp->employee_id, $enrolled))
                                            (already enrolled)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @if(count($employees) == count($enrolled))
                                <div class="text-warning mt-2 small">
                                    <i class="bi bi-info-circle me-1"></i>All employees are already enrolled.
                                </div>
                            @endif
                        </div>

                        <div class="mb-4">
                            <label class="form-label">
                                <i class="bi bi-images me-1"></i>Face Images
                                <span class="text-muted fw-normal">(minimum 5 required)</span>
                            </label>

                            <div class="upload-section hidden">
                                <div class="upload-zone" id="uploadZone">
                                    <i class="bi bi-cloud-arrow-up-fill"></i>
                                    <h6>Drag & drop images here</h6>
                                    <small>or click to browse (hold Ctrl to select multiple)</small>
                                    <input type="file" id="imageInput" class="d-none" accept="image/*" multiple>
                                </div>

                                <div class="preview-grid" id="previewGrid"></div>

                                <div class="d-flex justify-content-between mt-2">
                                    <small class="text-muted">Supported: JPG, PNG</small>
                                    <small id="imageCount" class="fw-bold text-muted">0 selected</small>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn-enroll" id="submitBtn" disabled>
                            <i class="bi bi-shield-check me-1"></i> Enroll Employee
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Enrolled List -->
        <div class="col-lg-7">
            <div class="card-enroll card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span>
                        <i class="bi bi-people-fill me-2 text-success"></i>Enrolled Employees
                    </span>
                    <span class="counter-badge">{{ count($enrolled) }}</span>
                </div>
                <div class="card-body p-0">
                    @if(count($enrolled) > 0)
                        <div class="table-responsive">
                            <table class="table table-enrolled">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Department</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($enrolled as $empId)
                                        @php $user = $employees->firstWhere('employee_id', $empId); @endphp
                                        <tr>
                                            <td data-label="Employee">
                                                <div class="employee-info">
                                                    @if($user && $user->image)
                                                        <img src="{{ asset('storage/'.$user->image) }}" class="employee-avatar">
                                                    @else
                                                        <span class="employee-avatar-placeholder">
                                                            {{ $user ? substr($user->name, 0, 1) : '?' }}
                                                        </span>
                                                    @endif
                                                    <div>
                                                        <strong>{{ $user ? $user->name : 'Unknown' }}</strong>
                                                        <small>{{ $empId }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td data-label="Department">
                                                <span class="badge badge-enrolled">{{ $user ? $user->department : '-' }}</span>
                                            </td>
                                            <td data-label="Action" class="text-center">
                                                <button type="button" class="btn btn-outline-primary btn-remove me-1 edit-btn"
                                                    data-id="{{ $empId }}"
                                                    data-name="{{ $user ? $user->name : 'Unknown' }}">
                                                    <i class="bi bi-pencil-square me-1"></i> Edit
                                                </button>
                                                <form action="{{ route('enrollment.destroy', $empId) }}" method="POST" class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-outline-danger btn-remove delete-btn">
                                                        <i class="bi bi-trash3 me-1"></i> Remove
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="bi bi-emoji-neutral"></i>
                            <h6>No employees enrolled yet</h6>
                            <p class="text-muted small">Select an employee and upload face images to get started.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<!-- Image Preview Lightbox -->
<div id="previewLightbox" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;z-index:99999;background:rgba(0,0,0,0.9);cursor:pointer;">
    <button type="button" id="previewCloseBtn" style="position:absolute;top:20px;right:30px;z-index:10;background:none;border:none;color:white;font-size:2rem;cursor:pointer;">&times;</button>
    <img id="previewFullImg" src="" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);max-width:90%;max-height:90%;object-fit:contain;">
</div>

<!-- Edit Enrollment Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form class="modal-content" id="editForm">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Update Enrollment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">Update face images for <strong id="editEmployeeName"></strong></p>
                <p class="text-muted small mb-3"><i class="bi bi-info-circle me-1"></i>Existing images are loaded below. Delete unwanted ones, then add new images to reach at least 5 total.</p>

                <div class="mb-3">
                    <label class="form-label">
                        <i class="bi bi-images me-1"></i>Face Images
                        <span class="text-muted fw-normal">(minimum 5 total)</span>
                    </label>
                    <div class="upload-zone" id="editUploadZone" style="padding: 1.5rem;">
                        <i class="bi bi-cloud-arrow-up-fill"></i>
                        <h6>Add more images</h6>
                        <small>drag & drop or click to browse</small>
                        <input type="file" id="editImageInput" class="d-none" accept="image/*" multiple>
                    </div>
                    <div class="preview-grid" id="editPreviewGrid"></div>
                    <div class="d-flex justify-content-between mt-2">
                        <small class="text-muted">Delete unwanted images with <i class="bi bi-x-circle-fill text-danger"></i></small>
                        <small id="editImageCount" class="fw-bold text-muted">0 selected</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn-enroll" id="editSubmitBtn" disabled style="width: auto; padding: 0.5rem 1.5rem;">
                    <i class="bi bi-shield-check me-1"></i> Update
                </button>
            </div>
        </form>
    </div>
</div>

@section('scripts')
<script>
    const enrolledImages = @json($enrolledImages);

    const uploadZone = document.getElementById('uploadZone');
    const imageInput = document.getElementById('imageInput');
    const previewGrid = document.getElementById('previewGrid');
    const imageCount = document.getElementById('imageCount');
    const submitBtn = document.getElementById('submitBtn');
    const employeeSelect = document.getElementById('employeeSelect');
    const enrollForm = document.getElementById('enrollForm');
    const uploadSection = document.querySelector('.upload-section');
    let droppedFiles = [];

    uploadZone.addEventListener('click', () => imageInput.click());

    uploadZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadZone.classList.add('dragover');
    });

    uploadZone.addEventListener('dragleave', () => {
        uploadZone.classList.remove('dragover');
    });

    uploadZone.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadZone.classList.remove('dragover');
        if (e.dataTransfer.files.length) {
            const newFiles = Array.from(e.dataTransfer.files).filter(f => f.type.startsWith('image/'));
            droppedFiles = [...droppedFiles, ...newFiles];
            updatePreview(droppedFiles);
        }
    });

    imageInput.addEventListener('change', () => {
        const newFiles = Array.from(imageInput.files);
        droppedFiles = [...droppedFiles, ...newFiles];
        imageInput.value = '';
        updatePreview(droppedFiles);
    });

    function updatePreview(files) {
        previewGrid.innerHTML = '';
        imageCount.textContent = files.length + ' selected';
        submitBtn.disabled = files.length < 5 || !employeeSelect.value;

        files.forEach((file, i) => {
            const url = URL.createObjectURL(file);
            const div = document.createElement('div');
            div.className = 'preview-item';
            div.innerHTML = `
                <img src="${url}" style="width:100%;height:100%;object-fit:contain;display:block;">
                <span class="badge-count">${i + 1}</span>
                <button class="remove-img" data-index="${i}">&times;</button>
            `;
            previewGrid.appendChild(div);
        });
    }

    const showPreview = (src) => {
        document.getElementById('previewFullImg').src = src;
        document.getElementById('previewLightbox').style.display = 'block';
    };
    const hidePreview = () => {
        document.getElementById('previewLightbox').style.display = 'none';
    };
    document.getElementById('previewLightbox').addEventListener('click', (e) => {
        if (e.target === e.currentTarget || e.target.id === 'previewCloseBtn') hidePreview();
    });

    previewGrid.addEventListener('click', (e) => {
        const btn = e.target.closest('.remove-img');
        if (btn) {
            const idx = parseInt(btn.dataset.index);
            droppedFiles.splice(idx, 1);
            updatePreview(droppedFiles);
            return;
        }
        const item = e.target.closest('.preview-item');
        if (item) {
            const img = item.querySelector('img');
            if (img) showPreview(img.src);
        }
    });

    employeeSelect.addEventListener('change', () => {
        if (employeeSelect.value) {
            uploadSection.classList.remove('hidden');
        } else {
            uploadSection.classList.add('hidden');
        }
        submitBtn.disabled = !employeeSelect.value || droppedFiles.length < 5;
    });

    enrollForm.addEventListener('submit', function(e) {
        e.preventDefault();

        if (!employeeSelect.value || droppedFiles.length < 5) return;

        const formData = new FormData();
        formData.append('employee_id', employeeSelect.value);
        droppedFiles.forEach((file, i) => {
            formData.append('images[]', file);
        });

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Enrolling...';

        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(res => res.json().then(data => ({ status: res.status, data })))
        .then(({ status, data }) => {
            if (status === 200 || status === 201) {
                Swal.fire({
                    icon: 'success',
                    title: 'Enrolled!',
                    text: data.message || 'Employee enrolled successfully.',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => location.reload());
            } else {
                throw new Error(data.message || data.error || 'Enrollment failed');
            }
        })
        .catch(err => {
            Swal.fire({
                icon: 'error',
                title: 'Failed',
                text: err.message
            });
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-shield-check me-1"></i> Enroll Employee';
        });
    });

    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const form = this.closest('.delete-form');
            Swal.fire({
                title: 'Remove Enrollment?',
                text: "This employee will no longer be recognized by the face system.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, remove',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
        });
    });

    // --- Edit Enrollment ---
    let editKeptFilenames = [];
    let editNewFiles = [];
    const editModal = document.getElementById('editModal');
    const editUploadZone = document.getElementById('editUploadZone');
    const editImageInput = document.getElementById('editImageInput');
    const editPreviewGrid = document.getElementById('editPreviewGrid');
    const editImageCount = document.getElementById('editImageCount');
    const editSubmitBtn = document.getElementById('editSubmitBtn');
    const editForm = document.getElementById('editForm');
    let editingEmployeeId = null;

    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            editingEmployeeId = this.dataset.id;
            document.getElementById('editEmployeeName').textContent = this.dataset.name;

            const existing = enrolledImages[editingEmployeeId] || [];
            editKeptFilenames = existing.map(img => img.name);
            editNewFiles = [];
            renderEditPreview();
            const modal = new bootstrap.Modal(editModal);
            modal.show();
        });
    });

    editUploadZone.addEventListener('click', () => editImageInput.click());

    editUploadZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        editUploadZone.classList.add('dragover');
    });

    editUploadZone.addEventListener('dragleave', () => {
        editUploadZone.classList.remove('dragover');
    });

    editUploadZone.addEventListener('drop', (e) => {
        e.preventDefault();
        editUploadZone.classList.remove('dragover');
        if (e.dataTransfer.files.length) {
            const newFiles = Array.from(e.dataTransfer.files).filter(f => f.type.startsWith('image/'));
            editNewFiles = [...editNewFiles, ...newFiles];
            renderEditPreview();
        }
    });

    editImageInput.addEventListener('change', () => {
        const newFiles = Array.from(editImageInput.files);
        editNewFiles = [...editNewFiles, ...newFiles];
        editImageInput.value = '';
        renderEditPreview();
    });

    function renderEditPreview() {
        editPreviewGrid.innerHTML = '';
        const total = editKeptFilenames.length + editNewFiles.length;
        editImageCount.textContent = total + ' selected';
        editSubmitBtn.disabled = total < 5;
        let idx = 0;

        editKeptFilenames.forEach((name) => {
            const imgs = enrolledImages[editingEmployeeId] || [];
            const found = imgs.find(i => i.name === name);
            if (!found) return;
            const div = document.createElement('div');
            div.className = 'preview-item';
            div.innerHTML = `
                <img src="${found.url}" style="width:100%;height:100%;object-fit:contain;display:block;">
                <span class="badge-count">${++idx}</span>
                <button class="remove-img" data-type="existing" data-name="${name}">&times;</button>
            `;
            editPreviewGrid.appendChild(div);
        });

        editNewFiles.forEach((file) => {
            const url = URL.createObjectURL(file);
            const div = document.createElement('div');
            div.className = 'preview-item';
            div.innerHTML = `
                <img src="${url}" style="width:100%;height:100%;object-fit:contain;display:block;">
                <span class="badge-count">${++idx}</span>
                <button class="remove-img" data-type="new" data-idx="${editNewFiles.indexOf(file)}">&times;</button>
            `;
            editPreviewGrid.appendChild(div);
        });
    }

    editPreviewGrid.addEventListener('click', (e) => {
        const btn = e.target.closest('.remove-img');
        if (btn) {
            const type = btn.dataset.type;
            if (type === 'existing') {
                const name = btn.dataset.name;
                editKeptFilenames = editKeptFilenames.filter(n => n !== name);
            } else {
                const idx = parseInt(btn.dataset.idx);
                editNewFiles.splice(idx, 1);
            }
            renderEditPreview();
            return;
        }
        const item = e.target.closest('.preview-item');
        if (item) {
            const img = item.querySelector('img');
            if (img) showPreview(img.src);
        }
    });

    editForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const total = editKeptFilenames.length + editNewFiles.length;
        if (!editingEmployeeId || total < 5) return;

        const formData = new FormData();
        formData.append('employee_id', editingEmployeeId);
        editKeptFilenames.forEach(name => formData.append('keep_images[]', name));
        editNewFiles.forEach(file => formData.append('new_images[]', file));

        editSubmitBtn.disabled = true;
        editSubmitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Updating...';

        fetch('{{ route("enrollment.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(res => res.json().then(data => ({ status: res.status, data })))
        .then(({ status, data }) => {
            if (status === 200 || status === 201) {
                bootstrap.Modal.getInstance(editModal).hide();
                Swal.fire({
                    icon: 'success',
                    title: 'Updated!',
                    text: data.message || 'Face images updated successfully.',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => location.reload());
            } else {
                throw new Error(data.message || data.error || 'Update failed');
            }
        })
        .catch(err => {
            Swal.fire({ icon: 'error', title: 'Failed', text: err.message });
            editSubmitBtn.disabled = false;
            editSubmitBtn.innerHTML = '<i class="bi bi-shield-check me-1"></i> Update';
        });
    });
</script>
@endsection
