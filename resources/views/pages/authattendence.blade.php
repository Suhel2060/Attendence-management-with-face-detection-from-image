@extends('layouts.app')

@section('title', 'My Attendance')

@section('content')
@php
    $total = $records->count();
    $present = $records->where('status', 'present')->count();
    $absent = $records->where('status', 'absent')->count();
    $leave = $records->where('status', 'leave')->count();
    $percentage = $total > 0 ? round(($present / $total) * 100) : 0;
    $grouped = $records->groupBy(fn($r) => \Carbon\Carbon::parse($r->date)->format('F Y'));
@endphp

<style>
:root {
    --att-primary: #4361ee;
    --att-success: #06d6a0;
    --att-danger: #ef476f;
    --att-warning: #ffd166;
    --att-info: #4cc9f0;
}

/* Stats Cards */
.stat-card {
    border-radius: 16px;
    border: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}
.stat-card::after {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    transition: all 0.5s ease;
}
.stat-card:hover::after {
    transform: scale(1.5);
}
.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 40px rgba(0,0,0,0.12);
}
.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
}
.stat-value {
    font-size: 1.75rem;
    font-weight: 800;
    line-height: 1.2;
}
.stat-label {
    font-size: 0.8rem;
    font-weight: 500;
    opacity: 0.8;
}

/* Progress Ring */
.progress-ring {
    width: 64px;
    height: 64px;
    position: relative;
}
.progress-ring svg {
    transform: rotate(-90deg);
}
.progress-ring .bg-circle {
    fill: none;
    stroke: #e9ecef;
    stroke-width: 5;
}
.progress-ring .fg-circle {
    fill: none;
    stroke: var(--att-primary);
    stroke-width: 5;
    stroke-linecap: round;
    transition: stroke-dashoffset 1s ease;
}
.progress-ring .center-text {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    font-weight: 800;
    color: var(--att-primary);
}

/* Filter Bar */
.filter-bar {
    background: white;
    border-radius: 16px;
    border: 1px solid rgba(0,0,0,0.04);
}
.filter-bar .form-select, .filter-bar .form-control {
    border-radius: 10px;
    border: 2px solid #e2e8f0;
    font-size: 0.88rem;
    padding: 0.5rem 1rem;
    transition: all 0.2s ease;
}
.filter-bar .form-select:focus, .filter-bar .form-control:focus {
    border-color: var(--att-primary);
    box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.12);
}

/* Month Group Header */
.month-group-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #eef1f5 100%);
    border-radius: 12px;
    padding: 0.75rem 1.25rem;
    font-weight: 700;
    font-size: 0.9rem;
    color: #495057;
    letter-spacing: 0.3px;
    position: sticky;
    top: 0;
    z-index: 2;
}

/* Table */
.att-table {
    border-collapse: separate;
    border-spacing: 0 4px;
}
.att-table thead th {
    border: none;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: #6c757d;
    padding: 0.75rem 1rem;
}
.att-table tbody tr {
    background: white;
    border-radius: 12px;
    transition: all 0.25s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.att-table tbody tr:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.06);
}
.att-table tbody td {
    border: none;
    padding: 1rem;
    vertical-align: middle;
    font-size: 0.9rem;
}
.att-table tbody tr td:first-child {
    border-radius: 12px 0 0 12px;
    padding-left: 1.25rem;
}
.att-table tbody tr td:last-child {
    border-radius: 0 12px 12px 0;
    padding-right: 1.25rem;
}

/* Status Badges */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 14px;
    border-radius: 20px;
    font-size: 0.78rem;
    font-weight: 600;
    letter-spacing: 0.3px;
}
.status-badge.present {
    background: rgba(6, 214, 160, 0.12);
    color: #059669;
}
.status-badge.absent {
    background: rgba(239, 71, 111, 0.12);
    color: #dc2626;
}
.status-badge.leave {
    background: rgba(255, 209, 102, 0.18);
    color: #b45309;
}

/* Time chip */
.time-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #f1f3f5;
    padding: 4px 12px;
    border-radius: 8px;
    font-size: 0.82rem;
    font-weight: 600;
    color: #495057;
}
.time-chip i {
    font-size: 0.75rem;
    color: #6c757d;
}
.time-chip.missing {
    background: transparent;
    color: #adb5bd;
}

/* Date display */
.date-display {
    display: flex;
    flex-direction: column;
    line-height: 1.3;
}
.date-display .day {
    font-weight: 700;
    font-size: 1rem;
    color: #212529;
}
.date-display .month-year {
    font-size: 0.75rem;
    color: #6c757d;
    font-weight: 500;
}

/* Empty state */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
}
.empty-state i {
    font-size: 3.5rem;
    color: #dee2e6;
    margin-bottom: 1rem;
}
.empty-state h5 {
    color: #6c757d;
    font-weight: 600;
}
.empty-state p {
    color: #adb5bd;
    font-size: 0.9rem;
}

/* Animations */
@keyframes fadeSlideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-in {
    animation: fadeSlideUp 0.5s ease forwards;
    opacity: 0;
}
.animate-in:nth-child(1) { animation-delay: 0.05s; }
.animate-in:nth-child(2) { animation-delay: 0.1s; }
.animate-in:nth-child(3) { animation-delay: 0.15s; }
.animate-in:nth-child(4) { animation-delay: 0.2s; }
.animate-in:nth-child(5) { animation-delay: 0.25s; }

/* Responsive */
@media (max-width: 576px) {
    .stat-value { font-size: 1.3rem; }
    .stat-icon { width: 40px; height: 40px; font-size: 1rem; }
    .progress-ring { width: 50px; height: 50px; }
    .time-chip { font-size: 0.75rem; padding: 3px 8px; }
    .att-table tbody td { padding: 0.75rem 0.75rem; font-size: 0.82rem; }
}
</style>

<div class="container-fluid px-0">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 animate-in">
        <div>
            <h4 class="fw-bold mb-1" style="color: #212529;">
                <i class="bi bi-clock-history me-2" style="color: var(--att-primary);"></i>My Attendance
            </h4>
            <p class="text-muted mb-0" style="font-size: 0.88rem;">
                <i class="bi bi-person-badge me-1"></i>{{ $usr->name }} &middot;
                {{ $total }} total records
            </p>
        </div>
        <div class="d-flex gap-2 mt-2 mt-sm-0">
            <div class="input-group input-group-sm" style="width: auto;">
                <span class="input-group-text bg-white border-0" style="border-radius: 10px 0 0 10px;">
                    <i class="bi bi-calendar3 text-muted"></i>
                </span>
                <select id="monthFilter" class="form-select" style="border-radius: 0 10px 10px 0; min-width: 140px;">
                    <option value="all">All Months</option>
                    @foreach($grouped->keys() as $month)
                        <option value="{{ $month }}">{{ $month }}</option>
                    @endforeach
                </select>
            </div>
            <div class="input-group input-group-sm" style="width: auto;">
                <span class="input-group-text bg-white border-0" style="border-radius: 10px 0 0 10px;">
                    <i class="bi bi-funnel text-muted"></i>
                </span>
                <select id="statusFilter" class="form-select" style="border-radius: 0 10px 10px 0; min-width: 120px;">
                    <option value="all">All Status</option>
                    <option value="present">Present</option>
                    <option value="absent">Absent</option>
                    <option value="leave">Leave</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3 animate-in">
            <div class="stat-card card bg-white p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: rgba(67, 97, 238, 0.1); color: var(--att-primary);">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <div>
                        <div class="stat-value">{{ $total }}</div>
                        <div class="stat-label">Total Days</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 animate-in">
            <div class="stat-card card bg-white p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: rgba(6, 214, 160, 0.12); color: var(--att-success);">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div>
                        <div class="stat-value">{{ $present }}</div>
                        <div class="stat-label">Present</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 animate-in">
            <div class="stat-card card bg-white p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: rgba(239, 71, 111, 0.1); color: var(--att-danger);">
                        <i class="bi bi-x-circle"></i>
                    </div>
                    <div>
                        <div class="stat-value">{{ $absent }}</div>
                        <div class="stat-label">Absent</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 animate-in">
            <div class="stat-card card bg-white p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="progress-ring flex-shrink-0">
                        <svg viewBox="0 0 36 36" width="64" height="64">
                            <circle class="bg-circle" cx="18" cy="18" r="15"/>
                            <circle class="fg-circle" cx="18" cy="18" r="15"
                                stroke-dasharray="94.248"
                                stroke-dashoffset="{{ 94.248 - (94.248 * $percentage / 100) }}"
                                stroke="{{ $percentage >= 75 ? 'var(--att-success)' : ($percentage >= 50 ? 'var(--att-warning)' : 'var(--att-danger)') }}"/>
                        </svg>
                        <span class="center-text">{{ $percentage }}%</span>
                    </div>
                    <div>
                        <div class="stat-value" style="font-size: 1.1rem;">{{ $percentage }}%</div>
                        <div class="stat-label">Attendance</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="card border-0 shadow-sm animate-in" style="border-radius: 16px;">
        <div class="card-body p-0">
            @if($records->isEmpty())
                <div class="empty-state">
                    <i class="bi bi-journal-text"></i>
                    <h5>No Attendance Records</h5>
                    <p class="mb-0">Your attendance history will appear here once you start marking attendance.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table att-table mb-0" id="attendanceTable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $record)
                            @php
                                $date = \Carbon\Carbon::parse($record->date);
                                $monthKey = $date->format('F Y');
                            @endphp
                            <tr class="att-row" data-month="{{ $monthKey }}" data-status="{{ $record->status }}">
                                <td>
                                    <div class="date-display">
                                        <span class="day">{{ $date->format('d') }} {{ $date->format('M') }}</span>
                                        <span class="month-year">{{ $date->format('Y') }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge {{ $record->status }}">
                                        <i class="bi bi-{{
                                            $record->status === 'present' ? 'check-circle-fill' :
                                            ($record->status === 'absent' ? 'x-circle-fill' : 'question-circle-fill')
                                        }}"></i>
                                        {{ ucfirst($record->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($record->check_in)
                                        <span class="time-chip">
                                            <i class="bi bi-door-open"></i>
                                            {{ \Carbon\Carbon::parse($record->check_in)->format('h:i A') }}
                                        </span>
                                    @else
                                        <span class="time-chip missing">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($record->check_out)
                                        <span class="time-chip">
                                            <i class="bi bi-door-closed"></i>
                                            {{ \Carbon\Carbon::parse($record->check_out)->format('h:i A') }}
                                        </span>
                                    @else
                                        <span class="time-chip missing">—</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    const $rows = $('.att-row');
    const $monthFilter = $('#monthFilter');
    const $statusFilter = $('#statusFilter');

    function filterRows() {
        const month = $monthFilter.val();
        const status = $statusFilter.val();

        $rows.each(function () {
            const matchMonth = month === 'all' || $(this).data('month') === month;
            const matchStatus = status === 'all' || $(this).data('status') === status;
            $(this).toggle(matchMonth && matchStatus);
        });

        const visible = $rows.filter(':visible').length;
        const total = $rows.length;
    }

    $monthFilter.on('change', filterRows);
    $statusFilter.on('change', filterRows);
});
</script>
@endsection
