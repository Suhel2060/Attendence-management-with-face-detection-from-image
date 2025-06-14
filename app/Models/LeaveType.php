<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name', 'max_days'
    ];

    public function leaves()
    {
        return $this->hasMany(Leaves::class, 'leave_type_id');
    }
}
