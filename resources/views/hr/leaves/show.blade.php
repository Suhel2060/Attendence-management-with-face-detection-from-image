@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">Review Leave Request</h3>
                        <span class="badge bg-light text-dark fs-6">
                            Status: <span class="text-warning">Pending Approval</span>
                        </span>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="row mb-5">
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <h5 class="card-title border-bottom pb-2 mb-3">
                                        <i class="bi bi-person-circle me-2"></i>Employee Details
                                    </h5>
                                    <div class="mb-3">
                                        <strong>Name:</strong> {{ $leave->employee->name }}
                                    </div>
                                    <div class="mb-3">
                                        <strong>Employee ID:</strong> {{ $leave->employee->employee_id }}
                                    </div>
                                    <div class="mb-3">
                                        <strong>Department:</strong> {{ $leave->employee->department ?? 'N/A' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <h5 class="card-title border-bottom pb-2 mb-3">
                                        <i class="bi bi-calendar-event me-2"></i>Leave Details
                                    </h5>
                                    <div class="mb-3">
                                        <strong>Type:</strong> 
                                        <span class="badge bg-info">{{ $leave->leaveType->name }}</span>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Dates:</strong> 
                                        {{ \Carbon\Carbon::parse($leave->start_date)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}
                                    </div>
                                    <div class="mb-3">
                                        <strong>Duration:</strong> 
                                        <span class="badge bg-primary">{{ $leave->total_days }} days</span>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Requested On:</strong> 
                                        {{ $leave->created_at->format('M d, Y h:i A') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mb-5">
                        <div class="card-body">
                            <h5 class="card-title border-bottom pb-2 mb-3">
                                <i class="bi bi-chat-text me-2"></i>Reason for Leave
                            </h5>
                            <p>{{ $leave->reason }}</p>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title border-bottom pb-2 mb-3">
                                <i class="bi bi-clipboard-check me-2"></i>Approval Action
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <form method="POST" action="{{ route('hr.leaves.approve', $leave) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-lg w-100 py-3">
                                            <i class="bi bi-check-circle me-2"></i>Approve Leave
                                        </button>
                                    </form>
                                </div>
                                <div class="col-md-6">
                                    <form method="POST" action="{{ route('hr.leaves.reject', $leave) }}">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="rejection_reason" class="form-label fw-bold">
                                                Rejection Reason
                                            </label>
                                            <textarea class="form-control" id="rejection_reason" 
                                                      name="rejection_reason" rows="2" required
                                                      placeholder="Specify reason for rejection"></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-danger btn-lg w-100 py-3">
                                            <i class="bi bi-x-circle me-2"></i>Reject Leave
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection