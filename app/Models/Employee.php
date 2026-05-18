<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone',
        'department', 'position', 'hire_date', 'salary', 'avatar',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'salary'    => 'decimal:2',
    ];

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    // scopes pour la recherche
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('first_name', 'like', "%{$term}%")
              ->orWhere('last_name', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%");
        });
    }

    public function scopeByDepartment($query, string $department)
    {
        return $query->where('department', $department);
    }

    public function scopeByPosition($query, string $position)
    {
        return $query->where('position', $position);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    // Relations
    public function leaveBalances()
    {
        return $this->hasMany(LeaveBalance::class);
    }

    public function congeRequests()
    {
        return $this->hasMany(CongeRequest::class);
    }

    public function leaveBalance(int $year)
    {
        return $this->leaveBalances()->where('year', $year)->first();
    }
}