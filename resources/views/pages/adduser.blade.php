@extends('layouts.app')
@section('title', 'User Management')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    :root {
        --um-primary: #4a90e2;
        --um-primary-dark: #3b7cc4;
        --um-primary-light: #e8f0fe;
        --um-success: #0ecb81;
        --um-danger: #f6465d;
        --um-warning: #f0b90b;
        --um-gray-50: #f8f9fc;
        --um-gray-100: #f1f3f8;
        --um-gray-200: #e2e6ef;
        --um-gray-300: #c8cedb;
        --um-gray-600: #6c757d;
        --um-gray-800: #2d3748;
        --um-gray-900: #1a202c;
        --um-shadow-sm: 0 1px 3px rgba(0,0,0,0.06);
        --um-shadow: 0 4px 20px rgba(0,0,0,0.06);
        --um-shadow-lg: 0 8px 40px rgba(0,0,0,0.08);
        --um-radius: 12px;
        --um-radius-sm: 8px;
        --um-transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .um-container {
        padding: 0;
        max-width: 100%;
    }

    .um-header-card {
        background: linear-gradient(135deg, #4a90e2 0%, #357abd 50%, #2d6bb5 100%);
        border-radius: var(--um-radius);
        padding: 1.75rem 2rem;
        margin-bottom: 1.75rem;
        box-shadow: 0 8px 32px rgba(74, 144, 226, 0.25);
        position: relative;
        overflow: hidden;
    }

    .um-header-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 300px;
        height: 300px;
        background: rgba(255,255,255,0.04);
        border-radius: 50%;
    }

    .um-header-card::after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: -10%;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.03);
        border-radius: 50%;
    }

    .um-header-card h4 {
        color: #fff;
        font-weight: 700;
        font-size: 1.4rem;
        letter-spacing: -0.3px;
        position: relative;
        z-index: 1;
    }

    .um-header-card p {
        color: rgba(255,255,255,0.75);
        font-size: 0.88rem;
        position: relative;
        z-index: 1;
    }

    .um-stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .um-stat-card {
        background: #fff;
        border-radius: var(--um-radius-sm);
        padding: 1rem 1.25rem;
        box-shadow: var(--um-shadow-sm);
        border: 1px solid var(--um-gray-100);
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: var(--um-transition);
    }

    .um-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--um-shadow);
    }

    .um-stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }

    .um-stat-icon.blue { background: var(--um-primary-light); color: var(--um-primary); }
    .um-stat-icon.purple { background: #f0e6ff; color: #7c3aed; }
    .um-stat-icon.green { background: #e6faf0; color: var(--um-success); }
    .um-stat-icon.orange { background: #fef3e6; color: var(--um-warning); }

    .um-stat-info h6 {
        font-size: 0.75rem;
        color: var(--um-gray-600);
        margin-bottom: 2px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .um-stat-info span {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--um-gray-900);
    }

    .um-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        align-items: center;
        margin-bottom: 1.25rem;
        justify-content: space-between;
    }

    .um-search-wrapper {
        position: relative;
        flex: 1;
        min-width: 220px;
        max-width: 380px;
    }

    .um-search-wrapper .bi-search {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--um-gray-300);
        font-size: 0.9rem;
        pointer-events: none;
    }

    .um-search-wrapper input {
        width: 100%;
        padding: 0.6rem 1rem 0.6rem 2.4rem;
        border: 2px solid var(--um-gray-100);
        border-radius: var(--um-radius-sm);
        font-size: 0.88rem;
        background: var(--um-gray-50);
        transition: var(--um-transition);
        outline: none;
    }

    .um-search-wrapper input:focus {
        border-color: var(--um-primary);
        background: #fff;
        box-shadow: 0 0 0 4px rgba(74, 144, 226, 0.1);
    }

    .um-btn-primary {
        background: linear-gradient(135deg, #4a90e2, #357abd);
        border: none;
        color: #fff;
        padding: 0.6rem 1.5rem;
        border-radius: var(--um-radius-sm);
        font-weight: 600;
        font-size: 0.88rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: var(--um-transition);
        cursor: pointer;
        white-space: nowrap;
    }

    .um-btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(74, 144, 226, 0.3);
        color: #fff;
    }

    .um-btn-primary:active {
        transform: translateY(0);
    }

    .um-btn-outline {
        background: transparent;
        border: 2px solid var(--um-gray-200);
        color: var(--um-gray-600);
        padding: 0.6rem 1rem;
        border-radius: var(--um-radius-sm);
        font-size: 0.88rem;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        transition: var(--um-transition);
        cursor: pointer;
    }

    .um-btn-outline:hover {
        border-color: var(--um-primary);
        color: var(--um-primary);
        background: var(--um-primary-light);
    }

    .um-table-card {
        background: #fff;
        border-radius: var(--um-radius);
        box-shadow: var(--um-shadow);
        overflow: hidden;
        border: 1px solid var(--um-gray-100);
    }

    .um-table-wrapper {
        overflow-x: auto;
    }

    .um-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 1050px;
    }

    .um-table thead {
        background: var(--um-gray-50);
    }

    .um-table thead th {
        padding: 0.85rem 1.25rem;
        font-size: 0.78rem;
        font-weight: 700;
        color: var(--um-gray-600);
        text-transform: uppercase;
        letter-spacing: 0.6px;
        border-bottom: 2px solid var(--um-gray-200);
        white-space: nowrap;
        text-align: left;
    }

    .um-table tbody tr {
        transition: var(--um-transition);
        border-bottom: 1px solid var(--um-gray-100);
    }

    .um-table tbody tr:last-child {
        border-bottom: none;
    }

    .um-table tbody tr:hover {
        background: rgba(74, 144, 226, 0.03);
    }

    .um-table tbody td {
        padding: 0.9rem 1.25rem;
        font-size: 0.88rem;
        color: var(--um-gray-800);
        vertical-align: middle;
    }

    .um-user-cell {
        display: flex;
        align-items: center;
        gap: 0.85rem;
    }

    .um-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
        background: var(--um-gray-100);
    }

    .um-avatar-initials {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.8rem;
        color: #fff;
        flex-shrink: 0;
    }

    .um-user-name {
        font-weight: 600;
        color: var(--um-gray-900);
    }

    .um-user-email {
        font-size: 0.78rem;
        color: var(--um-gray-600);
        display: block;
    }

    .um-role-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.3rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.3px;
    }

    .um-role-badge.admin {
        background: #f0e6ff;
        color: #7c3aed;
    }

    .um-role-badge.employee {
        background: var(--um-primary-light);
        color: var(--um-primary);
    }

    .um-dept-tag {
        display: inline-block;
        padding: 0.25rem 0.65rem;
        background: var(--um-gray-50);
        border-radius: 6px;
        font-size: 0.8rem;
        color: var(--um-gray-600);
        font-weight: 500;
        border: 1px solid var(--um-gray-200);
    }

    .um-action-group {
        display: flex;
        gap: 0.4rem;
        flex-wrap: nowrap;
    }

    .um-action-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: var(--um-transition);
        font-size: 0.85rem;
        background: transparent;
    }

    .um-action-btn.edit {
        color: var(--um-primary);
        background: var(--um-primary-light);
    }

    .um-action-btn.edit:hover {
        background: var(--um-primary);
        color: #fff;
    }

    .um-action-btn.attendance {
        color: var(--um-success);
        background: #e6faf0;
    }

    .um-action-btn.attendance:hover {
        background: var(--um-success);
        color: #fff;
    }

    .um-action-btn.delete {
        color: var(--um-danger);
        background: #fee8eb;
    }

    .um-action-btn.delete:hover {
        background: var(--um-danger);
        color: #fff;
    }

    .um-empty {
        text-align: center;
        padding: 3rem 1rem;
        color: var(--um-gray-600);
    }

    .um-empty i {
        font-size: 3rem;
        color: var(--um-gray-200);
        margin-bottom: 1rem;
        display: block;
    }

    /* Modal Styles */
    .um-modal .modal-content {
        border: none;
        border-radius: var(--um-radius);
        box-shadow: var(--um-shadow-lg);
        overflow: hidden;
    }

    .um-modal .modal-header {
        background: linear-gradient(135deg, #4a90e2, #357abd);
        color: #fff;
        padding: 1.25rem 1.5rem;
        border: none;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .um-modal .modal-header h5 {
        font-weight: 700;
        font-size: 1.05rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin: 0;
    }

    .um-modal .btn-close {
        filter: brightness(0) invert(1);
        opacity: 0.7;
        transition: var(--um-transition);
    }

    .um-modal .btn-close:hover {
        opacity: 1;
    }

    .um-modal .modal-body {
        padding: 1.5rem;
        background: #fff;
    }

    .um-form-group {
        margin-bottom: 1rem;
    }

    .um-form-group label {
        display: block;
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--um-gray-800);
        margin-bottom: 0.35rem;
    }

    .um-input-group {
        position: relative;
    }

    .um-input-group .input-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--um-gray-300);
        font-size: 0.9rem;
        pointer-events: none;
    }

    .um-input-group .form-control,
    .um-input-group .form-select {
        padding: 0.55rem 1rem 0.55rem 2.2rem;
        border: 2px solid var(--um-gray-100);
        border-radius: var(--um-radius-sm);
        font-size: 0.88rem;
        transition: var(--um-transition);
        background: var(--um-gray-50);
    }

    .um-input-group .form-control:focus,
    .um-input-group .form-select:focus {
        border-color: var(--um-primary);
        background: #fff;
        box-shadow: 0 0 0 4px rgba(74, 144, 226, 0.1);
    }

    .um-input-group textarea.form-control {
        padding-left: 1rem;
    }

    .um-modal .modal-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--um-gray-100);
        background: var(--um-gray-50);
        display: flex;
        gap: 0.5rem;
        justify-content: flex-end;
    }

    .um-modal .btn-save {
        background: linear-gradient(135deg, #4a90e2, #357abd);
        border: none;
        color: #fff;
        padding: 0.55rem 1.75rem;
        border-radius: var(--um-radius-sm);
        font-weight: 600;
        font-size: 0.88rem;
        transition: var(--um-transition);
        cursor: pointer;
    }

    .um-modal .btn-save:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(74, 144, 226, 0.3);
    }

    .um-modal .btn-cancel {
        background: transparent;
        border: 2px solid var(--um-gray-200);
        color: var(--um-gray-600);
        padding: 0.55rem 1.5rem;
        border-radius: var(--um-radius-sm);
        font-weight: 500;
        font-size: 0.88rem;
        transition: var(--um-transition);
        cursor: pointer;
    }

    .um-modal .btn-cancel:hover {
        border-color: var(--um-gray-300);
        background: var(--um-gray-100);
    }

    .um-modal .btn-save:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .um-header-card {
            padding: 1.25rem;
        }

        .um-stats-row {
            grid-template-columns: repeat(2, 1fr);
        }

        .um-toolbar {
            flex-direction: column;
            align-items: stretch;
        }

        .um-search-wrapper {
            max-width: 100%;
        }

        .um-table thead {
            display: none;
        }

        .um-table tbody tr {
            display: block;
            padding: 1rem;
            border-bottom: 2px solid var(--um-gray-100);
        }

        .um-table tbody td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.4rem 0;
            border: none;
            min-height: 36px;
        }

        .um-table tbody td::before {
            content: attr(data-label);
            font-weight: 600;
            font-size: 0.75rem;
            color: var(--um-gray-600);
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        .um-table tbody td:last-child {
            padding-top: 0.6rem;
            border-top: 1px solid var(--um-gray-100);
            margin-top: 0.4rem;
        }

        .um-action-group {
            gap: 0.5rem;
        }

        .um-action-btn {
            width: 36px;
            height: 36px;
        }
    }

    @media (max-width: 480px) {
        .um-stats-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="um-container">

    {{-- Header --}}
    <div class="um-header-card d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h4><i class="bi bi-people-fill me-2"></i> Employee Management</h4>
            <p class="mb-0">Manage all registered employees and their account details</p>
        </div>
        <div class="d-flex gap-2">
            <button class="um-btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                <i class="bi bi-plus-lg"></i> Create User
            </button>
        </div>
    </div>

    {{-- Stats --}}
    <div class="um-stats-row">
        <div class="um-stat-card">
            <div class="um-stat-icon blue"><i class="bi bi-people"></i></div>
            <div class="um-stat-info">
                <h6>Total Users</h6>
                <span>{{ count($users) }}</span>
            </div>
        </div>
        <div class="um-stat-card">
            <div class="um-stat-icon purple"><i class="bi bi-shield-check"></i></div>
            <div class="um-stat-info">
                <h6>Admins</h6>
                <span>{{ $users->filter(fn($u) => $u->hasRole('Admin'))->count() }}</span>
            </div>
        </div>
        <div class="um-stat-card">
            <div class="um-stat-icon green"><i class="bi bi-person-badge"></i></div>
            <div class="um-stat-info">
                <h6>Employees</h6>
                <span>{{ $users->filter(fn($u) => $u->hasRole('Employee'))->count() }}</span>
            </div>
        </div>
        <div class="um-stat-card">
            <div class="um-stat-icon orange"><i class="bi bi-building"></i></div>
            <div class="um-stat-info">
                <h6>Departments</h6>
                <span>{{ $users->pluck('department')->unique()->filter()->count() }}</span>
            </div>
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="um-toolbar">
        <div class="um-search-wrapper">
            <i class="bi bi-search"></i>
            <input type="text" id="umSearch" placeholder="Search by name, email or department...">
        </div>
    </div>

    {{-- Table --}}
    <div class="um-table-card">
        <div class="um-table-wrapper">
            <table class="um-table" id="umTable">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Emp ID</th>
                        <th>Department</th>
                        <th>Gender</th>
                        <th>DOB</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                    @php
                        $initials = collect(explode(' ', $user->name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('');
                        $colors = ['#4a90e2','#7c3aed','#0ecb81','#f6465d','#f0b90b','#e67e22','#1abc9c','#e74c3c'];
                        $avatarColor = $colors[crc32($user->name) % count($colors)];
                        $isAdmin = $user->hasRole('Admin');
                    @endphp
                    <tr id="userRow{{ $user->id }}">
                        <td data-label="Employee">
                            <div class="um-user-cell">
                                @if($user->image)
                                    <img src="{{ asset('storage/'.$user->image) }}" class="um-avatar" alt="">
                                @else
                                    <div class="um-avatar-initials" style="background:{{ $avatarColor }};">{{ $initials }}</div>
                                @endif
                                <div>
                                    <div class="um-user-name">{{ $user->name }}</div>
                                    <span class="um-user-email">{{ $user->email }}</span>
                                </div>
                            </div>
                        </td>
                        <td data-label="Emp ID"><code>{{ $user->employee_id }}</code></td>
                        <td data-label="Department">
                            <span class="um-dept-tag">{{ $user->department ?? 'N/A' }}</span>
                        </td>
                        <td data-label="Gender">{{ $user->gender ?? 'N/A' }}</td>
                        <td data-label="DOB">{{ $user->date_of_birth ?? 'N/A' }}</td>
                        <td data-label="Phone">
                            @if($user->phone_number)
                                <a href="tel:{{ $user->phone_number }}" style="color:var(--um-primary);text-decoration:none;">{{ $user->phone_number }}</a>
                            @else
                                N/A
                            @endif
                        </td>
                        <td data-label="Role">
                            <span class="um-role-badge {{ $isAdmin ? 'admin' : 'employee' }}">
                                <i class="bi {{ $isAdmin ? 'bi-shield-fill-check' : 'bi-person-fill' }}"></i>
                                {{ $isAdmin ? 'Admin' : 'Employee' }}
                            </span>
                        </td>
                        <td data-label="Actions" style="text-align:right;">
                            <div class="um-action-group" style="justify-content:flex-end;">
                                <button class="um-action-btn edit" data-user='@json($user)' title="Edit User">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="um-action-btn attendance" data-id="{{ $user->employee_id }}" title="Edit Attendance">
                                    <i class="bi bi-calendar-check"></i>
                                </button>
                                <button class="um-action-btn delete" data-id="{{ $user->id }}" title="Delete User">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="um-empty">
                                <i class="bi bi-people"></i>
                                <h6>No users found</h6>
                                <p class="mb-0">Click "Create User" to add the first employee.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Create User Modal --}}
<div class="modal fade um-modal" id="createUserModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <form id="addUserForm" class="modal-content" enctype="multipart/form-data">
            @csrf
            <div class="modal-header">
                <h5><i class="bi bi-person-plus-fill"></i> Create User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6 um-form-group">
                        <label>Full Name</label>
                        <div class="um-input-group">
                            <i class="bi bi-person input-icon"></i>
                            <input type="text" id="name" class="form-control" name="name" required placeholder="John Doe">
                        </div>
                    </div>
                    <div class="col-md-6 um-form-group">
                        <label>Email</label>
                        <div class="um-input-group">
                            <i class="bi bi-envelope input-icon"></i>
                            <input type="email" id="email" class="form-control" name="email" required placeholder="john@company.com">
                        </div>
                    </div>
                    <div class="col-md-6 um-form-group">
                        <label>Password</label>
                        <div class="um-input-group">
                            <i class="bi bi-lock input-icon"></i>
                            <input type="password" id="password" class="form-control" name="password" required placeholder="Min 8 characters">
                        </div>
                    </div>
                    <div class="col-md-6 um-form-group">
                        <label>Department</label>
                        <div class="um-input-group">
                            <i class="bi bi-building input-icon"></i>
                            <select id="department" name="department" class="form-select" required>
                                <option value="" selected disabled>Select Department</option>
                                <option value="HR">HR</option>
                                <option value="Finance">Finance</option>
                                <option value="IT">IT</option>
                                <option value="Marketing">Marketing</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 um-form-group">
                        <label>Gender</label>
                        <div class="um-input-group">
                            <i class="bi bi-gender-ambiguous input-icon"></i>
                            <select id="gender" class="form-select" name="gender" required>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 um-form-group">
                        <label>Date of Birth</label>
                        <div class="um-input-group">
                            <i class="bi bi-calendar input-icon"></i>
                            <input type="text" id="dob" class="form-control" name="date_of_birth_ad" required placeholder="yyyy-mm-dd">
                        </div>
                    </div>
                    <div class="col-md-6 um-form-group">
                        <label>Phone Number</label>
                        <div class="um-input-group">
                            <i class="bi bi-telephone input-icon"></i>
                            <input type="text" id="phone" class="form-control" name="phone_number" required placeholder="10-digit number" pattern="[0-9]{10}">
                        </div>
                    </div>
                    <div class="col-md-6 um-form-group">
                        <label>Date of Joining</label>
                        <div class="um-input-group">
                            <i class="bi bi-calendar-check input-icon"></i>
                            <input type="text" id="joining" class="form-control" name="date_of_joining" required placeholder="yyyy-mm-dd">
                        </div>
                    </div>
                    <div class="col-md-6 um-form-group">
                        <label>Profile Image</label>
                        <div class="um-input-group">
                            <i class="bi bi-image input-icon"></i>
                            <input type="file" id="image" class="form-control" name="image" accept="image/*">
                        </div>
                    </div>
                    <div class="col-12 um-form-group">
                        <label>Address</label>
                        <div class="um-input-group">
                            <textarea id="address" class="form-control" name="address" required placeholder="Full address" rows="2"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn-save" id="createBtn"><i class="bi bi-check-lg me-1"></i> Save</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit User Modal --}}
<div class="modal fade um-modal" id="editUserModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <form id="editUserForm" class="modal-content" enctype="multipart/form-data">
            <input type="hidden" id="edit_user_id">
            <div class="modal-header">
                <h5><i class="bi bi-pencil-square"></i> Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6 um-form-group">
                        <label>Full Name</label>
                        <div class="um-input-group">
                            <i class="bi bi-person input-icon"></i>
                            <input type="text" id="edit_name" class="form-control" name="name" required>
                        </div>
                    </div>
                    <div class="col-md-6 um-form-group">
                        <label>Email</label>
                        <div class="um-input-group">
                            <i class="bi bi-envelope input-icon"></i>
                            <input type="email" id="edit_email" class="form-control" name="email" required>
                        </div>
                    </div>
                    <div class="col-md-6 um-form-group">
                        <label>Department</label>
                        <div class="um-input-group">
                            <i class="bi bi-building input-icon"></i>
                            <select id="edit_department" name="department" class="form-select" required>
                                <option value="" selected disabled>Select Department</option>
                                <option value="HR">HR</option>
                                <option value="Finance">Finance</option>
                                <option value="IT">IT</option>
                                <option value="Marketing">Marketing</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 um-form-group">
                        <label>Gender</label>
                        <div class="um-input-group">
                            <i class="bi bi-gender-ambiguous input-icon"></i>
                            <select id="edit_gender" class="form-select" name="gender" required>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 um-form-group">
                        <label>Date of Birth</label>
                        <div class="um-input-group">
                            <i class="bi bi-calendar input-icon"></i>
                            <input type="text" id="edit_dob" class="form-control" name="date_of_birth_ad" required placeholder="yyyy-mm-dd">
                        </div>
                    </div>
                    <div class="col-md-6 um-form-group">
                        <label>Phone Number</label>
                        <div class="um-input-group">
                            <i class="bi bi-telephone input-icon"></i>
                            <input type="text" id="edit_phone" class="form-control" name="phone_number" required pattern="[0-9]{10}">
                        </div>
                    </div>
                    <div class="col-md-6 um-form-group">
                        <label>Date of Joining</label>
                        <div class="um-input-group">
                            <i class="bi bi-calendar-check input-icon"></i>
                            <input type="text" id="edit_joining" class="form-control" name="date_of_joining" required placeholder="yyyy-mm-dd">
                        </div>
                    </div>
                    <div class="col-md-6 um-form-group">
                        <label>Profile Image</label>
                        <div class="um-input-group">
                            <i class="bi bi-image input-icon"></i>
                            <input type="file" id="edit_image" class="form-control" name="image" accept="image/*">
                        </div>
                    </div>
                    <div class="col-12 um-form-group">
                        <label>Address</label>
                        <div class="um-input-group">
                            <textarea id="edit_address" class="form-control" name="address" required rows="2"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn-save" id="updateBtn"><i class="bi bi-check-lg me-1"></i> Update</button>
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

    // Initialize Datepickers
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

    // Search functionality
    $('#umSearch').on('keyup', function () {
        let val = this.value.toLowerCase();
        $('#umTable tbody tr').each(function () {
            let text = $(this).text().toLowerCase();
            $(this).toggle(text.includes(val));
        });
    });

    // Create User
    $('#addUserForm').submit(function (e) {
        e.preventDefault();
        let btn = $('#createBtn');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');
        let formData = new FormData(this);

        $.ajax({
            url: '/users',
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            data: formData,
            processData: false,
            contentType: false,
            success: function () {
                $('#createUserModal').modal('hide');
                Swal.fire({ icon: 'success', title: 'Success!', text: 'User created successfully.', timer: 2000, showConfirmButton: false })
                    .then(() => location.reload());
            },
            error: function (xhr) {
                let msg = 'Failed to create user.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                }
                Swal.fire({ icon: 'error', title: 'Error!', text: msg });
                btn.prop('disabled', false).html('<i class="bi bi-check-lg me-1"></i> Save');
            }
        });
    });

    // Edit User - populate modal
    $(document).on('click', '.editBtn, .um-action-btn.edit', function () {
        let user = $(this).data('user');
        if (!user) return;
        $('#edit_user_id').val(user.id);
        $('#edit_name').val(user.name);
        $('#edit_email').val(user.email);
        $('#edit_department').val(user.department);
        $('#edit_gender').val(user.gender);
        $('#edit_dob').val(user.date_of_birth_ad || user.date_of_birth);
        $('#edit_phone').val(user.phone_number);
        $('#edit_joining').val(user.date_of_joining);
        $('#edit_address').val(user.address);
        $('#editUserModal').modal('show');
    });

    // Update User
    $('#editUserForm').submit(function (e) {
        e.preventDefault();
        let id = $('#edit_user_id').val();
        let btn = $('#updateBtn');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Updating...');
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
                Swal.fire({ icon: 'success', title: 'Updated!', text: 'User updated successfully.', timer: 2000, showConfirmButton: false })
                    .then(() => location.reload());
            },
            error: function (xhr) {
                let msg = 'Failed to update user.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                }
                Swal.fire({ icon: 'error', title: 'Error!', text: msg });
                btn.prop('disabled', false).html('<i class="bi bi-check-lg me-1"></i> Update');
            }
        });
    });

    // Delete User
    $(document).on('click', '.deleteBtn, .um-action-btn.delete', function () {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Delete User?',
            text: "This action cannot be undone. All attendance and leave records will also be removed.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f6465d',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/users/' + id,
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    data: { _method: 'DELETE' },
                    success: function () {
                        $('#userRow' + id).fadeOut(300, function () { $(this).remove(); });
                        Swal.fire({ icon: 'success', title: 'Deleted!', text: 'User has been deleted.', timer: 2000, showConfirmButton: false });
                    },
                    error: function () {
                        Swal.fire({ icon: 'error', title: 'Error!', text: 'Failed to delete user.' });
                    }
                });
            }
        });
    });

    // Edit Attendance redirect
    $(document).on('click', '.editAttendenceButton, .um-action-btn.attendance', function () {
        let empId = $(this).data('id');
        if (empId) window.location.href = '/attendance/' + empId;
    });

    // Reset form on modal close
    $('#createUserModal').on('hidden.bs.modal', function () {
        $('#addUserForm')[0].reset();
        $('#createBtn').prop('disabled', false).html('<i class="bi bi-check-lg me-1"></i> Save');
    });

    $('#editUserModal').on('hidden.bs.modal', function () {
        $('#updateBtn').prop('disabled', false).html('<i class="bi bi-check-lg me-1"></i> Update');
    });
});
</script>

@endsection
