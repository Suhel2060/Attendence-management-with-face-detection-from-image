@extends('layouts.app')
@section('title', 'Time Corrections')

@section('content')
<style>
    :root {
        --hrc-primary: #4a90e2;
        --hrc-success: #0ecb81;
        --hrc-danger: #f6465d;
        --hrc-warning: #f0b90b;
        --hrc-gray-50: #f8f9fc;
        --hrc-gray-100: #f1f3f8;
        --hrc-gray-200: #e2e6ef;
        --hrc-gray-600: #6c757d;
        --hrc-gray-800: #2d3748;
        --hrc-gray-900: #1a202c;
        --hrc-radius: 12px;
        --hrc-radius-sm: 8px;
        --hrc-shadow: 0 4px 20px rgba(0,0,0,0.06);
        --hrc-transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .hrc-card {
        background: #fff;
        border-radius: var(--hrc-radius);
        box-shadow: var(--hrc-shadow);
        border: 1px solid var(--hrc-gray-100);
        overflow: hidden;
    }

    .hrc-card-header {
        background: linear-gradient(135deg, #4a90e2, #357abd);
        color: #fff;
        padding: 1.5rem 2rem;
    }

    .hrc-card-header h5 {
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }

    .hrc-card-header p {
        margin: 0.3rem 0 0;
        opacity: 0.8;
        font-size: 0.88rem;
    }

    .hrc-card-body {
        padding: 1.5rem;
    }

    .hrc-filter-bar {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        margin-bottom: 1.25rem;
        align-items: center;
    }

    .hrc-filter-bar .btn-group .btn {
        font-size: 0.82rem;
        font-weight: 500;
        border-radius: var(--hrc-radius-sm);
        padding: 0.4rem 1rem;
    }

    .hrc-table {
        width: 100%;
        border-collapse: collapse;
    }

    .hrc-table thead th {
        padding: 0.75rem 1rem;
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--hrc-gray-600);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid var(--hrc-gray-200);
        background: var(--hrc-gray-50);
        text-align: left;
        white-space: nowrap;
    }

    .hrc-table tbody td {
        padding: 0.9rem 1rem;
        font-size: 0.88rem;
        color: var(--hrc-gray-800);
        border-bottom: 1px solid var(--hrc-gray-100);
        vertical-align: middle;
    }

    .hrc-table tbody tr:hover {
        background: rgba(74, 144, 226, 0.03);
    }

    .hrc-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.3rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .hrc-badge.pending { background: #fef3e6; color: #d97706; }
    .hrc-badge.approved { background: #e6faf0; color: #059669; }
    .hrc-badge.rejected { background: #fee8eb; color: #dc2626; }

    .hrc-type-tag {
        display: inline-block;
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        font-size: 0.78rem;
        font-weight: 500;
        background: var(--hrc-gray-50);
        color: var(--hrc-gray-600);
        border: 1px solid var(--hrc-gray-200);
    }

    .hrc-employee-cell {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .hrc-employee-cell .name {
        font-weight: 600;
    }

    .hrc-employee-cell .dept {
        font-size: 0.75rem;
        color: var(--hrc-gray-600);
    }

    .hrc-action-btn {
        padding: 0.4rem 1rem;
        border-radius: var(--hrc-radius-sm);
        font-size: 0.82rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: var(--hrc-transition);
    }

    .hrc-action-btn.approve {
        background: #e6faf0;
        color: #059669;
    }

    .hrc-action-btn.approve:hover {
        background: #059669;
        color: #fff;
    }

    .hrc-action-btn.reject {
        background: #fee8eb;
        color: #dc2626;
    }

    .hrc-action-btn.reject:hover {
        background: #dc2626;
        color: #fff;
    }

    .hrc-action-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none !important;
    }

    .hrc-empty {
        text-align: center;
        padding: 3rem 1rem;
        color: var(--hrc-gray-600);
    }

    .hrc-empty i {
        font-size: 2.5rem;
        color: var(--hrc-gray-200);
        display: block;
        margin-bottom: 0.75rem;
    }

    .hrc-reject-textarea {
        border: 2px solid var(--hrc-gray-200);
        border-radius: var(--hrc-radius-sm);
        padding: 0.75rem;
        font-size: 0.88rem;
        width: 100%;
        transition: var(--hrc-transition);
    }

    .hrc-reject-textarea:focus {
        border-color: var(--hrc-danger);
        box-shadow: 0 0 0 4px rgba(246, 70, 93, 0.1);
        outline: none;
    }

    @media (max-width: 768px) {
        .hrc-table thead { display: none; }
        .hrc-table tbody tr {
            display: block;
            padding: 0.75rem;
            border-bottom: 2px solid var(--hrc-gray-100);
        }
        .hrc-table tbody td {
            display: flex;
            justify-content: space-between;
            padding: 0.4rem 0;
            border: none;
            min-height: 34px;
        }
        .hrc-table tbody td::before {
            content: attr(data-label);
            font-weight: 600;
            font-size: 0.75rem;
            color: var(--hrc-gray-600);
            text-transform: uppercase;
            margin-right: 1rem;
        }
    }
</style>

<div class="hrc-card">
    <div class="hrc-card-header">
        <h5><i class="bi bi-clock-history"></i> Time Correction Requests</h5>
        <p>Review and approve/reject employee time correction requests</p>
    </div>
    <div class="hrc-card-body">
        <div class="hrc-filter-bar">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-secondary active hrc-filter-btn" data-filter="all">All</button>
                <button type="button" class="btn btn-outline-warning hrc-filter-btn" data-filter="pending">Pending</button>
                <button type="button" class="btn btn-outline-success hrc-filter-btn" data-filter="approved">Approved</button>
                <button type="button" class="btn btn-outline-danger hrc-filter-btn" data-filter="rejected">Rejected</button>
            </div>
            <span style="font-size:0.82rem;color:var(--hrc-gray-600);">
                <i class="bi bi-info-circle"></i> <span id="pendingCount">{{ $requests->where('status','pending')->count() }}</span> pending
            </span>
        </div>

        @if ($requests->count())
        <div style="overflow-x:auto;">
            <table class="hrc-table" id="correctionsTable">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Requested Time</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($requests as $r)
                    <tr data-status="{{ $r->status }}" id="correctionRow{{ $r->id }}">
                        <td data-label="Employee">
                            <div class="hrc-employee-cell">
                                @if($r->employee && $r->employee->image)
                                    <img src="{{ asset('storage/'.$r->employee->image) }}" style="width:34px;height:34px;border-radius:50%;object-fit:cover;">
                                @elseif($r->employee)
                                    <div style="width:34px;height:34px;border-radius:50%;background:var(--hrc-gray-200);display:flex;align-items:center;justify-content:center;">
                                        <i class="bi bi-person text-muted"></i>
                                    </div>
                                @endif
                                <div>
                                    <div class="name">{{ $r->employee->name ?? 'Unknown' }}</div>
                                    <div class="dept">{{ $r->employee->department ?? '' }} · {{ $r->employee_id }}</div>
                                </div>
                            </div>
                        </td>
                        <td data-label="Date">{{ \Carbon\Carbon::parse($r->date)->format('M d, Y') }}</td>
                        <td data-label="Type">
                            <span class="hrc-type-tag">
                                @if($r->type === 'clock_in') <i class="bi bi-sign-turn-left"></i> Clock In
                                @elseif($r->type === 'clock_out') <i class="bi bi-sign-turn-right"></i> Clock Out
                                @else <i class="bi bi-arrow-left-right"></i> Both
                                @endif
                            </span>
                        </td>
                        <td data-label="Requested Time">
                            <div style="font-size:0.85rem;">
                                @if($r->requested_time_in)
                                    <span class="text-primary"><i class="bi bi-box-arrow-in-right"></i> {{ \Carbon\Carbon::parse($r->requested_time_in)->format('h:i A') }}</span><br>
                                @endif
                                @if($r->requested_time_out)
                                    <span class="text-warning"><i class="bi bi-box-arrow-right"></i> {{ \Carbon\Carbon::parse($r->requested_time_out)->format('h:i A') }}</span>
                                @endif
                            </div>
                            @if($r->reason)
                                <small class="text-muted d-block" style="font-size:0.75rem;margin-top:2px;" title="{{ $r->reason }}">
                                    <i class="bi bi-chat-quote"></i> {{ Str::limit($r->reason, 50) }}
                                </small>
                            @endif
                        </td>
                        <td data-label="Status" class="status-cell">
                            @include('hr.time-corrections._status_badge', ['correction' => $r])
                        </td>
                        <td data-label="Actions" class="actions-cell">
                            @if($r->status === 'pending')
                            <div class="d-flex gap-1">
                                <button type="button" class="hrc-action-btn approve" data-id="{{ $r->id }}" data-name="{{ $r->employee->name ?? 'Unknown' }}">
                                    <i class="bi bi-check-lg"></i> Approve
                                </button>
                                <button type="button" class="hrc-action-btn reject" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $r->id }}">
                                    <i class="bi bi-x-lg"></i> Reject
                                </button>
                            </div>

                            <div class="modal fade" id="rejectModal{{ $r->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header" style="background:#f6465d;color:#fff;border:none;">
                                            <h6 class="modal-title"><i class="bi bi-x-circle"></i> Reject Correction</h6>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p style="font-size:0.9rem;color:var(--hrc-gray-600);">
                                                Rejecting correction for <strong>{{ $r->employee->name ?? 'Unknown' }}</strong>
                                                ({{ \Carbon\Carbon::parse($r->date)->format('M d, Y') }})
                                            </p>
                                            <label style="font-weight:600;font-size:0.82rem;color:var(--hrc-gray-800);">Reason for rejection *</label>
                                            <textarea id="rejectNotes{{ $r->id }}" class="hrc-reject-textarea" rows="3" placeholder="Provide a reason..." minlength="5"></textarea>
                                        </div>
                                        <div class="modal-footer" style="border-top:1px solid var(--hrc-gray-100);">
                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                            <button type="button" class="btn btn-danger btn-sm btn-reject-confirm" data-id="{{ $r->id }}">
                                                <i class="bi bi-x-lg"></i> Reject
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @else
                            <span class="text-muted" style="font-size:0.82rem;">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="hrc-empty">
            <i class="bi bi-inbox"></i>
            <h6>No correction requests</h6>
            <p class="mb-0">Employees can submit time correction requests if they forget to clock in/out.</p>
        </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    // Filter buttons
    document.querySelectorAll('.hrc-filter-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.hrc-filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const filter = this.dataset.filter;
            document.querySelectorAll('#correctionsTable tbody tr').forEach(row => {
                row.style.display = (filter === 'all' || row.dataset.status === filter) ? '' : 'none';
            });
        });
    });

    // Approve via AJAX
    document.querySelectorAll('.hrc-action-btn.approve').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            const name = this.dataset.name;

            Swal.fire({
                title: 'Approve Correction?',
                html: `Approve correction for <strong>${name}</strong>?<br>The attendance record will be updated.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#059669',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Approve',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (!result.isConfirmed) return;

                const row = document.getElementById('correctionRow' + id);
                const approveBtn = row.querySelector('.hrc-action-btn.approve');
                const rejectBtn = row.querySelector('.hrc-action-btn.reject');
                approveBtn.disabled = true;
                rejectBtn.disabled = true;
                approveBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

                fetch('{{ route("hr.time-corrections.approve", "PLACEHOLDER") }}'.replace('PLACEHOLDER', id), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: 'Approved!', text: data.message, timer: 2000, showConfirmButton: false });

                        // Update row status
                        row.dataset.status = 'approved';
                        row.querySelector('.status-cell').innerHTML = data.status_html;
                        row.querySelector('.actions-cell').innerHTML = '<span class="text-muted" style="font-size:0.82rem;">—</span>';

                        // Update pending count
                        const countEl = document.getElementById('pendingCount');
                        countEl.textContent = Math.max(0, parseInt(countEl.textContent) - 1);
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Failed to approve.' });
                        approveBtn.disabled = false;
                        rejectBtn.disabled = false;
                        approveBtn.innerHTML = '<i class="bi bi-check-lg"></i> Approve';
                    }
                })
                .catch(() => {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Network error. Please try again.' });
                    approveBtn.disabled = false;
                    rejectBtn.disabled = false;
                    approveBtn.innerHTML = '<i class="bi bi-check-lg"></i> Approve';
                });
            });
        });
    });

    // Reject via AJAX
    document.querySelectorAll('.btn-reject-confirm').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            const notes = document.getElementById('rejectNotes' + id).value.trim();
            const row = document.getElementById('correctionRow' + id);
            const approveBtn = row.querySelector('.hrc-action-btn.approve');
            const rejectBtns = row.querySelectorAll('.hrc-action-btn.reject');

            if (!notes || notes.length < 5) {
                Swal.fire({ icon: 'warning', title: 'Required', text: 'Please provide a reason for rejection (at least 5 characters).' });
                return;
            }

            if (approveBtn) approveBtn.disabled = true;
            rejectBtns.forEach(b => b.disabled = true);
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            fetch('{{ route("hr.time-corrections.reject", "PLACEHOLDER") }}'.replace('PLACEHOLDER', id), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ review_notes: notes })
            })
            .then(res => res.json())
            .then(data => {
                // Close modal
                const modalEl = document.getElementById('rejectModal' + id);
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();

                if (data.success) {
                    Swal.fire({ icon: 'success', title: 'Rejected', text: data.message, timer: 2000, showConfirmButton: false });

                    row.dataset.status = 'rejected';
                    row.querySelector('.status-cell').innerHTML = data.status_html;
                    row.querySelector('.actions-cell').innerHTML = '<span class="text-muted" style="font-size:0.82rem;">—</span>';

                    const countEl = document.getElementById('pendingCount');
                    countEl.textContent = Math.max(0, parseInt(countEl.textContent) - 1);
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Failed to reject.' });
                    if (approveBtn) approveBtn.disabled = false;
                    rejectBtns.forEach(b => b.disabled = false);
                    this.disabled = false;
                    this.innerHTML = '<i class="bi bi-x-lg"></i> Reject';
                }
            })
            .catch(() => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Network error. Please try again.' });
                if (approveBtn) approveBtn.disabled = false;
                rejectBtns.forEach(b => b.disabled = false);
                this.disabled = false;
                this.innerHTML = '<i class="bi bi-x-lg"></i> Reject';
            });
        });
    });

    // Reset reject modal on hide
    document.querySelectorAll('[id^="rejectModal"]').forEach(el => {
        el.addEventListener('hidden.bs.modal', function () {
            const id = this.id.replace('rejectModal', '');
            const btn = this.querySelector('.btn-reject-confirm');
            if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bi bi-x-lg"></i> Reject'; }
        });
    });
});
</script>
@endsection
