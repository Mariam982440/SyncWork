<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\CongeRequest;
use App\Services\LeaveService;
use App\Http\Requests\StoreCongeRequest;
use App\Http\Requests\RejectCongeRequest;
use Illuminate\Http\Request;

class CongeController extends Controller
{
    public function __construct(protected LeaveService $leaveService) {}

    /**
     * RH : liste toutes les demandes.
     * Employé : liste ses propres demandes.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole(['admin', 'rh'])) {
            $conges = CongeRequest::with('employee')
                ->latest()
                ->paginate(15);
        } else {
            $employee = Employee::where('email', $user->email)->firstOrFail();
            $conges   = $employee->congeRequests()->latest()->paginate(15);
        }

        return view('conges.index', compact('conges'));
    }

    public function create()
    {
        $user     = auth()->user();
        $employee = Employee::where('email', $user->email)->firstOrFail();
        $balance  = $employee->leaveBalance(now()->year);

        return view('conges.create', compact('employee', 'balance'));
    }

    public function store(StoreCongeRequest $request)
    {
        $user     = auth()->user();
        $employee = Employee::where('email', $user->email)->firstOrFail();

        $workingDays = $this->leaveService->countWorkingDays(
            $request->start_date,
            $request->end_date
        );

        // Vérifie carence + solde
        if ($reason = $this->leaveService->getBlockReason($employee, $workingDays)) {
            return back()->withErrors(['blocked' => $reason])->withInput();
        }

        CongeRequest::create([
            'employee_id'  => $employee->id,
            'start_date'   => $request->start_date,
            'end_date'     => $request->end_date,
            'working_days' => $workingDays,
            'reason'       => $request->reason,
            'status'       => 'pending',
        ]);

        return redirect()->route('conges.index')
            ->with('success', "Demande envoyée ({$workingDays} jour(s) ouvrable(s)).");
    }

    public function approve(CongeRequest $conge)
    {
        abort_unless(auth()->user()->hasRole(['admin', 'rh']), 403);
        abort_unless($conge->isPending(), 422, 'Cette demande a déjà été traitée.');

        $conge->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'decided_at'  => now(),
        ]);

        $this->leaveService->deductBalance($conge->employee, $conge->working_days);

        return back()->with('success', 'Demande approuvée.');
    }

    public function reject(RejectCongeRequest $request, CongeRequest $conge)
    {
        abort_unless(auth()->user()->hasRole(['admin', 'rh']), 403);
        abort_unless($conge->isPending(), 422, 'Cette demande a déjà été traitée.');

        $conge->update([
            'status'          => 'rejected',
            'rejected_reason' => $request->rejected_reason,
            'approved_by'     => auth()->id(),
            'decided_at'      => now(),
        ]);

        return back()->with('success', 'Demande refusée.');
    }

    public function cancel(CongeRequest $conge)
    {
        abort_unless($conge->isPending(), 422, 'Impossible d\'annuler une demande déjà traitée.');

        // Vérifie que c'est bien la demande de l'employé connecté
        $employee = Employee::where('email', auth()->user()->email)->firstOrFail();
        abort_unless($conge->employee_id === $employee->id, 403);

        if ($conge->isApproved()) {
            $this->leaveService->restoreBalance($employee, $conge->working_days);
        }

        $conge->delete();

        return back()->with('success', 'Demande annulée.');
    }
}