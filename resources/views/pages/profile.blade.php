@extends('layouts.app')
@section('title', 'User Profile')

@section('content')
<div class="container py-5">
    <!-- Profile Header -->
    <div class="row align-items-center mb-5">
        <div class="col-md-3 text-center">
            <div class="profile-image-container mb-3">
                @if($user->image)
                    <img src="{{ asset('storage/'.$user->image) }}" class="img-fluid rounded-circle shadow" alt="Profile Image" style="width: 200px; height: 200px; object-fit: cover;">
                @else
                    <div class="d-flex align-items-center justify-content-center bg-light rounded-circle text-muted" 
                         style="width: 200px; height: 200px;">
                        <i class="fas fa-user fa-4x"></i>
                    </div>
                @endif
            </div>
            <h3 class="mt-3">{{ $user->name }}</h3>
            <p class="text-muted">{{ $user->department }}</p>
        </div>

        <div class="col-md-9">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">Employee Information</h4>
                        {{-- <div>
                            <a href="" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                            <button class="btn btn-primary editBtn" data-user='@json($user)'>
                                <i class="fas fa-edit"></i> Edit Profile
                            </button>
                        </div> --}}
                    </div>

                    <div class="row">
                        <!-- Personal Info -->
                        <div class="col-md-6">
                            <div class="info-item mb-4">
                                <h6 class="text-muted mb-3"><i class="fas fa-id-card me-2"></i>Personal Details</h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="fas fa-venus-mars me-2"></i>
                                        Gender: {{ $user->gender ?? 'N/A' }}
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-birthday-cake me-2"></i>
                                        Date of Birth: {{ $user->date_of_birth_ad ?? 'N/A' }}
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-phone me-2"></i>
                                        Phone: {{ $user->phone_number ?? 'N/A' }}
                                    </li>
                                    <li>
                                        <i class="fas fa-map-marker-alt me-2"></i>
                                        Address: {{ $user->address ?? 'N/A' }}
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Employment Info -->
                        <div class="col-md-6">
                            <div class="info-item mb-4">
                                <h6 class="text-muted mb-3"><i class="fas fa-briefcase me-2"></i>Employment Details</h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="fas fa-id-badge me-2"></i>
                                        Employee ID: {{ $user->employee_id }}
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-envelope me-2"></i>
                                        Email: {{ $user->email }}
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-building me-2"></i>
                                        Department: {{ $user->department ?? 'N/A' }}
                                    </li>
                                    <li>
                                        <i class="fas fa-calendar-alt me-2"></i>
                                        Date of Joining: {{ $user->date_of_joining }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Sections -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-history me-2"></i>Employment Timeline</h5>
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-point"></div>
                            <div class="timeline-content">
                                <h6>Joined Company</h6>
                                <small class="text-muted">{{ $user->date_of_joining }}</small>
                                <p>Started as {{ $user->department }} department member</p>
                            </div>
                        </div>
                        <!-- Add more timeline items as needed -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .profile-image-container {
        position: relative;
        transition: transform 0.3s ease;
    }

    .profile-image-container:hover {
        transform: scale(1.05);
    }

    .info-item {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1.5rem;
        transition: all 0.3s ease;
    }

    .info-item:hover {
        background: #fff;
        box-shadow: 0 2px 15px rgba(0,0,0,0.1);
    }

    .timeline {
        border-left: 2px solid #dee2e6;
        margin-left: 20px;
        padding-left: 30px;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .timeline-point {
        position: absolute;
        left: -32px;
        top: 5px;
        width: 12px;
        height: 12px;
        background-color: #0d6efd;
        border-radius: 50%;
    }

    .card {
        border: none;
        border-radius: 15px;
    }
</style>
@endsection