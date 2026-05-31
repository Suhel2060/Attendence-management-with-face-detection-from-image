<span class="hrc-badge {{ $correction->status }}">
    <i class="bi {{ $correction->status === 'approved' ? 'bi-check-circle' : ($correction->status === 'rejected' ? 'bi-x-circle' : 'bi-hourglass-split') }}"></i>
    {{ ucfirst($correction->status) }}
</span>
@if($correction->status !== 'pending' && $correction->reviewer)
    <div style="font-size:0.72rem;color:var(--hrc-gray-600);margin-top:2px;">
        by {{ $correction->reviewer->name }}
        @if($correction->reviewed_at)
            · {{ \Carbon\Carbon::parse($correction->reviewed_at)->format('M d, h:i A') }}
        @endif
    </div>
    @if($correction->review_notes)
        <small class="text-muted d-block" style="font-size:0.72rem;">
            <i class="bi bi-chat"></i> {{ $correction->review_notes }}
        </small>
    @endif
@endif
