@extends('layouts.app')
@section('title', 'My Time Corrections')

@section('content')
<style>
    :root {
        --tc-primary: #4a90e2;
        --tc-success: #0ecb81;
        --tc-danger: #f6465d;
        --tc-warning: #f0b90b;
        --tc-gray-50: #f8f9fc;
        --tc-gray-100: #f1f3f8;
        --tc-gray-200: #e2e6ef;
        --tc-gray-600: #6c757d;
        --tc-gray-800: #2d3748;
        --tc-gray-900: #1a202c;
        --tc-radius: 12px;
        --tc-radius-sm: 8px;
        --tc-shadow: 0 4px 20px rgba(0,0,0,0.06);
        --tc-transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .tc-card {
        background: #fff;
        border-radius: var(--tc-radius);
        box-shadow: var(--tc-shadow);
        border: 1px solid var(--tc-gray-100);
        overflow: hidden;
    }

    .tc-card-header {
        background: linear-gradient(135deg, #4a90e2, #357abd);
        color: #fff;
        padding: 1.5rem 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .tc-card-header h5 {
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }

    .tc-card-body {
        padding: 1.5rem;
    }

    .tc-table {
        width: 100%;
        border-collapse: collapse;
    }

    .tc-table thead th {
        padding: 0.75rem 1rem;
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--tc-gray-600);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid var(--tc-gray-200);
        background: var(--tc-gray-50);
        text-align: left;
        white-space: nowrap;
    }

    .tc-table tbody td {
        padding: 0.85rem 1rem;
        font-size: 0.88rem;
        color: var(--tc-gray-800);
        border-bottom: 1px solid var(--tc-gray-100);
        vertical-align: middle;
    }

    .tc-table tbody tr:hover {
        background: rgba(74, 144, 226, 0.03);
    }

    .tc-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.3rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .tc-badge.pending { background: #fef3e6; color: #d97706; }
    .tc-badge.approved { background: #e6faf0; color: #059669; }
    .tc-badge.rejected { background: #fee8eb; color: #dc2626; }

    .tc-type-tag {
        display: inline-block;
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        font-size: 0.78rem;
        font-weight: 500;
        background: var(--tc-gray-50);
        color: var(--tc-gray-600);
        border: 1px solid var(--tc-gray-200);
    }

    .tc-empty {
        text-align: center;
        padding: 3rem 1rem;
        color: var(--tc-gray-600);
    }

    .tc-empty i {
        font-size: 2.5rem;
        color: var(--tc-gray-200);
        display: block;
        margin-bottom: 0.75rem;
    }

    @media (max-width: 768px) {
        .tc-table thead { display: none; }
        .tc-table tbody tr {
            display: block;
            padding: 0.75rem;
            border-bottom: 2px solid var(--tc-gray-100);
        }
        .tc-table tbody td {
            display: flex;
            justify-content: space-between;
            padding: 0.4rem 0;
            border: none;
        }
        .tc-table tbody td::before {
            content: attr(data-label);
            font-weight: 600;
            font-size: 0.75rem;
            color: var(--tc-gray-600);
            text-transform: uppercase;
            margin-right: 1rem;
        }
    }
</style>

<div class="tc-card">
    <div class="tc-card-header">
        <h5><i class="bi bi-clock-history"></i> My Time Correction Requests</h5>
        <a href="{{ route('time-corrections.create') }}" class="btn btn-light btn-sm fw-semibold">
            <i class="bi bi-plus-lg"></i> New Request
        </a>
    </div>
    <div class="tc-card-body">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($requests->count())
        <div style="overflow-x:auto;">
            <table class="tc-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Requested Time</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Requested On</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($requests as $r)
                    <tr>
                        <td data-label="Date">{{ \Carbon\Carbon::parse($r->date)->format('M d, Y') }}</td>
                        <td data-label="Type">
                            <span class="tc-type-tag">
                                @if($r->type === 'clock_in') <i class="bi bi-sign-turn-left"></i> Clock In
                                @elseif($r->type === 'clock_out') <i class="bi bi-sign-turn-right"></i> Clock Out
                                @else <i class="bi bi-arrow-left-right"></i> Both
                                @endif
                            </span>
                        </td>
                        <td data-label="Requested Time">
                            @if($r->requested_time_in)
                                In: {{ \Carbon\Carbon::parse($r->requested_time_in)->format('h:i A') }}<br>
                            @endif
                            @if($r->requested_time_out)
                                Out: {{ \Carbon\Carbon::parse($r->requested_time_out)->format('h:i A') }}
                            @endif
                        </td>
                        <td data-label="Reason" style="max-width:220px;">
                            <span class="text-truncate d-inline-block" style="max-width:200px;overflow:hidden;text-overflow:ellipsis;" title="{{ $r->reason }}">
                                {{ $r->reason }}
                            </span>
                        </td>
                        <td data-label="Status">
                            <span class="tc-badge {{ $r->status }}">
                                <i class="bi {{ $r->status === 'approved' ? 'bi-check-circle' : ($r->status === 'rejected' ? 'bi-x-circle' : 'bi-hourglass-split') }}"></i>
                                {{ ucfirst($r->status) }}
                            </span>
                            @if($r->status === 'rejected' && $r->review_notes)
                                <br><small class="text-muted" style="font-size:0.75rem;">Reason: {{ $r->review_notes }}</small>
                            @endif
                        </td>
                        <td data-label="Requested On">{{ $r->created_at->format('M d, h:i A') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="tc-empty">
            <i class="bi bi-inbox"></i>
            <h6>No correction requests yet</h6>
            <p class="mb-3">If you forgot to clock in or out, submit a correction request.</p>
            <a href="{{ route('time-corrections.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Submit Your First Request
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
