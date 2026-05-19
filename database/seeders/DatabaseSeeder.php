<?php

namespace Database\Seeders;

use App\Models\CongeRequest;
use App\Models\Employee;
use App\Models\LeaveBalance;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $sara = $this->seedPerson(
            [
                'name' => 'Sara El Amrani',
                'email' => 'sara@syncwork.ma',
                'password' => 'sara123',
                'role' => 'admin',
            ],
            [
                'first_name' => 'Sara',
                'last_name' => 'El Amrani',
                'phone' => '0600000001',
                'department' => 'Direction',
                'position' => 'Administratrice SI',
                'hire_date' => '2020-02-03',
                'salary' => 18000,
            ]
        );

        $mariam = $this->seedPerson(
            [
                'name' => 'Mariam Benali',
                'email' => 'mariam@syncwork.ma',
                'password' => 'mariam123',
                'role' => 'rh',
            ],
            [
                'first_name' => 'Mariam',
                'last_name' => 'Benali',
                'phone' => '0600000002',
                'department' => 'Ressources Humaines',
                'position' => 'Responsable RH',
                'hire_date' => '2021-06-14',
                'salary' => 14500,
            ]
        );

        $this->seedPerson(
            [
                'name' => 'Nadia Alaoui',
                'email' => 'nadia@syncwork.ma',
                'password' => 'nadia123',
                'role' => 'employee',
            ],
            [
                'first_name' => 'Nadia',
                'last_name' => 'Alaoui',
                'phone' => '0600000003',
                'department' => 'Finance',
                'position' => 'Comptable',
                'hire_date' => '2023-09-01',
                'salary' => 9200,
            ],
            ['accrued_days' => 14, 'used_days' => 4, 'available_days' => 10]
        );

        $ali = $this->seedAli(
            [
                'name' => 'Ali Mansouri',
                'email' => 'ali@sync.ma',
                'password' => 'ali12345',
                'role' => 'employee',
            ],
            [
                'first_name' => 'Ali',
                'last_name' => 'Mansouri',
                'phone' => '0612345678',
                'department' => 'Production',
                'position' => 'Chef d equipe',
                'hire_date' => '2018-03-12',
                'salary' => 12500,
            ],
            ['accrued_days' => 28, 'used_days' => 12, 'available_days' => 16]
        );

        $this->seedAliCongeHistory($ali, $mariam->id);

        $this->seedPerson(
            [
                'name' => 'Yassine Berrada',
                'email' => 'yassine.admin@sync.ma',
                'password' => 'password12345',
                'role' => 'admin',
            ],
            [
                'first_name' => 'Yassine',
                'last_name' => 'Berrada',
                'phone' => '0622222222',
                'department' => 'Direction Generale',
                'position' => 'Directeur Operations',
                'hire_date' => '2017-11-20',
                'salary' => 22000,
            ],
            ['accrued_days' => 30, 'used_days' => 6, 'available_days' => 24]
        );

        $this->seedPerson(
            [
                'name' => 'Leila Rami',
                'email' => 'leila.rh@sync.ma',
                'password' => 'password12345',
                'role' => 'rh',
            ],
            [
                'first_name' => 'Leila',
                'last_name' => 'Rami',
                'phone' => '0633333333',
                'department' => 'Ressources Humaines',
                'position' => 'Chargee de recrutement',
                'hire_date' => '2022-04-05',
                'salary' => 11000,
            ],
            ['accrued_days' => 22, 'used_days' => 8, 'available_days' => 14]
        );

        $this->seedPerson(
            [
                'name' => 'Karim Zahraoui',
                'email' => 'karim@sync.ma',
                'password' => 'password12345',
                'role' => 'employee',
            ],
            [
                'first_name' => 'Karim',
                'last_name' => 'Zahraoui',
                'phone' => '0644444444',
                'department' => 'Logistique',
                'position' => 'Coordinateur logistique',
                'hire_date' => '2024-01-15',
                'salary' => 8500,
            ],
            ['accrued_days' => 12, 'used_days' => 2, 'available_days' => 10]
        );

        $this->seedPerson(
            [
                'name' => 'Imane Tazi',
                'email' => 'imane@sync.ma',
                'password' => 'password12345',
                'role' => 'employee',
            ],
            [
                'first_name' => 'Imane',
                'last_name' => 'Tazi',
                'phone' => '0655555555',
                'department' => 'Marketing',
                'position' => 'Specialiste communication',
                'hire_date' => '2025-02-10',
                'salary' => 7800,
            ],
            ['accrued_days' => 8, 'used_days' => 0, 'available_days' => 8]
        );
    }

    private function seedPerson(array $userData, array $employeeData, array $balanceData = []): Employee
    {
        User::updateOrCreate(
            ['email' => $userData['email']],
            [
                'name' => $userData['name'],
                'password' => Hash::make($userData['password']),
                'role' => $userData['role'],
            ]
        );

        $employee = Employee::updateOrCreate(
            ['email' => $userData['email']],
            $employeeData
        );

        LeaveBalance::updateOrCreate(
            ['employee_id' => $employee->id, 'year' => now()->year],
            array_merge(
                [
                    'accrued_days' => 0,
                    'used_days' => 0,
                    'available_days' => 0,
                    'last_accrual_date' => now()->toDateString(),
                ],
                $balanceData
            )
        );

        return $employee;
    }

    private function seedAli(array $userData, array $employeeData, array $balanceData = []): Employee
    {
        $user = User::where('email', $userData['email'])
            ->orWhereRaw('lower(name) = ?', ['ali'])
            ->first();

        if ($user) {
            $user->update([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
                'role' => $userData['role'],
            ]);
        } else {
            User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
                'role' => $userData['role'],
            ]);
        }

        $employee = Employee::where('email', $userData['email'])
            ->orWhereRaw('lower(first_name) = ?', ['ali'])
            ->first();

        if ($employee) {
            $employee->update(array_merge($employeeData, ['email' => $userData['email']]));
        } else {
            $employee = Employee::create(array_merge($employeeData, ['email' => $userData['email']]));
        }

        LeaveBalance::updateOrCreate(
            ['employee_id' => $employee->id, 'year' => now()->year],
            array_merge(
                [
                    'accrued_days' => 0,
                    'used_days' => 0,
                    'available_days' => 0,
                    'last_accrual_date' => now()->toDateString(),
                ],
                $balanceData
            )
        );

        return $employee;
    }

    private function seedAliCongeHistory(Employee $ali, int $approvedBy): void
    {
        $conges = [
            [
                'start_date' => '2025-08-04',
                'end_date' => '2025-08-15',
                'working_days' => 10,
                'reason' => 'Conge annuel ete',
                'status' => 'approved',
                'approved_by' => $approvedBy,
                'decided_at' => '2025-07-10 10:30:00',
            ],
            [
                'start_date' => '2026-01-12',
                'end_date' => '2026-01-16',
                'working_days' => 5,
                'reason' => 'Repos familial',
                'status' => 'approved',
                'approved_by' => $approvedBy,
                'decided_at' => '2025-12-20 09:15:00',
            ],
            [
                'start_date' => '2026-03-09',
                'end_date' => '2026-03-11',
                'working_days' => 3,
                'reason' => 'Demarches administratives',
                'status' => 'rejected',
                'rejected_reason' => 'Periode chargee en production.',
                'approved_by' => $approvedBy,
                'decided_at' => '2026-02-28 14:40:00',
            ],
            [
                'start_date' => '2026-06-08',
                'end_date' => '2026-06-10',
                'working_days' => 3,
                'reason' => 'Conge personnel',
                'status' => 'pending',
            ],
        ];

        foreach ($conges as $conge) {
            CongeRequest::updateOrCreate(
                [
                    'employee_id' => $ali->id,
                    'start_date' => $conge['start_date'],
                    'end_date' => $conge['end_date'],
                ],
                array_merge(['employee_id' => $ali->id], $conge)
            );
        }
    }
}
