<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

// Admin backend routes (server-rendered) — use /backend/* to avoid SPA /admin/* conflicts
Route::get('/backend/login', [App\Http\Controllers\Admin\LoginController::class, 'showLoginForm']);
Route::post('/backend/login', [App\Http\Controllers\Admin\LoginController::class, 'login']);
Route::post('/backend/logout', [App\Http\Controllers\Admin\LoginController::class, 'logout']);
Route::get('/backend/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index']);
Route::get('/backend/users', [App\Http\Controllers\Admin\UserManagementController::class, 'index']);
Route::get('/backend/loans', [App\Http\Controllers\Admin\LoanManagementController::class, 'index']);
Route::get('/backend/financial', [App\Http\Controllers\Admin\FinancialController::class, 'index']);
Route::get('/backend/notifications', [App\Http\Controllers\Admin\NotificationController::class, 'index']);
Route::get('/backend/permissions', [App\Http\Controllers\Admin\PermissionController::class, 'index']);
Route::get('/backend/config', [App\Http\Controllers\Admin\ConfigController::class, 'index']);

// Root health/status endpoint used by tests and browsers
Route::get('/', function () {
    return response('OK', 200);
});

Route::get('/admin/{any?}', function () {
    return File::get(public_path('admin/index.html'));
})->where('any', '.*');

// Local-only endpoint to seed test data for development & QA
if (app()->environment('local') || config('app.debug')) {
    Route::post('/backend/seed-test-data', function () {
        try {
            // Create sample users and loans
            $users = [];
            $faker = \Faker\Factory::create();
            for ($i = 0; $i < 3; $i++) {
                $user = \App\Models\User::firstOrCreate(
                    ['email' => "testuser{$i}@example.com"],
                    [
                        'full_name' => $faker->name,
                        'user_name' => 'testuser' . $i,
                        
                        'password' => \Illuminate\Support\Facades\Hash::make('password123'),
                    ]
                );
                $users[] = $user;
            }

            // Create sample loans for users
            foreach ($users as $idx => $user) {
                for ($j = 0; $j < 2; $j++) {
                    \App\Models\Loan::create([
                        'user_id' => $user->id,
                        'order_id' => 'SEED' . strtoupper(substr(uniqid(),0,8)),
                        'loan_amount' => rand(500, 5000),
                        'loan_period' => 12,
                        'principle' => rand(500,5000),
                        'interest_rate' => 5.5,
                        'interest_amount' => 100,
                        'total_payment' => 600,
                        'status' => ($j === 0) ? 'approved' : 'pending',
                        'disbursed_at' => ($j === 0) ? now() : null,
                    ]);
                }
            }

            return response()->json(['message' => 'Seeded sample users and loans'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    });
    // also allow GET for quick developer access (no CSRF required for GET)
    Route::get('/backend/seed-test-data', function () {
        try {
            $users = [];
            $faker = \Faker\Factory::create();
            for ($i = 0; $i < 3; $i++) {
                $user = \App\Models\User::firstOrCreate(
                    ['email' => "testuser{$i}@example.com"],
                    [
                        'full_name' => $faker->name,
                        'user_name' => 'testuser' . $i,
                        
                        'password' => \Illuminate\Support\Facades\Hash::make('password123'),
                    ]
                );
                $users[] = $user;
            }

            foreach ($users as $idx => $user) {
                for ($j = 0; $j < 2; $j++) {
                    \App\Models\Loan::create([
                        'user_id' => $user->id,
                        'order_id' => 'SEED' . strtoupper(substr(uniqid(),0,8)),
                        'loan_amount' => rand(500, 5000),
                        'loan_period' => 12,
                        'principle' => rand(500,5000),
                        'interest_rate' => 5.5,
                        'interest_amount' => 100,
                        'total_payment' => 600,
                        'status' => ($j === 0) ? 'approved' : 'pending',
                        'disbursed_at' => ($j === 0) ? now() : null,
                    ]);
                }
            }

            return response()->json(['message' => 'Seeded sample users and loans (GET)'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    });
}
