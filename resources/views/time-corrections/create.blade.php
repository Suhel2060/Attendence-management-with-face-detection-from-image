@extends('layouts.app')
@section('title', 'Request Time Correction')

@section('content')
<style>
    :root {
        --tc-primary: #4a90e2;
        --tc-primary-light: #e8f0fe;
        --tc-warning: #f0b90b;
        --tc-danger: #f6465d;
        --tc-success: #0ecb81;
        --tc-gray-50: #f8f9fc;
        --tc-gray-100: #f1f3f8;
        --tc-gray-200: #e2e6ef;
        --tc-gray-600: #6c757d;
        --tc-gray-800: #2d3748;
        --tc-gray-900: #1a202c;
        --tc-radius: 12px;
        --tc-radius-sm: 8px;
        --tc-shadow: 0 4px 20px rgba(0,0,0,0.06);
        --tc-transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .tc-card {
        background: #fff;
        border-radius: var(--tc-radius);
        box-shadow: var(--tc-shadow);
        border: 1px solid var(--tc-gray-100);
        overflow: hidden;
    }

    .tc-card-header {
        background: linear-gradient(135deg, #4a90e2, #357abd);
        color: #fff;
        padding: 1.5rem 2rem;
    }

    .tc-card-header h5 {
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }

    .tc-card-header p {
        margin: 0.3rem 0 0;
        opacity: 0.8;
        font-size: 0.88rem;
    }

    .tc-card-body {
        padding: 2rem;
    }

    .tc-today-card {
        background: var(--tc-gray-50);
        border-radius: var(--tc-radius-sm);
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid var(--tc-gray-200);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .tc-today-card .label {
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--tc-gray-600);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .tc-today-card .value {
        font-size: 1rem;
        font-weight: 600;
        color: var(--tc-gray-900);
    }

    .tc-form-label {
        display: block;
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--tc-gray-800);
        margin-bottom: 0.35rem;
    }

    .tc-input-group {
        position: relative;
    }

    .tc-input-group .tc-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--tc-gray-600);
        font-size: 0.9rem;
        pointer-events: none;
        opacity: 0.4;
    }

    .tc-input-group .form-control,
    .tc-input-group .form-select {
        padding: 0.55rem 1rem 0.55rem 2.2rem;
        border: 2px solid var(--tc-gray-100);
        border-radius: var(--tc-radius-sm);
        font-size: 0.88rem;
        transition: var(--tc-transition);
        background: var(--tc-gray-50);
    }

    .tc-input-group .form-control:focus,
    .tc-input-group .form-select:focus {
        border-color: var(--tc-primary);
        background: #fff;
        box-shadow: 0 0 0 4px rgba(74, 144, 226, 0.1);
    }

    .tc-input-group textarea.form-control {
        padding-left: 1rem;
    }

    .tc-type-options {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .tc-type-option {
        flex: 1;
        min-width: 120px;
    }

    .tc-type-option input {
        display: none;
    }

    .tc-type-option label {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.4rem;
        padding: 1rem 0.75rem;
        border: 2px solid var(--tc-gray-200);
        border-radius: var(--tc-radius-sm);
        cursor: pointer;
        transition: var(--tc-transition);
        text-align: center;
        background: #fff;
    }

    .tc-type-option label i {
        font-size: 1.3rem;
        color: var(--tc-gray-600);
    }

    .tc-type-option label span {
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--tc-gray-600);
    }

    .tc-type-option input:checked + label {
        border-color: var(--tc-primary);
        background: var(--tc-primary-light);
    }

    .tc-type-option input:checked + label i,
    .tc-type-option input:checked + label span {
        color: var(--tc-primary);
    }

    .tc-time-row {
        display: none;
    }

    .tc-time-row.active {
        display: block;
    }

    .tc-btn-primary {
        background: linear-gradient(135deg, #4a90e2, #357abd);
        border: none;
        color: #fff;
        padding: 0.65rem 2rem;
        border-radius: var(--tc-radius-sm);
        font-weight: 600;
        font-size: 0.9rem;
        transition: var(--tc-transition);
        cursor: pointer;
    }

    .tc-btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(74, 144, 226, 0.3);
        color: #fff;
    }

    .tc-btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .tc-btn-secondary {
        background: transparent;
        border: 2px solid var(--tc-gray-200);
        color: var(--tc-gray-600);
        padding: 0.65rem 1.75rem;
        border-radius: var(--tc-radius-sm);
        font-weight: 500;
        font-size: 0.9rem;
        transition: var(--tc-transition);
        cursor: pointer;
    }

    .tc-btn-secondary:hover {
        border-color: var(--tc-gray-300);
        background: var(--tc-gray-100);
    }

    @media (max-width: 576px) {
        .tc-card-body { padding: 1.25rem; }
        .tc-type-options { flex-direction: column; }
        .tc-type-option { min-width: 100%; }
    }
</style>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="tc-card">
            <div class="tc-card-header">
                <h5><i class="bi bi-clock-history"></i> Request Time Correction</h5>
                <p>Submit a request if you forgot to clock in or clock out</p>
            </div>
            <div class="tc-card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Today's Attendance Status --}}
                @if($attendance)
                <div class="tc-today-card">
                    <div>
                        <div class="label">Today's Attendance</div>
                        <div class="value">
                            @if($attendance->check_in && $attendance->check_out)
                                <span class="text-success"><i class="bi bi-check-circle-fill"></i> Completed</span>
                            @elseif($attendance->check_in)
                                <span class="text-warning"><i class="bi bi-clock-fill"></i> Clocked in at {{ \Carbon\Carbon::parse($attendance->check_in)->format('h:i A') }}</span>
                            @else
                                <span class="text-danger"><i class="bi bi-x-circle-fill"></i> Not clocked in</span>
                            @endif
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="label">Date</div>
                        <div class="value">{{ now()->format('M d, Y') }}</div>
                    </div>
                </div>
                @else
                <div class="tc-today-card">
                    <div>
                        <div class="label">Today's Attendance</div>
                        <div class="value text-danger"><i class="bi bi-x-circle-fill"></i> No record found</div>
                    </div>
                    <div class="text-end">
                        <div class="label">Date</div>
                        <div class="value">{{ now()->format('M d, Y') }}</div>
                    </div>
                </div>
                @endif

                <form method="POST" action="{{ route('time-corrections.store') }}">
                    @csrf

                    {{-- Date --}}
                    <div class="mb-3">
                        <label class="tc-form-label">Date of Correction</label>
                        <div class="tc-input-group">
                            <i class="bi bi-calendar tc-icon"></i>
                            <input type="date" name="date" class="form-control" value="{{ old('date', now()->toDateString()) }}" required max="{{ now()->toDateString() }}">
                        </div>
                    </div>

                    {{-- Correction Type --}}
                    <div class="mb-3">
                        <label class="tc-form-label">What needs to be corrected?</label>
                        <div class="tc-type-options" id="typeOptions">
                            <div class="tc-type-option">
                                <input type="radio" name="type" id="type_in" value="clock_in" {{ old('type') === 'clock_in' ? 'checked' : '' }}>
                                <label for="type_in">
                                    <i class="bi bi-sign-turn-left-fill"></i>
                                    <span>Clock In</span>
                                </label>
                            </div>
                            <div class="tc-type-option">
                                <input type="radio" name="type" id="type_out" value="clock_out" {{ old('type') === 'clock_out' ? 'checked' : '' }}>
                                <label for="type_out">
                                    <i class="bi bi-sign-turn-right-fill"></i>
                                    <span>Clock Out</span>
                                </label>
                            </div>
                            <div class="tc-type-option">
                                <input type="radio" name="type" id="type_both" value="both" {{ old('type') === 'both' ? 'checked' : '' }}>
                                <label for="type_both">
                                    <i class="bi bi-arrow-left-right"></i>
                                    <span>Both</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Time In --}}
                    <div class="mb-3 tc-time-row" id="timeInRow">
                        <label class="tc-form-label">Requested Clock In Time</label>
                        <div class="tc-input-group">
                            <i class="bi bi-clock tc-icon"></i>
                            <input type="time" name="requested_time_in" id="requested_time_in" class="form-control" value="{{ old('requested_time_in') }}">
                        </div>
                    </div>

                    {{-- Time Out --}}
                    <div class="mb-3 tc-time-row" id="timeOutRow">
                        <label class="tc-form-label">Requested Clock Out Time</label>
                        <div class="tc-input-group">
                            <i class="bi bi-clock tc-icon"></i>
                            <input type="time" name="requested_time_out" id="requested_time_out" class="form-control" value="{{ old('requested_time_out') }}">
                        </div>
                    </div>

                    {{-- Reason --}}
                    <div class="mb-4">
                        <label class="tc-form-label">Reason for Correction</label>
                        <div class="tc-input-group">
                            <textarea name="reason" class="form-control" rows="3" placeholder="Explain why you missed clocking in/out..." required minlength="10">{{ old('reason') }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex gap-3">
                        <button type="submit" class="tc-btn-primary"><i class="bi bi-send-fill me-1"></i> Submit Request</button>
                        <a href="{{ route('time-corrections.my-requests') }}" class="tc-btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const typeRadios = document.querySelectorAll('input[name="type"]');
    const timeInRow = document.getElementById('timeInRow');
    const timeOutRow = document.getElementById('timeOutRow');
    const timeInInput = document.getElementById('requested_time_in');
    const timeOutInput = document.getElementById('requested_time_out');
    const now = new Date();
    const currentTime = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');

    function toggleTimeFields() {
        const selected = document.querySelector('input[name="type"]:checked');
        if (!selected) {
            timeInRow.classList.remove('active');
            timeOutRow.classList.remove('active');
            return;
        }
        const val = selected.value;

        timeInRow.classList.toggle('active', val === 'clock_in' || val === 'both');
        timeOutRow.classList.toggle('active', val === 'clock_out' || val === 'both');

        // Set defaults
        if (val === 'clock_in' && !timeInInput.value) timeInInput.value = currentTime;
        if (val === 'clock_out' && !timeOutInput.value) timeOutInput.value = currentTime;
        if (val === 'both') {
            if (!timeInInput.value) timeInInput.value = currentTime;
            if (!timeOutInput.value) timeOutInput.value = currentTime;
        }
    }

    typeRadios.forEach(r => r.addEventListener('change', toggleTimeFields));
    toggleTimeFields();

    // Pre-fill current time for date = today
    const dateInput = document.querySelector('input[name="date"]');
    dateInput.addEventListener('change', function () {
        const selectedDate = this.value;
        const today = new Date().toISOString().split('T')[0];
        if (selectedDate !== today) {
            timeInInput.value = '';
            timeOutInput.value = '';
        } else {
            toggleTimeFields();
        }
    });
});
</script>
@endsection
