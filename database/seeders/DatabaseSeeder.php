<?php
// database/seeders/DatabaseSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Super Admin
        Admin::create([
            'full_name' => 'Super Admin123',
            'user_name' => 'Super Admin',
            'email' => 'superadmin@loanapp.com',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
            'phone' => '08012345678',
            // 'permissions' => json_encode(['*']),
            'is_active' => true,
        ]);

        // Create Admin
        Admin::create([
            'full_name' => 'System Admin123',
            'user_name' => 'System Admin',
            'email' => 'admin@loanapp.com',
            'password' => Hash::make('password123'),
            'role' => 'admins',
            'phone' => '08087654321',
            // 'permissions' => json_encode([
            //     'manage_loans',
            //     'manage_users',
            //     'view_dashboard',
            // ]),
            'is_active' => true,
        ]);

        // Create Loan Officer
        Admin::create([
            'full_name' => 'Loan Officer123',
            'user_name' => 'Loan Officer',
            'email' => 'officer@loanapp.com',
            'password' => Hash::make('password123'),
            'role' => 'loan_officer',
            'phone' => '08011223344',
            // 'permissions' => json_encode([
            //     'manage_loans',
            //     'view_dashboard',
            // ]),
            'is_active' => true,
        ]);

        $this->command->info('Default admin users created successfully!');
        $this->command->info('Super Admin: superadmin@loanapp.com / password123');
        $this->command->info('Admin: admin@loanapp.com / password123');
        $this->command->info('Loan Officer: officer@loanapp.com / password123');
    }
}