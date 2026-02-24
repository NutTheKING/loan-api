<?php
// database/seeders/DatabaseSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Models\Role;
use App\Models\Module;
use App\Models\RoleModule;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Step 1: Create Roles
        $roles = [
            [
                'name' => 'Super Admin',
                'key' => 'super_admin'
            ],
            [
                'name' => 'Admin',
                'key' => 'admin'
            ],
            [
                'name' => 'Loan Opperator',
                'key' => 'loan_opperator'
            ]
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(['key' => $roleData['key']], $roleData);
        }

        $this->command->info('Roles created successfully!');

        // Step 2: Create Modules
        $modules = [
            ['name' => 'Loan Management', 'key' => 'loan_management', 'order_sequent' => 1],
            ['name' => 'All Members', 'key' => 'all_members', 'order_sequent' => 2],
            ['name' => 'Risk Control', 'key' => 'risk_control', 'order_sequent' => 3],
            ['name' => 'Financial Statistics', 'key' => 'financial_statistics', 'order_sequent' => 4],
            ['name' => 'Dashboard', 'key' => 'dashboard', 'order_sequent' => 5],
            ['name' => 'User Management', 'key' => 'user_management', 'order_sequent' => 6],
            ['name' => 'Role & Permission', 'key' => 'role_permission', 'order_sequent' => 7],
            ['name' => 'Loan Config', 'key' => 'loan_config', 'order_sequent' => 8],
            ['name' => 'Reports', 'key' => 'reports', 'order_sequent' => 9],
            ['name' => 'Audit Logs', 'key' => 'audit_logs', 'order_sequent' => 10],
        ];

        foreach ($modules as $moduleData) {
            Module::firstOrCreate(['key' => $moduleData['key']], $moduleData);
        }

        $this->command->info('Modules created successfully!');

        // Step 3: Assign all modules to Super Admin role
        $superAdminRole = Role::where('key', 'super_admin')->first();
        $allModules = Module::all();

        foreach ($allModules as $module) {
            RoleModule::firstOrCreate([
                'role_id' => $superAdminRole->id,
                'module_id' => $module->id
            ]);
        }

        $this->command->info('All modules assigned to Super Admin role!');

        // Step 4: Create Admin Users with role_id instead of role string
        $superAdmin = Admin::firstOrCreate(
            ['email' => 'superadmin@loanapp.com'],
            [
                'full_name' => 'Super Admin123',
                'user_name' => 'Super Admin',
                'password' => Hash::make('password123'),
                'role_id' => Role::where('key', 'super_admin')->first()->id,
                'phone' => '08012345678',
                'is_active' => true,
            ]
        );

        $admin = Admin::firstOrCreate(
            ['email' => 'admin@loanapp.com'],
            [
                'full_name' => 'System Admin123',
                'user_name' => 'System Admin',
                'password' => Hash::make('password123'),
                'role_id' => Role::where('key', 'admin')->first()->id,
                'phone' => '08087654321',
                'is_active' => true,
            ]
        );

        $loanOfficer = Admin::firstOrCreate(
            ['email' => 'officer@loanapp.com'],
            [
                'full_name' => 'Loan Operator123',
                'user_name' => 'Loan Operator',
                'password' => Hash::make('password123'),
                'role_id' => Role::where('key', 'loan_opperator')->first()->id,
                'phone' => '08011223344',
                'is_active' => true,
            ]
        );

        $this->command->info('Default admin users created successfully!');
        $this->command->info('Super Admin: superadmin@loanapp.com / password123');
        $this->command->info('Admin: admin@loanapp.com / password123');
        $this->command->info('Loan Officer: officer@loanapp.com / password123');
    }
}