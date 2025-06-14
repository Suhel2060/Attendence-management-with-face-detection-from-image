<div class="bg-dark text-white border-end" id="sidebar-wrapper" style="min-height: 100vh;width:20%">
    <!-- Profile Section -->
    <div class="profile-section text-center p-4 border-bottom">
        <div class="d-flex flex-column align-items-center">
            @if($usr->image)
                <img src="{{ asset('storage/'.$usr->image) }}" class="rounded-circle mb-3" 
                     style="width: 80px; height: 80px; object-fit: cover;">
            @else
                <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center mb-3" 
                     style="width: 80px; height: 80px;">
                    <i class="bi bi-person-fill text-light" style="font-size: 2rem;"></i>
                </div>
            @endif
            <h5 class="mb-1">{{ $usr->name }}</h5>
            <small class="text-muted">{{ $usr->email }}</small>
        </div>
    </div>

    <div class="list-group list-group-flush">
        <a href="/dashboard" class="list-group-item list-group-item-action bg-dark text-white hover-lift p-3 d-flex align-items-center">
            <i class="bi bi-speedometer2 me-3" style="font-size: 1.2rem;"></i>
            Dashboard
        </a>
        
        @if ($usr->hasRole('Admin'))
        <div class="admin-section border-top border-bottom border-dark">
            <a href="/user" class="list-group-item list-group-item-action bg-dark text-white hover-lift p-3 d-flex align-items-center">
                <i class="bi bi-people-fill me-3" style="font-size: 1.2rem;"></i>
                User Management
            </a>
            <a href="/all-attendence" class="list-group-item list-group-item-action bg-dark text-white hover-lift p-3 d-flex align-items-center">
                <i class="bi bi-list-check me-3" style="font-size: 1.2rem;"></i>
                All Attendence
            </a>
            <a href="/hr/leaves" class="list-group-item list-group-item-action bg-dark text-white hover-lift p-3 d-flex align-items-center">
                <i class="bi bi-list-check me-3" style="font-size: 1.2rem;"></i>
                Leave Management
            </a>
        </div>
        @endif

        <div class="user-section border-top border-dark">
            <a href="/attendence" class="list-group-item list-group-item-action bg-dark text-white hover-lift p-3 d-flex align-items-center">
                <i class="bi bi-clock-history me-3" style="font-size: 1.2rem;"></i>
                My Attendence
            </a>
            <a href="/profile" class="list-group-item list-group-item-action bg-dark text-white hover-lift p-3 d-flex align-items-center">
                <i class="bi bi-person-circle me-3" style="font-size: 1.2rem;"></i>
                My Profile
            </a>
            <a href="/leaves" class="list-group-item list-group-item-action bg-dark text-white hover-lift p-3 d-flex align-items-center">
                <i class="bi bi-calendar2-check me-3" style="font-size: 1.2rem;"></i>
                My Leave Request
            </a>
            <a href="/leaves/create" class="list-group-item list-group-item-action bg-dark text-white hover-lift p-3 d-flex align-items-center">
                <i class="bi bi-plus-circle me-3" style="font-size: 1.2rem;"></i>
                Leave Request
            </a>
        </div>
    </div>
</div>

<style>
    /* Enhanced Styling */
    #sidebar-wrapper {
        background: linear-gradient(180deg, #2c3034 0%, #1a1d20 100%);
        box-shadow: 5px 0 15px rgba(0, 0, 0, 0.2);
    }

    .list-group-item {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        position: relative;
    }

    .list-group-item:hover {
        background-color: #343a40;
        transform: translateX(10px);
        box-shadow: 3px 0 15px rgba(0, 0, 0, 0.2);
    }

    .list-group-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 3px;
        background: #0d6efd;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .list-group-item:hover::before {
        opacity: 1;
    }

    .profile-section {
        background: rgba(255, 255, 255, 0.05);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
    }

    .admin-section {
        border-color: rgba(255, 255, 255, 0.1) !important;
    }

    .bi {
        width: 25px;
        text-align: center;
    }

    @media (max-width: 768px) {
        #sidebar-wrapper {
            width: 250px !important;
            position: fixed;
            z-index: 1000;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }

        #sidebar-wrapper.active {
            transform: translateX(0);
        }

        .profile-section {
            padding: 1.5rem !important;
        }

        .list-group-item {
            font-size: 0.9rem;
            padding: 0.75rem 1rem;
        }
    }
</style>