<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeCorrection extends Model
{
    protected $fillable = [
        'employee_id',
        'date',
        'type',
        'requested_time_in',
        'requested_time_out',
        'reason',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
    ];

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id', 'employee_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
