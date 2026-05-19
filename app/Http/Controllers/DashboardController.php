<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\CongeRequest;
use App\Models\LeaveBalance;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return match($user->role) {
            'admin'    => $this->adminDashboard(),
            'rh'       => $this->rhDashboard(),
            'employee' => $this->employeeDashboard($user),
        };
    }

    private function adminDashboard()
    {
        $stats = [
            'total_employees'  => Employee::count(),
            'pending_conges'   => CongeRequest::where('status', 'pending')->count(),
            'approved_conges'  => CongeRequest::where('status', 'approved')->count(),
            'archived'         => Employee::onlyTrashed()->count(),
        ];

        $by_department = Employee::whereNull('deleted_at')
            ->select('department', DB::raw('count(*) as total'))
            ->groupBy('department')
            ->orderByDesc('total')
            ->get();

        $recent_conges = CongeRequest::with('employee')
            ->latest()
            ->take(5)
            ->get();

        $recent_employees = Employee::latest()->take(5)->get();

        return view('dashboard.admin', compact(
            'stats', 'by_department', 'recent_conges', 'recent_employees'
        ));
    }

    private function rhDashboard()
    {
        $stats = [
            'total_employees' => Employee::count(),
            'pending_conges'  => CongeRequest::where('status', 'pending')->count(),
            'approved_conges' => CongeRequest::whereMonth('decided_at', now()->month)
                                    ->where('status', 'approved')->count(),
            'rejected_conges' => CongeRequest::whereMonth('decided_at', now()->month)
                                    ->where('status', 'rejected')->count(),
        ];

        $pending_conges = CongeRequest::with('employee')
            ->where('status', 'pending')
            ->latest()
            ->get();

        $by_department = Employee::whereNull('deleted_at')
            ->select('department', DB::raw('count(*) as total'))
            ->groupBy('department')
            ->get();

        $employees_by_department = Employee::whereNull('deleted_at')
            ->orderBy('department')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get()
            ->groupBy('department');

        return view('dashboard.rh', compact(
            'stats',
            'pending_conges',
            'by_department',
            'employees_by_department'
        ));
    }

    private function employeeDashboard($user)
    {
        $employee = Employee::firstOrCreate(
            ['email' => $user->email],
            [
                'first_name' => $this->firstNameFrom($user->name),
                'last_name' => $this->lastNameFrom($user->name),
                'department' => 'Non affecte',
                'position' => 'Employe',
                'hire_date' => now()->toDateString(),
            ]
        );

        $balance = LeaveBalance::firstOrCreate(
            ['employee_id' => $employee->id, 'year' => now()->year],
            ['accrued_days' => 0, 'used_days' => 0, 'available_days' => 0]
        );

        $conges = $employee->congeRequests()
            ->latest()
            ->take(5)
            ->get();

        $next_accrual = now()->startOfMonth()->addMonth()->format('d/m/Y');

        // Carence : date à partir de laquelle il peut poser
        $eligible_from = $employee->hire_date->addMonths(6);
        $is_eligible   = now()->gte($eligible_from);

        return view('dashboard.employee', compact(
            'employee', 'balance', 'conges', 'next_accrual',
            'eligible_from', 'is_eligible'
        ));
    }

    private function firstNameFrom(string $name): string
    {
        return preg_split('/\s+/', trim($name), 2)[0] ?? 'Utilisateur';
    }

    private function lastNameFrom(string $name): string
    {
        return preg_split('/\s+/', trim($name), 2)[1] ?? '';
    }
}
