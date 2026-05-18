<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::query();

        if ($search = $request->input('search')) {
            $query->search($search);
        }

        if ($department = $request->input('department')) {
            $query->byDepartment($department);
        }

        if ($position = $request->input('position')) {
            $query->byPosition($position);
        }

        $employees   = $query->latest()->paginate(10)->withQueryString();
        $departments = Employee::distinct()->pluck('department');
        $positions   = Employee::distinct()->pluck('position');

        return view('employees.index', compact('employees', 'departments', 'positions'));
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(StoreEmployeeRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        Employee::create($data);

        return redirect()->route('employees.index')
            ->with('success', 'Employé créé avec succès.');
    }

    public function show(Employee $employee)
    {
        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        $data = $request->validated();

        if ($request->hasFile('avatar')) {
            if ($employee->avatar) {
                Storage::disk('public')->delete($employee->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $employee->update($data);

        return redirect()->route('employees.show', $employee)
            ->with('success', 'Employé mis à jour.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete(); // soft delete

        return redirect()->route('employees.index')
            ->with('success', 'Employé archivé.');
    }
}