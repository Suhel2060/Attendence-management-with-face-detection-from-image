@extends('layouts.app')
@section('title', 'User Management')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- <style>
    /* Modern Styling */
    .user-management-container {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.05);
        padding: 2rem;
        margin: 2rem auto;
    }

    .table-wrapper {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #f0f2f5;
        box-shadow: 0 4px 24px rgba(0,0,0,0.04);
    }

    .table thead {
        background: linear-gradient(135deg, #f8f9fa 0%, #f1f3f5 100%);
    }

    .table thead th {
        border-bottom: 2px solid #e9ecef;
        font-weight: 600;
        color: #2d3436;
        padding: 1.2rem 1.5rem;
    }

    .table tbody td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
        color: #4a4a4a;
    }

    .table tbody tr {
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .table tbody tr:hover {
        background: rgba(99, 102, 241, 0.03);
        transform: translateX(4px);
    }

    .action-buttons .btn {
        margin: 0 4px;
        border-radius: 8px;
        font-size: 0.875rem;
        padding: 0.5rem 1rem;
        transition: all 0.2s ease;
    }

    /* Modal Styling */
    .modal-header {
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        color: white;
        border-radius: 12px 12px 0 0;
        padding: 1.5rem;
    }

    .modal-title {
        font-weight: 600;
        letter-spacing: -0.5px;
    }

    .modal-content {
        border: none;
        border-radius: 12px;
        box-shadow: 0 12px 40px rgba(0,0,0,0.15);
    }

    .form-control {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 0.75rem 1.25rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
    }

    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 0.5rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        border: none;
        padding: 0.75rem 1.75rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);
    }

    /* Datepicker Styling */
    .ui-datepicker {
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.1) !important;
        border: 1px solid #f1f3f5;
    }

    .ui-datepicker-header {
        background: #6366f1 !important;
        color: white;
        border-radius: 12px 12px 0 0;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .user-management-container {
            padding: 1.5rem;
            margin: 1rem;
        }
        
        .table thead {
            display: none;
        }
        
        .table tbody td {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem;
        }
        
        .table tbody td::before {
            content: attr(data-label);
            font-weight: 600;
            color: #6366f1;
            margin-right: 1rem;
        }
    }
</style> --}}

<style>
    /* Optimized Table CSS */
    .user-management-container {
        padding: 1.5rem;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        width: 71rem;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    .table {
        min-width: 1000px;
        margin-bottom: 0;
    }

    .table thead th {
        background: #f8f9fa;
        color: #495057;
        font-weight: 600;
        padding: 0.75rem 1rem;
        border-bottom: 2px solid #e9ecef;
    }

    .table tbody td {
        padding: 0.5rem 1rem;
        vertical-align: middle;
        color: #4a4a4a;
        font-size: 0.9rem;
    }

    .action-buttons {
        white-space: nowrap;
    }

    .btn-sm {
        padding: 0.25rem 0.75rem;
        font-size: 0.825rem;
        margin: 2px;
    }

    .btn-warning {
        background-color: #ffc107;
        border-color: #ffc107;
        color: #000;
    }

    .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
    }

    @media (max-width: 768px) {
        .user-management-container {
            padding: 1rem;
            margin: 0.5rem;
        }
        
        .table thead {
            display: none;
        }
        
        .table tbody td {
            display: block;
            text-align: right;
            padding: 0.5rem;
        }
        
        .table tbody td::before {
            content: attr(data-label);
            float: left;
            font-weight: 500;
            color: #6c757d;
        }

        .action-buttons {
            text-align: right;
        }
    }
</style>


<div class="container user-management-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0" style="font-weight: 700; color: #1a1a1a;">Employee Management</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
            Create User
        </button>
    </div>

    <div class="table-wrapper">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Emp ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th>Gender</th>
                    <th>DOB</th>
                    <th>Phone</th>
                    <th>DOJ</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                <tr id="userRow{{ $user->id }}">
                    <td data-label="Emp ID">{{ $user->employee_id }}</td>
                    <td data-label="Name">{{ $user->name }}</td>
                    <td data-label="Email">{{ $user->email }}</td>
                    <td data-label="Department">{{ $user->department ?? 'N/A' }}</td>
                    <td data-label="Gender">{{ $user->gender ?? 'N/A' }}</td>
                    <td data-label="DOB">{{ $user->date_of_birth ?? 'N/A' }}</td>
                    <td data-label="Phone">{{ $user->phone_number ?? 'N/A' }}</td>
                    <td data-label="DOJ">{{ $user->date_of_joining ?? 'N/A' }}</td>
                    <td data-label="Actions" class="action-buttons">
                        <button class="btn btn-sm btn-warning editBtn" data-user='@json($user)'>Edit</button>
                        <button class="btn btn-sm btn-warning editAttendenceButton" data-id="{{ $user->employee_id }}">Edit Attendence</button>
                        <button class="btn btn-sm btn-danger deleteBtn" data-id="{{ $user->id }}">Delete</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<!-- Create User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="addUserForm" class="modal-content" enctype="multipart/form-data">
            @csrf
            <div class="modal-header">
                <h5>Create User</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row">
                <div class="col-md-6 mb-3">
                    <label>Name</label>
                    <input type="text" id="name" class="form-control" name="name" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Email</label>
                    <input type="email" id="email" class="form-control" name="email" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Password</label>
                    <input type="password" id="password" class="form-control" name="password" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="department">Department</label>
                    <select id="department" name="department" class="form-control" required>
                        <option value="" selected disabled>Select Department</option>
                        <option value="HR">HR</option>
                        <option value="Finance">Finance</option>
                        <option value="IT">IT</option>
                        <option value="Marketing">Marketing</option>
                        <!-- Add more departments as needed -->
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Gender</label>
                    <select id="gender" class="form-control" name="gender" required>
                        <option>Male</option>
                        <option>Female</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Date of Birth</label>
                    <input type="text" id="dob" class="form-control" name="date_of_birth_ad" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Phone</label>
                    <input type="text" id="phone" class="form-control" name="phone_number" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Date of Joining</label>
                    <input type="text" id="joining" class="form-control" name="date_of_joining" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Image</label>
                    <input type="file" id="image" class="form-control" name="image" accept="image/*">
                </div>
                <div class="col-md-12 mb-3">
                    <label>Address</label>
                    <textarea id="address" class="form-control" name="address" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Save</button>
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="editUserForm" class="modal-content" enctype="multipart/form-data">
            <input type="hidden" id="edit_user_id">
            <div class="modal-header">
                <h5>Edit User</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row">
                <div class="col-md-6 mb-3">
                    <label>Name</label>
                    <input type="text" id="edit_name" class="form-control" name="name" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Email</label>
                    <input type="email" id="edit_email" class="form-control" name="email" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="edit_department">Department</label>
                    <select id="edit_department" name="department" class="form-control" required>
                        <option value="" selected disabled>Select Department</option>
                        <option value="HR">HR</option>
                        <option value="Finance">Finance</option>
                        <option value="IT">IT</option>
                        <option value="Marketing">Marketing</option>
                        <!-- Add more departments as needed -->
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label>Gender</label>
                    <select id="edit_gender" class="form-control" name="gender" required>
                        <option>Male</option>
                        <option>Female</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Date of Birth</label>
                    <input type="text" id="edit_dob" class="form-control" name="date_of_birth_ad" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Phone</label>
                    <input type="text" id="edit_phone" class="form-control" name="phone_number" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Date of Joining</label>
                    <input type="text" id="edit_joining" class="form-control" name="date_of_joining" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Image</label>
                    <input type="file" id="edit_image" class="form-control" name="image" accept="image/*">
                </div>
                <div class="col-md-12 mb-3">
                    <label>Address</label>
                    <textarea id="edit_address" class="form-control" name="address" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-warning">Update</button>
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
  $(document).ready(function () {
    let csrfToken = $('meta[name="csrf-token"]').attr('content');
    let today = new Date();
    let minDob = new Date();
    minDob.setFullYear(today.getFullYear() - 18);

    // Initialize Datepickers with restrictions
    $('#dob, #edit_dob').datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        yearRange: "1950:" + (today.getFullYear() - 18),
        maxDate: minDob
    });

    $('#joining, #edit_joining').datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        yearRange: "1950:" + today.getFullYear(),
        maxDate: today
    });

    // Create User Form Submit
    $('#addUserForm').submit(function (e) {
        e.preventDefault();
        let formData = new FormData(this);
        
        $.ajax({
            url: '/users',
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $('#createUserModal').modal('hide');
                Swal.fire('Success!', 'User created successfully.', 'success').then(() => location.reload());
            },
            error: function (xhr, status, error) {
                console.error("Error details: ", xhr.responseText);
                Swal.fire('Error!', 'Failed to create user.', 'error');
            }
        });
    });

    // Edit User
    $(document).on('click', '.editBtn', function () {
        let user = $(this).data('user');
        $('#edit_user_id').val(user.id);
        $('#edit_name').val(user.name);
        $('#edit_email').val(user.email);
        $('#edit_department').val(user.department);
        $('#edit_gender').val(user.gender);
        $('#edit_dob').val(user.date_of_birth_ad);
        $('#edit_phone').val(user.phone_number);
        $('#edit_joining').val(user.date_of_joining);
        $('#edit_address').val(user.address);
        $('#editUserModal').modal('show');
    });

    // Update User Form Submit
    $('#editUserForm').submit(function (e) {
        e.preventDefault();
        let id = $('#edit_user_id').val();
        let formData = new FormData(this);
        
        $.ajax({
            url: '/users/' + id,
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            data: formData,
            processData: false,
            contentType: false,
            success: function () {
                $('#editUserModal').modal('hide');
                Swal.fire('Updated!', 'User updated successfully.', 'success').then(() => location.reload());
            },
            error: function () {
                Swal.fire('Error!', 'Failed to update user.', 'error');
            }
        });
    });

    // Delete User
    $(document).on('click', '.deleteBtn', function () {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/users/' + id,
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    data: { _method: 'DELETE' },
                    success: function () {
                        $('#userRow' + id).remove();
                        Swal.fire('Deleted!', 'User has been deleted.', 'success');
                    },
                    error: function () {
                        Swal.fire('Error!', 'Failed to delete user.', 'error');
                    }
                });
            }
        });
    });


});


document.addEventListener('DOMContentLoaded', function () {
        const buttons = document.querySelectorAll('.editAttendenceButton');

        buttons.forEach(button => {
            button.addEventListener('click', function () {
                const employeeId = this.dataset.id;
                if (employeeId) {
                    window.location.href = `/attendance/${employeeId}`;
                }
            });
        });
    });
</script>

@endsection
