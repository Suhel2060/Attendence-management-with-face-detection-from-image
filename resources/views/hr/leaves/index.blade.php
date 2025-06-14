@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0"><i class="bi bi-clock-history me-2"></i>Pending Leave Approvals</h3>
                <span class="badge bg-light text-dark fs-6">
                    {{ $pendingLeaves->total() }} Pending Requests
                </span>
            </div>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Employee</th>
                            <th>Leave Type</th>
                            <th>Dates</th>
                            <th>Duration</th>
                            <th>Reason</th>
                            <th>Requested</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingLeaves as $leave)
                        <tr>
                            <td>{{ $leave->employee->name }}</td>
                            <td>
                                <span class="badge bg-info">
                                    {{ $leave->leaveType->name }}
                                </span>
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($leave->start_date)->format('M d') }} - 
                                {{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}
                            </td>
                            <td class="fw-bold">{{ $leave->total_days }} days</td>
                            <td class="text-truncate" style="max-width: 200px;">
                                {{ $leave->reason }}
                            </td>
                            <td>{{ $leave->created_at->diffForHumans() }}</td>
                            <td>
                                <a href="{{ route('hr.leaves.show', $leave) }}" 
                                   class="btn btn-sm btn-primary">
                                   <i class="bi bi-eye"></i> Review
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bi bi-check-circle-fill text-success fs-1 mb-2"></i>
                                    <h4>No pending leave requests</h4>
                                    <p class="text-muted">All leave requests have been processed</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($pendingLeaves->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $pendingLeaves->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection