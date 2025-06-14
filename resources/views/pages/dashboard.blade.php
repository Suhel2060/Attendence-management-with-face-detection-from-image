@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="row g-4 my-1">
        <!-- Statistics Cards -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card shadow border-0 overflow-hidden">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-gradient-primary p-3 rounded-3">
                            <i class="fas fa-users fa-2x text-white"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1">Total Employees</h6>
                            <h3 class="mb-0">245</h3>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="badge bg-soft-success text-success">
                            <i class="fas fa-arrow-up"></i> 12.5%
                        </span>
                        <span class="text-muted ms-2">Since last month</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Department Card -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card shadow border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-gradient-success p-3 rounded-3">
                            <i class="fas fa-building fa-2x text-white"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1">Departments</h6>
                            <h3 class="mb-0">14</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Leave Requests Card -->
        <div class="col-12 col-xl-5">
            <div class="card shadow border-0 h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">My Leave Requests</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Leave Type</th>
                                    <th>Dates</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                    <th>Requested</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(Auth::user()->leaves()->latest()->take(3)->get() as $leave)
                                <tr>
                                    <td>{{ $leave->leaveType->name }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($leave->start_date)->format('Y-m-d') }} -
                                        {{ \Carbon\Carbon::parse($leave->end_date)->format('Y-m-d') }}
                                    </td>
                                    <td>{{ $leave->total_days }} days</td>
                                    <td>
                                        @if($leave->status === 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @elseif($leave->status === 'rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td>{{ $leave->created_at->diffForHumans() }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No leave requests yet</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end mt-3">
                        <a href="{{ route('leaves.create') }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-circle me-1"></i> Request Leave
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="col-12 col-xl-7">
            <div class="card shadow border-0 h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Employee Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="departmentChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Attendance Chart -->
        <div class="col-12 col-xl-5">
            <div class="card shadow border-0 h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Attendance Rate</h5>
                </div>
                <div class="card-body">
                    <canvas id="attendanceChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Performance Chart -->
        <div class="col-12 col-xl-7">
            <div class="card shadow border-0 h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Performance Overview</h5>
                </div>
                <div class="card-body">
                    <canvas id="performanceChart" style="height: 250px;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Department Distribution Chart (Doughnut)
    const departmentCtx = document.getElementById('departmentChart');
    if (departmentCtx) {
        new Chart(departmentCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Engineering', 'Marketing', 'HR', 'Sales', 'Support'],
                datasets: [{
                    data: [35, 20, 15, 25, 5],
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                    hoverOffset: 10
                }]
            },
            options: {
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });
    }

    // Attendance Chart (Line)
    const attendanceCtx = document.getElementById('attendanceChart');
    if (attendanceCtx) {
        new Chart(attendanceCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Attendance Rate',
                    data: [95, 92, 96, 94, 97, 98],
                    borderColor: '#4e73df',
                    tension: 0.4,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: false,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });
    }

    // Performance Chart (Bar)
    const performanceCtx = document.getElementById('performanceChart');
    if (performanceCtx) {
        new Chart(performanceCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['John D.', 'Alice M.', 'Bob R.', 'Sarah K.', 'Mike T.'],
                datasets: [{
                    label: 'Performance Rating',
                    data: [88, 92, 85, 78, 95],
                    backgroundColor: '#1cc88a',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });
    }
});
</script>

<style>
.card {
    border-radius: 0.75rem;
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
}

.bg-gradient-primary {
    background: linear-gradient(45deg, #4e73df, #224abe);
}

.bg-gradient-success {
    background: linear-gradient(45deg, #1cc88a, #13855c);
}
</style>
