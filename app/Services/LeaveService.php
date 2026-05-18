<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\LeaveBalance;
use App\Models\CongeRequest;
use Carbon\Carbon;

class LeaveService
{
    /**
     * Vérifie si l'employé a passé la période de carence (6 mois).
     */
    public function canRequestLeave(Employee $employee): bool
    {
        $eligibleFrom = Carbon::parse($employee->hire_date)->addMonths(6);
        return Carbon::now()->gte($eligibleFrom);
    }

    /**
     * Vérifie si le solde disponible couvre les jours demandés.
     */
    public function hasEnoughBalance(Employee $employee, int $requestedDays): bool
    {
        $balance = $employee->leaveBalance(now()->year);
        return $balance && $balance->available_days >= $requestedDays;
    }

    /**
     * Compte les jours ouvrables (lundi–vendredi) entre deux dates incluses.
     */
    public function countWorkingDays(string $startDate, string $endDate): int
    {
        $start = Carbon::parse($startDate);
        $end   = Carbon::parse($endDate);
        $days  = 0;

        while ($start->lte($end)) {
            if ($start->isWeekday()) {
                $days++;
            }
            $start->addDay();
        }

        return $days;
    }

    /**
     * Décrémente le solde après approbation.
     */
    public function deductBalance(Employee $employee, int $days): void
    {
        $balance = $employee->leaveBalance(now()->year);

        if ($balance) {
            $balance->increment('used_days', $days);
            $balance->decrement('available_days', $days);
        }
    }

    /**
     * Restitue le solde en cas de refus ou annulation.
     */
    public function restoreBalance(Employee $employee, int $days): void
    {
        $balance = $employee->leaveBalance(now()->year);

        if ($balance) {
            $balance->decrement('used_days', $days);
            $balance->increment('available_days', $days);
        }
    }

    /**
     * Retourne un message d'erreur lisible si la demande est bloquée.
     */
    public function getBlockReason(Employee $employee, int $requestedDays): ?string
    {
        if (!$this->canRequestLeave($employee)) {
            $eligibleFrom = Carbon::parse($employee->hire_date)->addMonths(6);
            return "Vous ne pouvez pas encore poser de congé. Éligible à partir du {$eligibleFrom->format('d/m/Y')}.";
        }

        if (!$this->hasEnoughBalance($employee, $requestedDays)) {
            $balance = $employee->leaveBalance(now()->year);
            $available = $balance ? $balance->available_days : 0;
            return "Solde insuffisant. Vous avez {$available} jour(s) disponible(s), {$requestedDays} demandé(s).";
        }

        return null;
    }
}