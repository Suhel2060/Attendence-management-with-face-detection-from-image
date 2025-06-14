<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendence extends Model
{
    use HasFactory;
    protected $table="attendances";
    protected $fillable=["employee_id"];




    public function User(){
        return $this->belongsTo(User::class,'employee_id','employee_id');
    }
}
