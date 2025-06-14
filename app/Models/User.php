<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        // Automatically generate employee_id when creating a new employee
        static::creating(function ($employee) {
            $employee->employee_id = static::generateEmployeeId();
        });
    }

    /**
     * Generate the Employee ID in the format EID-XXX
     *
     * @return string
     */
    private static function generateEmployeeId()
    {
        // Get the last employee ID
        $lastEmployee = static::orderBy('id', 'desc')->first();

        // Get the last number and increment it
        $lastEmployeeId = $lastEmployee ? (int) substr($lastEmployee->employee_id, 4) : 0;
        $newEmployeeId = $lastEmployeeId + 1;

        // Format the new employee ID as EID-XXX (e.g., EID-001, EID-002, etc.)
        return 'EID-' . str_pad($newEmployeeId, 3, '0', STR_PAD_LEFT);
    }


    public function Attendence(){
        return $this->hasMany(Attendence::class,'employee_id','employee_id');
    }



    public function leaves()
{
    return $this->hasMany(Leaves::class, 'employee_id', 'employee_id');
}

public function pendingLeaves()
{
    return $this->leaves()->where('status', Leaves::STATUS_PENDING);
}

public function approvedLeaves()
{
    return $this->leaves()->where('status', Leaves::STATUS_APPROVED);
}

}
