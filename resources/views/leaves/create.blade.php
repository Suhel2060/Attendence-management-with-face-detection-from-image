@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-gradient-primary text-white py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0"><i class="bi bi-calendar-plus me-2"></i> New Leave Request</h3>
                        <div class="badge bg-white text-primary p-2">
                            {{ now()->format('F Y') }}
                        </div>
                    </div>
                </div>

                <div class="card-body p-5">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center">
                            <i class="bi bi-check-circle-fill me-2 fs-4"></i>
                            <div>{{ session('success') }}</div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="row mb-5">
                        <div class="col-md-8">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title text-primary mb-4">
                                        <i class="bi bi-info-circle me-2"></i>Leave Balances
                                    </h5>
                                    
                                    <div class="row">
                                        @foreach($leaveTypes as $type)
                                        <div class="col-md-6 mb-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="fw-bold">{{ $type->name }}:</span>
                                                <div>
                                                    <span class="badge bg-success rounded-pill">
                                                        {{ $balances[$type->id]['remaining'] }}/{{ $type->max_days }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="progress mt-2" style="height: 8px;">
                                                @php
                                                    $percentage = ($balances[$type->id]['taken'] / $type->max_days) * 100;
                                                @endphp
                                                <div class="progress-bar bg-{{ $percentage > 80 ? 'danger' : 'success' }}" 
                                                    role="progressbar" 
                                                    style="width: {{ $percentage }}%"
                                                    aria-valuenow="{{ $percentage }}" 
                                                    aria-valuemin="0" 
                                                    aria-valuemax="100">
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 bg-light-warning shadow-sm">
                                <div class="card-body text-center p-4">
                                    <i class="bi bi-exclamation-triangle fs-1 text-warning mb-3"></i>
                                    <h5>Leave Policy</h5>
                                    <p class="small mb-0">
                                        All leave requests must be submitted at least 3 days in advance. 
                                        Annual leave resets on January 1st.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h5 class="card-title text-primary mb-4">
                                <i class="bi bi-pencil-square me-2"></i>Request Details
                            </h5>
                            
                            <form method="POST" action="{{ route('leaves.store') }}">
                                @csrf

                                <div class="row mb-4">
                                    <!-- Leave Type -->
                                    <div class="col-md-6 mb-4">
                                        <label for="leave_type_id" class="form-label fw-bold">Leave Type <span class="text-danger">*</span></label>
                                        <select class="form-select form-select-lg @error('leave_type_id') is-invalid @enderror" 
                                                id="leave_type_id" name="leave_type_id" required>
                                            <option value="" disabled selected>Select Leave Type</option>
                                            @foreach($leaveTypes as $type)
                                                <option value="{{ $type->id }}" 
                                                    {{ old('leave_type_id') == $type->id ? 'selected' : '' }}
                                                    data-balance="{{ $balances[$type->id]['remaining'] }}">
                                                    {{ $type->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('leave_type_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <!-- Balance Indicator -->
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label fw-bold">Available Balance</label>
                                        <div class="alert alert-light d-flex align-items-center" id="balance-display">
                                            <i class="bi bi-calendar-check me-2"></i>
                                            <span id="balance-text">Select a leave type</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Date Range -->
                                <div class="row mb-4">
                                    <div class="col-md-6 mb-4">
                                        <label for="start_date" class="form-label fw-bold">Start Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control form-control-lg @error('start_date') is-invalid @enderror" 
                                               id="start_date" name="start_date" value="{{ old('start_date') }}" min="{{ now()->format('Y-m-d') }}" required>
                                        @error('start_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label for="end_date" class="form-label fw-bold">End Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control form-control-lg @error('end_date') is-invalid @enderror" 
                                               id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                                        @error('end_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Duration Preview -->
                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <div class="alert alert-info d-flex align-items-center">
                                            <i class="bi bi-info-circle me-2"></i>
                                            <span id="duration-preview">Selected duration will appear here</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Reason -->
                                <div class="mb-4">
                                    <label for="reason" class="form-label fw-bold">Reason <span class="text-danger">*</span></label>
                                    <textarea class="form-control form-control-lg @error('reason') is-invalid @enderror" 
                                              id="reason" name="reason" rows="4" 
                                              placeholder="Please explain the reason for your leave..." required>{{ old('reason') }}</textarea>
                                    @error('reason')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Balance Error -->
                                @if($errors->has('balance'))
                                    <div class="alert alert-danger alert-dismissible fade show mt-4">
                                        <i class="bi bi-exclamation-octagon me-2"></i>
                                        {{ $errors->first('balance') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif

                                <div class="d-flex justify-content-end mt-5">
                                    <button type="reset" class="btn btn-light btn-lg me-3">
                                        <i class="bi bi-eraser me-2"></i>Reset Form
                                    </button>
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="bi bi-send-check me-2"></i>Submit Request
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const startDate = document.getElementById('start_date');
        const endDate = document.getElementById('end_date');
        const leaveType = document.getElementById('leave_type_id');
        const balanceDisplay = document.getElementById('balance-text');
        const durationPreview = document.getElementById('duration-preview');
        
        // Initialize min end date
        if(startDate.value) {
            endDate.min = startDate.value;
        }
        
        // Set min end date based on start date
        startDate.addEventListener('change', function() {
            endDate.min = this.value;
            calculateDuration();
        });
        
        endDate.addEventListener('change', calculateDuration);
        
        // Update balance display when leave type changes
        leaveType.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const balance = selectedOption.getAttribute('data-balance');
            
            if(balance) {
                balanceDisplay.innerHTML = `<span class="fw-bold">${balance} days</span> available`;
            } else {
                balanceDisplay.textContent = 'Select a leave type';
            }
        });
        
        // Calculate and display duration
        function calculateDuration() {
            if(startDate.value && endDate.value) {
                const start = new Date(startDate.value);
                const end = new Date(endDate.value);
                
                // Calculate difference in days (inclusive)
                const diffDays = Math.floor((end - start) / (1000 * 60 * 60 * 24)) + 1;
                
                if(diffDays > 0) {
                    durationPreview.innerHTML = 
                        `<strong>${diffDays} days</strong> from ${start.toDateString()} to ${end.toDateString()}`;
                } else {
                    durationPreview.textContent = 'End date must be after start date';
                }
            }
        }
        
        // Initialize balance display if returning with error
        @if(old('leave_type_id'))
            setTimeout(() => {
                const selectedOption = leaveType.querySelector('option[selected]');
                if(selectedOption) {
                    const balance = selectedOption.getAttribute('data-balance');
                    balanceDisplay.innerHTML = `<span class="fw-bold">${balance} days</span> available`;
                }
            }, 100);
        @endif
    });
</script>

<style>
    .card {
        border-radius: 15px;
        overflow: hidden;
    }
    .card-header {
        border-radius: 15px 15px 0 0 !important;
    }
    .progress {
        border-radius: 10px;
    }
    .bg-gradient-primary {
        background: linear-gradient(135deg, #3a7bd5, #00d2ff);
    }
    .bg-light-warning {
        background-color: rgba(255, 193, 7, 0.1);
    }
</style>
@endsection