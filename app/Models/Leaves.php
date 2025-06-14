<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leaves extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'employee_id', 'leave_type_id', 'start_date', 'end_date', 
        'total_days', 'reason', 'status'  // Added status
    ];

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id','employee_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }
}