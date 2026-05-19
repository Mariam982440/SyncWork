<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\LeaveBalance;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AccrueLeaveBalances extends Command
{
    protected $signature   = 'leave:accrue';
    protected $description = 'Crédite +1.5 jour de congé à tous les employés éligibles (carence ≥ 6 mois)';

    public function handle(): void
    {
        $employees = Employee::whereNull('deleted_at')->get();
        $count = 0;

        foreach ($employees as $employee) {
            $monthsWorked = Carbon::parse($employee->hire_date)->diffInMonths(now());

            if ($monthsWorked < 6) {
                continue; // carence non écoulée
            }

            $balance = LeaveBalance::firstOrCreate(
                ['employee_id' => $employee->id, 'year' => now()->year],
                ['accrued_days' => 0, 'used_days' => 0, 'available_days' => 0]
            );

            $balance->accrued_days   += 1.5;
            $balance->available_days  = $balance->accrued_days - $balance->used_days;
            $balance->last_accrual_date = now()->toDateString();
            $balance->save();

            $count++;
        }

        $this->info("Soldes crédités pour {$count} employé(s).");
    }
}