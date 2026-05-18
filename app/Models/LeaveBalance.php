<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveBalance extends Model
{
    protected $fillable = [
        'employee_id', 'year', 'accrued_days',
        'used_days', 'available_days', 'last_accrual_date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}