<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        User::factory()->create([
        'name'  => 'Admin syncWork',
        'email' => 'admin@syncwork.ma',
        'role'  => 'admin',
    ]);

        User::factory()->create([
            'name'  => 'Responsable RH',
            'email' => 'rh@syncwork.ma',
            'role'  => 'rh',
        ]);

        User::factory()->create([
            'name'  => 'Employé Test',
            'email' => 'employe@syncwork.ma',
            'role'  => 'employee',
        ]);
    }



}
