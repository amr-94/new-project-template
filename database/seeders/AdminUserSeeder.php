<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // check if the admin role exists, if not create it
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // if not, create a default admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('password123'),
            ]
        );

        // assign the admin role to the user if not already assigned
        if (!$admin->hasRole('admin')) {
            $admin->assignRole($adminRole);
        }
    }
}