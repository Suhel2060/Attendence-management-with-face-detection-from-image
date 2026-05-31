<div class="d-flex flex-column bg-dark text-white" id="sidebar-wrapper" style="min-height: 100vh; width: 260px; flex-shrink: 0;">
    <!-- Brand Header -->
    <div class="p-3 text-center" style="background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);">
        <i class="bi bi-shield-check" style="font-size: 1.8rem;"></i>
        <h6 class="mt-1 mb-0 fw-bold" style="letter-spacing: 1px;">ATTENDANCE</h6>
        <small style="opacity: 0.8;">Face Recognition System</small>
    </div>

    <!-- Profile Section -->
    <div class="text-center py-3 px-3 border-bottom" style="border-color: rgba(255,255,255,0.08) !important;">
        <div class="d-flex align-items-center gap-3">
            @if($usr->image)
                <img src="{{ asset('storage/'.$usr->image) }}" class="rounded-circle flex-shrink-0"
                     style="width: 44px; height: 44px; object-fit: cover; border: 2px solid rgba(255,255,255,0.15);">
            @else
                <div class="bg-secondary rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center"
                     style="width: 44px; height: 44px; border: 2px solid rgba(255,255,255,0.15);">
                    <i class="bi bi-person-fill text-light" style="font-size: 1.2rem;"></i>
                </div>
            @endif
            <div class="text-start overflow-hidden">
                <div class="fw-semibold text-truncate" style="font-size: 0.9rem;">{{ $usr->name }}</div>
                <small class="text-muted text-truncate d-block" style="font-size: 0.75rem;">{{ $usr->email }}</small>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <div class="list-group list-group-flush flex-grow-1 overflow-auto">
        <a href="/dashboard" class="list-group-item list-group-item-action bg-dark text-white d-flex align-items-center gap-3 py-3 nav-item">
            <i class="bi bi-speedometer2 nav-icon"></i>
            <span>Dashboard</span>
        </a>

        @if ($usr->hasRole('Admin'))
        <div class="section-divider">
            <small class="section-label">ADMIN</small>
        </div>
        <a href="/user" class="list-group-item list-group-item-action bg-dark text-white d-flex align-items-center gap-3 py-3 nav-item">
            <i class="bi bi-people-fill nav-icon"></i>
            <span>User Management</span>
        </a>
        <a href="/all-attendence" class="list-group-item list-group-item-action bg-dark text-white d-flex align-items-center gap-3 py-3 nav-item">
            <i class="bi bi-list-check nav-icon"></i>
            <span>All Attendance</span>
        </a>
        <a href="/enrollment" class="list-group-item list-group-item-action bg-dark text-white d-flex align-items-center gap-3 py-3 nav-item">
            <i class="bi bi-camera-fill nav-icon"></i>
            <span>Face Enrollment</span>
        </a>
        <a href="/hr/leaves" class="list-group-item list-group-item-action bg-dark text-white d-flex align-items-center gap-3 py-3 nav-item">
            <i class="bi bi-calendar-check nav-icon"></i>
            <span>Leave Management</span>
        </a>
        <a href="/hr/time-corrections" class="list-group-item list-group-item-action bg-dark text-white d-flex align-items-center gap-3 py-3 nav-item">
            <i class="bi bi-clock-history nav-icon"></i>
            <span>Time Corrections</span>
        </a>
        @endif

        <div class="section-divider">
            <small class="section-label">EMPLOYEE</small>
        </div>
        <a href="/authattendence" class="list-group-item list-group-item-action bg-dark text-white d-flex align-items-center gap-3 py-3 nav-item">
            <i class="bi bi-clock-history nav-icon"></i>
            <span>My Attendance</span>
        </a>
        <a href="/profile" class="list-group-item list-group-item-action bg-dark text-white d-flex align-items-center gap-3 py-3 nav-item">
            <i class="bi bi-person-circle nav-icon"></i>
            <span>My Profile</span>
        </a>
        <a href="/leaves" class="list-group-item list-group-item-action bg-dark text-white d-flex align-items-center gap-3 py-3 nav-item">
            <i class="bi bi-inbox nav-icon"></i>
            <span>My Leave Requests</span>
        </a>
        <a href="/leaves/create" class="list-group-item list-group-item-action bg-dark text-white d-flex align-items-center gap-3 py-3 nav-item">
            <i class="bi bi-send-plus nav-icon"></i>
            <span>New Leave Request</span>
        </a>
        <a href="/time-corrections" class="list-group-item list-group-item-action bg-dark text-white d-flex align-items-center gap-3 py-3 nav-item">
            <i class="bi bi-arrow-counterclockwise nav-icon"></i>
            <span>Time Correction</span>
        </a>
    </div>

    <!-- Logout -->
    <div class="border-top p-3" style="border-color: rgba(255,255,255,0.08) !important;">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-danger btn-sm w-100 d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
    </div>
</div>

<style>
    #sidebar-wrapper {
        background: linear-gradient(180deg, #1e2124 0%, #151719 100%);
        box-shadow: 2px 0 20px rgba(0, 0, 0, 0.3);
        border-right: 1px solid rgba(255, 255, 255, 0.06);
        flex-shrink: 0;
        overflow-x: hidden;
    }

    .nav-item {
        transition: all 0.25s ease;
        border: none;
        border-left: 3px solid transparent;
        font-size: 0.88rem;
    }

    .nav-item:hover {
        background: rgba(13, 110, 253, 0.1) !important;
        border-left-color: #0d6efd;
    }

    .nav-item:hover .nav-icon {
        color: #0d6efd;
    }

    .nav-icon {
        width: 22px;
        text-align: center;
        font-size: 1.1rem;
        transition: color 0.25s ease;
        color: rgba(255, 255, 255, 0.6);
    }

    .section-divider {
        padding: 0.75rem 1rem 0.25rem;
    }

    .section-label {
        color: rgba(255, 255, 255, 0.3);
        font-size: 0.65rem;
        font-weight: 700;
        letter-spacing: 1.5px;
    }

    @media (max-width: 768px) {
        #sidebar-wrapper {
            width: 260px !important;
            position: fixed;
            z-index: 1000;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }

        #sidebar-wrapper.active {
            transform: translateX(0);
        }
    }
</style>
