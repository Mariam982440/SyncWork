<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\LeaveBalance;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class, 'unique:employees,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        [$firstName, $lastName] = $this->splitName($request->name);

        $user = DB::transaction(function () use ($request, $firstName, $lastName) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'employee',
            ]);

            $employee = Employee::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $request->email,
                'department' => 'Non affecte',
                'position' => 'Employe',
                'hire_date' => now()->toDateString(),
            ]);

            LeaveBalance::create([
                'employee_id' => $employee->id,
                'year' => now()->year,
                'accrued_days' => 0,
                'used_days' => 0,
                'available_days' => 0,
            ]);

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }

    private function splitName(string $name): array
    {
        $parts = preg_split('/\s+/', trim($name), 2);

        return [
            $parts[0] ?? 'Utilisateur',
            $parts[1] ?? '',
        ];
    }
}
