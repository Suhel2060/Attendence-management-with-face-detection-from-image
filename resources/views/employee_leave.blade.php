@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">My Leave Dashboard</h4>
                </div>
                <div class="card-body">
                    
                    <!-- Leave Balances Summary -->
                    <div class="mb-5">
                        <h5 class="mb-4 text-primary">
                            <i class="fas fa-chart-pie me-2"></i>Leave Balances Summary
                        </h5>
                        <div class="row">
                            @foreach($leaveBalances as $balance)
                            <div class="col-md-4 mb-3">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="fw-bold text-primary">{{ $balance->name }}</h6>
                                                <div class="d-flex align-items-center mt-2">
                                                    <span class="badge bg-info me-2">Max: {{ $balance->max_days }} days</span>
                                                    <span class="badge bg-success">Remaining: {{ $balance->remaining }} days</span>
                                                </div>
                                            </div>
                                            <div class="position-relative">
                                                <div class="progress-circle" 
                                                    data-percent="{{ round(($balance->taken_days / $balance->max_days) * 100) }}" 
                                                    data-color="{{ $balance->remaining > 0 ? '#20c997' : '#dc3545' }}">
                                                    <span>{{ round(($balance->taken_days / $balance->max_days) * 100) }}%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="progress mt-3" style="height: 8px;">
                                            <div class="progress-bar" 
                                                role="progressbar" 
                                                style="width: {{ min(100, ($balance->taken_days / $balance->max_days) * 100) }}%; 
                                                       background-color: {{ $balance->remaining > 0 ? '#20c997' : '#dc3545' }};" 
                                                aria-valuenow="{{ $balance->taken_days }}" 
                                                aria-valuemin="0" 
                                                aria-valuemax="{{ $balance->max_days }}">
                                            </div>
                                        </div>
                                        <div class="mt-2 d-flex justify-content-between small text-muted">
                                            <span>Taken: {{ $balance->taken_days }} days</span>
                                            <span>Available: {{ $balance->remaining }} days</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Leave Requests -->
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="text-primary">
                                <i class="fas fa-list-alt me-2"></i>My Leave Requests
                            </h5>
                            <a href="{{ route('leaves.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i> New Leave Request
                            </a>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Leave Type</th>
                                        <th>Dates</th>
                                        <th>Duration</th>
                                        <th>Reason</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($leaveRequests as $request)
                                    <tr>
                                        <td>
                                            <span class="badge" style="background-color: {{ $request->leaveType->color ?? '#6c757d' }}; color: white;">
                                                {{ $request->leaveType->name }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($request->start_date)->format('M d, Y') }} - 
                                            {{ \Carbon\Carbon::parse($request->end_date)->format('M d, Y') }}
                                        </td>
                                        <td>{{ $request->total_days }} days</td>
                                        <td>{{ \Illuminate\Support\Str::limit($request->reason, 30) }}</td>
                                        <td>
                                            @if($request->status === \App\Models\Leaves::STATUS_APPROVED)
                                                <span class="badge bg-success">Approved</span>
                                            @elseif($request->status === \App\Models\Leaves::STATUS_PENDING)
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @else
                                                <span class="badge bg-danger">Rejected</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary view-details" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#leaveDetailsModal"
                                                    data-type="{{ $request->leaveType->name }}"
                                                    data-start="{{ \Carbon\Carbon::parse($request->start_date)->format('M d, Y') }}"
                                                    data-end="{{ \Carbon\Carbon::parse($request->end_date)->format('M d, Y') }}"
                                                    data-duration="{{ $request->total_days }}"
                                                    data-reason="{{ $request->reason }}"
                                                    data-status="{{ ucfirst($request->status) }}"
                                                    data-created="{{ $request->created_at->format('M d, Y h:i A') }}">
                                                <i class="fas fa-eye"></i> Details
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="fas fa-inbox fa-2x mb-3 text-muted"></i>
                                            <p class="text-muted">No leave requests found. Click "New Leave Request" to create one.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        @if($leaveRequests->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $leaveRequests->links() }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Leave Details Modal -->
<div class="modal fade" id="leaveDetailsModal" tabindex="-1" aria-labelledby="leaveDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="leaveDetailsModalLabel">Leave Request Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label text-muted small">Leave Type</label>
                    <p class="fw-bold" id="detail-type"></p>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted small">Start Date</label>
                        <p class="fw-bold" id="detail-start"></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small">End Date</label>
                        <p class="fw-bold" id="detail-end"></p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted small">Duration</label>
                        <p class="fw-bold" id="detail-duration"></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small">Status</label>
                        <p class="fw-bold" id="detail-status"></p>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small">Reason</label>
                    <p id="detail-reason" class="border rounded p-3 bg-light"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small">Requested On</label>
                    <p class="fw-bold" id="detail-created"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card-header {
        border-radius: 0.5rem 0.5rem 0 0 !important;
    }
    
    .progress-circle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: #e9ecef;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .progress-circle::after {
        content: "";
        position: absolute;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: white;
    }
    
    .progress-circle span {
        position: relative;
        z-index: 1;
        font-size: 0.8rem;
        font-weight: bold;
    }
    
    .table th {
        font-weight: 600;
        color: #495057;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.05);
    }
    
    .badge.bg-warning {
        background-color: #ffc107 !important;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Leave Details Modal
        const viewButtons = document.querySelectorAll('.view-details');
        viewButtons.forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('detail-type').textContent = this.getAttribute('data-type');
                document.getElementById('detail-start').textContent = this.getAttribute('data-start');
                document.getElementById('detail-end').textContent = this.getAttribute('data-end');
                document.getElementById('detail-duration').textContent = this.getAttribute('data-duration') + ' days';
                document.getElementById('detail-reason').textContent = this.getAttribute('data-reason');
                document.getElementById('detail-status').textContent = this.getAttribute('data-status');
                document.getElementById('detail-created').textContent = this.getAttribute('data-created');
                
                // Set status badge color
                const statusElement = document.getElementById('detail-status');
                const status = this.getAttribute('data-status').toLowerCase();
                statusElement.className = 'fw-bold';
                
                if (status === 'approved') {
                    statusElement.classList.add('text-success');
                } else if (status === 'pending') {
                    statusElement.classList.add('text-warning');
                } else {
                    statusElement.classList.add('text-danger');
                }
            });
        });
        
        // Initialize circular progress indicators
        const circles = document.querySelectorAll('.progress-circle');
        circles.forEach(circle => {
            const percent = circle.getAttribute('data-percent');
            const color = circle.getAttribute('data-color');
            
            const svgNS = "http://www.w3.org/2000/svg";
            const svg = document.createElementNS(svgNS, "svg");
            svg.setAttribute("width", "60");
            svg.setAttribute("height", "60");
            svg.setAttribute("viewBox", "0 0 60 60");
            svg.style.position = 'absolute';
            svg.style.top = '0';
            svg.style.left = '0';
            
            // Background circle
            const bgCircle = document.createElementNS(svgNS, "circle");
            bgCircle.setAttribute("cx", "30");
            bgCircle.setAttribute("cy", "30");
            bgCircle.setAttribute("r", "28");
            bgCircle.setAttribute("fill", "none");
            bgCircle.setAttribute("stroke", "#e9ecef");
            bgCircle.setAttribute("stroke-width", "4");
            svg.appendChild(bgCircle);
            
            // Progress circle
            const progressCircle = document.createElementNS(svgNS, "circle");
            progressCircle.setAttribute("cx", "30");
            progressCircle.setAttribute("cy", "30");
            progressCircle.setAttribute("r", "28");
            progressCircle.setAttribute("fill", "none");
            progressCircle.setAttribute("stroke", color);
            progressCircle.setAttribute("stroke-width", "4");
            progressCircle.setAttribute("stroke-dasharray", "175.929");
            progressCircle.setAttribute("stroke-dashoffset", 175.929 - (175.929 * percent / 100));
            progressCircle.setAttribute("transform", "rotate(-90 30 30)");
            svg.appendChild(progressCircle);
            
            circle.prepend(svg);
        });
    });
</script>
@endpush