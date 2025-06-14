@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Attendance Management for {{ $user->name }}</h2>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#attendanceModal">
            <i class="fas fa-plus me-2"></i>Add Attendance
        </button>
    </div>

    <!-- Attendance Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Date</th>
                    <th>Check IN</th>
                    <th>Check OUT</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendance as $record)
                <tr>
                    <td>{{ $record->date->format('M d, Y') }}</td>
                    <td>{{ $record->check_in->format('h:i A') }}</td>
                    <td>{{ $record->check_out ? $record->check_out->format('h:i A') : '--' }}</td>
                    <td>
                        <select class="form-select form-select-sm" name="attendance[{{ $record->id }}]">
                            <option value="present" {{ $record->status == 'present' ? 'selected' : '' }}>Present</option>
                            <option value="absent" {{ $record->status == 'absent' ? 'selected' : '' }}>Absent</option>
                            <option value="leave" {{ $record->status == 'leave' ? 'selected' : '' }}>Leave</option>
                        </select>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-warning editAttendanceButton"
                                data-id="{{ $record->id }}"
                                data-employee-id="{{ $user->employee_id }}">
                            <i class="fas fa-edit"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-2"></i>Update All
        </button>
    </div>
</div>

<!-- Attendance Modal -->
<div class="modal fade" id="attendanceModal" tabindex="-1" aria-labelledby="attendanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="attendanceModalLabel">Add/Edit Attendance</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="attendanceForm" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="attendanceDate" class="form-label">Date</label>
                        <input type="date" class="form-control" id="attendanceDate" name="date" required>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col">
                            <label for="checkIn" class="form-label">Check In</label>
                            <input type="time" class="form-control" id="checkIn" name="check_in" required>
                        </div>
                        <div class="col">
                            <label for="checkOut" class="form-label">Check Out</label>
                            <input type="time" class="form-control" id="checkOut" name="check_out" required>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="present">Present</option>
                            <option value="absent">Absent</option>
                            <option value="leave">Leave</option>
                        </select>
                    </div>
                    
                    <input type="hidden" name="attendance_id" id="attendance_id">
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Attendance
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editButtons = document.querySelectorAll('.editAttendanceButton');
        const attendanceModal = new bootstrap.Modal('#attendanceModal');
        const attendanceForm = document.getElementById('attendanceForm');

        // Handle Edit Attendance
        editButtons.forEach(button => {
            button.addEventListener('click', async () => {
                const attendanceId = button.dataset.id;
                const employeeId = button.dataset.employeeId;

                try {
                    const response = await fetch(`/attendance/${employeeId}/edit/${attendanceId}`);
                    const data = await response.json();

                    // Populate form fields
                    document.getElementById('attendance_id').value = data.id;
                    document.getElementById('attendanceDate').value = data.date;
                    document.getElementById('checkIn').value = data.check_in;
                    document.getElementById('checkOut').value = data.check_out;
                    document.getElementById('status').value = data.status;

                    // Update form action
                    attendanceForm.action = `/attendance/${employeeId}/update/${attendanceId}`;
                    
                    attendanceModal.show();
                } catch (error) {
                    console.error('Error fetching attendance data:', error);
                }
            });
        });

        // Handle New Attendance
        // document.querySelector('[data-bs-target="#attendanceModal"]').addEventListener('click', () => {
        //     attendanceForm.reset();
        //     attendanceForm.action = "{{ route('attendance.store', $user->employee_id) }}";
        //     document.getElementById('attendance_id').value = '';
        // });
    });
</script>
@endsection