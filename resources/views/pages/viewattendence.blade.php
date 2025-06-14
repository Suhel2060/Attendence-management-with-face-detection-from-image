@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white py-3">
            <h4 class="mb-0">Attendance Records</h4>
        </div>

        <div class="card-body">
            <!-- Filter Section -->
            <div class="d-flex flex-wrap gap-3 mb-4 align-items-end">
                <form class="row g-3 flex-grow-1" method="GET" action="">
                    <div class="col-12 col-md-3">
                        <input type="date" name="date_from" class="form-control form-control-sm" 
                               value="{{ request('date_from') }}">
                    </div>
                    <div class="col-12 col-md-3">
                        <input type="date" name="date_to" class="form-control form-control-sm" 
                               value="{{ request('date_to') }}">
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" name="search" class="form-control form-control-sm" 
                                   placeholder="Search by name" value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-12 col-md-2">
                        <button class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-filter me-2"></i>Filter
                        </button>
                    </div>
                </form>
                
                <div class="ms-auto d-flex gap-2">
                  <a href="{{ url()->current() }}" class="btn btn-outline-secondary btn-sm">
                      <i class="fas fa-sync-alt me-2"></i>Clear Filters
                  </a>
                  <button class="btn btn-success btn-sm" id="exportBtn">
                    <i class="fas fa-download me-2"></i>Download CSV
                </button>
                  {{-- <a href="{{ route('your.download.route', request()->query()) }}" 
                     class="btn btn-success btn-sm">
                      <i class="fas fa-download me-2"></i>Download CSV
                  </a> --}}
              </div>
            </div>

            <!-- Attendance Table -->
            <div class="table-responsive rounded-3 border">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">Employee ID</th>
                            <th class="px-4 py-3">User</th>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Check In</th>
                            <th class="px-4 py-3">Check Out</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendences as $attendence)
                        <tr class="cursor-pointer">
                            <td class="px-4">{{ $attendence->employee_id }}</td>
                            <td class="px-4 fw-medium">{{ $attendence->user->name }}</td>
                            <td class="px-4">{{ $attendence->date }}</td>
                            <td class="px-4">
                                <span class="badge bg-{{ 
                                    $attendence->status === 'present' ? 'success' : 
                                    ($attendence->status === 'absent' ? 'danger' : 'warning') 
                                }} bg-opacity-10 text-{{ 
                                    $attendence->status === 'present' ? 'success' : 
                                    ($attendence->status === 'absent' ? 'danger' : 'warning') 
                                }}">
                                    {{ ucfirst($attendence->status) }}
                                </span>
                            </td>
                            <td class="px-4">{{ $attendence->check_in ?? '—' }}</td>
                            <td class="px-4">{{ $attendence->check_out ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="fas fa-database me-2"></i>No records found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- @if($attendences->hasPages())
            <div class="mt-4">
                {{ $attendences->links() }}
            </div>
            @endif --}}
        </div>
    </div>
</div>


<script>
  $(document).ready(function() {
    $('#exportBtn').click(function(e) {
        e.preventDefault();
        
        // Show loading state
        const btn = $(this);
        btn.prop('disabled', true);
        btn.html('<i class="fas fa-spinner fa-spin me-2"></i>Generating...');

        // Get current filter values
        const filters = {
            date_from: $('input[name="date_from"]').val(),
            date_to: $('input[name="date_to"]').val(),
            search: $('input[name="search"]').val(),
            _token: '{{ csrf_token() }}'
        };

        // Create hidden form
        const form = $('<form>', {
            method: 'GET',
            action: '{{ route("attendance.export") }}',
            style: 'display: none;'
        });

        // Add filter parameters
        $.each(filters, function(key, value) {
            form.append($('<input>', {
                type: 'hidden',
                name: key,
                value: value
            }));
        });

        // Submit form
        $('body').append(form);
        form.submit();
        
        // Cleanup and restore button
        setTimeout(() => {
            form.remove();
            btn.prop('disabled', false);
            btn.html('<i class="fas fa-download me-2"></i>Download CSV');
        }, 3000);
    });
});
</script>
@endsection

@push('styles')
<style>
    .card-header {
        border-radius: 0.5rem 0.5rem 0 0 !important;
    }
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
        transition: background-color 0.2s ease;
    }
    .cursor-pointer {
        cursor: pointer;
    }
    .form-control-sm {
        height: calc(1.5em + 0.75rem + 2px);
    }
</style>

@endpush