<?php
// routes/api.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\User\AuthController as UserAuthController;
use App\Http\Controllers\Api\V1\User\LoanController as UserLoanController;
use App\Http\Controllers\Api\V1\User\ProfileController as UserProfileController;
use App\Http\Controllers\Api\V1\User\DashboardController as UserDashboardController;
use App\Http\Controllers\Api\V1\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Api\V1\Admin\LoanController as AdminLoanController;
use App\Http\Controllers\Api\V1\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\V1\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\V1\Admin\NotificationApiController as AdminNotificationApiController;
use App\Http\Controllers\Api\V1\Admin\PermissionApiController as AdminPermissionApiController;
use App\Http\Controllers\Api\V1\Admin\ConfigApiController as AdminConfigApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    
    // API Status
    Route::get('/', function () {
        return response()->json([
            'success' => true,
            'message' => 'Loan Management System API',
            'version' => 'v1',
            'status' => 'operational',
            'timestamp' => now()->toISOString(),
        ]);
    });
    
    // ========== USER ROUTES ==========
    Route::prefix('user')->group(function () {
        // Authentication
        Route::post('/register', [UserAuthController::class, 'register']);
        Route::post('/login', [UserAuthController::class, 'login']);
        // Protected Routes
        Route::middleware(['auth:sanctum'])->group(function () {
            // Auth
            Route::post('/logout', [UserAuthController::class, 'logout']);
            Route::post('/refresh', [UserAuthController::class, 'refresh']);
            // Route::post('/change-password', [UserAuthController::class, 'changePassword']);
            
            // Profile
            Route::prefix('profile')->group(function () {
                Route::get('/', [UserAuthController::class, 'profile']);
                Route::put('/contact', [UserProfileController::class, 'updateContact']);
                Route::put('/employment', [UserProfileController::class, 'updateEmployment']);
            });
            
            // Loans
            Route::prefix('loans')->group(function () {
                Route::get('/', [UserLoanController::class, 'index']);
                Route::get('/loan_cal/{loan_amount?}/{loan_period}', [UserLoanController::class, 'calculateLoan']);
                Route::post('/', [UserLoanController::class, 'store']);
                Route::get('/{id}', [UserLoanController::class, 'show']);
                Route::post('/{id}/repay', [UserLoanController::class, 'makeRepayment']); //for payment
                // Route::post('/{id}/cancel', [UserLoanController::class, 'cancel']); 
                Route::get('/{id}/schedule', [UserLoanController::class, 'repaymentSchedule']); // get schedule of loan payments
            });
            
            // Dashboard
            Route::prefix('dashboard')->group(function () {
                Route::get('/', [UserDashboardController::class, 'index']);
                Route::get('/statistics', [UserDashboardController::class, 'statistics']);
                Route::get('/activities', [UserDashboardController::class, 'activities']);
            });
        });
    });
    
    // ========== ADMIN ROUTES ==========
    Route::prefix('admin')->group(function () {
        // Development-only public dashboard endpoint (for local testing)
        if (app()->environment('local') || config('app.debug')) {
            Route::get('/dashboard-public', [AdminDashboardController::class, 'index']);
        }
        // Authentication
        Route::post('/login', [AdminAuthController::class, 'login']);
        
        // Protected Routes  for Operations
        Route::middleware(['auth:admin', 'admin.type:admin'])->group(function () {
            // Auth  
            Route::post('/register', [AdminAuthController::class, 'register']);
            Route::post('/logout', [AdminAuthController::class, 'logout']);
            Route::post('/refresh', [AdminAuthController::class, 'refresh']);
            Route::get('/profile', [AdminAuthController::class, 'profile']);
            Route::put('/profile', [AdminAuthController::class, 'update']);
            Route::post('/change-password', [AdminAuthController::class, 'changePassword']);

            // Dashboard
            Route::prefix('dashboard')->group(function () {
                Route::get('/', [AdminDashboardController::class, 'index']);
                Route::get('/statistics', [AdminDashboardController::class, 'statistics']);
                Route::get('/loan-analytics', [AdminDashboardController::class, 'loanAnalytics']);
                Route::get('/revenue-analytics', [AdminDashboardController::class, 'revenueAnalytics']);
                Route::get('/user-analytics', [AdminDashboardController::class, 'userAnalytics']);
                Route::get('/performance-metrics', [AdminDashboardController::class, 'performanceMetrics']);
                Route::get('/risk-analysis', [AdminDashboardController::class, 'riskAnalysis']);
                Route::get('/monthly-report', [AdminDashboardController::class, 'monthlyReport']);
                Route::get('/recent-activities', [AdminDashboardController::class, 'recentActivities']);
            });
            
            // Users Management
            Route::prefix('users')->group(function () {
                Route::get('/', [AdminUserController::class, 'index']);
                Route::get('/with-loans', [AdminUserController::class, 'withLoans']);
                Route::get('/search', [AdminUserController::class, 'search']);
                Route::get('/{id}', [AdminUserController::class, 'show']);
                Route::put('/{id}', [AdminUserController::class, 'update']);
                Route::delete('/{id}', [AdminUserController::class, 'destroy']);
                Route::put('/{id}/status', [AdminUserController::class, 'updateStatus']);
                Route::get('/{id}/statistics', [AdminUserController::class, 'statistics']);
            });
            
            // Loans Management
            Route::prefix('loans')->group(function () {
                Route::get('/', [AdminLoanController::class, 'index']);
                Route::get('/filter', [AdminLoanController::class, 'filter']);
                Route::get('/overdue', [AdminLoanController::class, 'overdue']);
                Route::get('/active', [AdminLoanController::class, 'active']);
                Route::get('/statistics', [AdminLoanController::class, 'statistics']);
                Route::get('/{id}', [AdminLoanController::class, 'show']);
                Route::put('/{id}', [AdminLoanController::class, 'update']);
                Route::delete('/{id}', [AdminLoanController::class, 'destroy']);
                Route::post('/{id}/approve', [AdminLoanController::class, 'approve']);
                Route::post('/{id}/reject', [AdminLoanController::class, 'reject']);
                Route::post('/{id}/disburse', [AdminLoanController::class, 'disburse']);
                Route::post('/{id}/mark-defaulted', [AdminLoanController::class, 'markDefaulted']);
                Route::post('/{id}/add-late-fees', [AdminLoanController::class, 'addLateFees']);
            });
        });
        // Protected Routes
        Route::middleware(['auth:admin'])->group(function () {
            // Auth  
            Route::post('/register', [AdminAuthController::class, 'register']);
            Route::post('/logout', [AdminAuthController::class, 'logout']);
            Route::post('/refresh', [AdminAuthController::class, 'refresh']);
            Route::get('/profile', [AdminAuthController::class, 'profile']);
            Route::put('/profile', [AdminAuthController::class, 'update']);
            Route::post('/change-password', [AdminAuthController::class, 'changePassword']);

            // Dashboard
            Route::prefix('dashboard')->group(function () {
                Route::get('/', [AdminDashboardController::class, 'index']);
                Route::get('/statistics', [AdminDashboardController::class, 'statistics']);
                Route::get('/loan-analytics', [AdminDashboardController::class, 'loanAnalytics']);
                Route::get('/revenue-analytics', [AdminDashboardController::class, 'revenueAnalytics']);
                Route::get('/user-analytics', [AdminDashboardController::class, 'userAnalytics']);
                Route::get('/performance-metrics', [AdminDashboardController::class, 'performanceMetrics']);
                Route::get('/risk-analysis', [AdminDashboardController::class, 'riskAnalysis']);
                Route::get('/monthly-report', [AdminDashboardController::class, 'monthlyReport']);
                Route::get('/recent-activities', [AdminDashboardController::class, 'recentActivities']);
            });

            // Notifications
            Route::get('/notifications', [AdminNotificationApiController::class, 'index']);

            // Permissions
            Route::get('/permissions', [AdminPermissionApiController::class, 'index']);

            // Config
            Route::get('/config', [AdminConfigApiController::class, 'index']);
            
            // Users Management
            Route::prefix('users')->group(function () {
                Route::get('/', [AdminUserController::class, 'index']);
                Route::get('/with-loans', [AdminUserController::class, 'withLoans']);
                Route::get('/search', [AdminUserController::class, 'search']);
                Route::get('/{id}', [AdminUserController::class, 'show']);
                Route::put('/{id}', [AdminUserController::class, 'update']);
                Route::delete('/{id}', [AdminUserController::class, 'destroy']);
                Route::put('/{id}/status', [AdminUserController::class, 'updateStatus']);
                Route::get('/{id}/statistics', [AdminUserController::class, 'statistics']);
            });
            
            // Loans Management
            Route::prefix('loans')->group(function () {
                Route::get('/', [AdminLoanController::class, 'index']);
                Route::get('/filter', [AdminLoanController::class, 'filter']);
                Route::get('/overdue', [AdminLoanController::class, 'overdue']);
                Route::get('/active', [AdminLoanController::class, 'active']);
                Route::get('/statistics', [AdminLoanController::class, 'statistics']);
                Route::get('/{id}', [AdminLoanController::class, 'show']);
                Route::put('/{id}', [AdminLoanController::class, 'update']);
                Route::delete('/{id}', [AdminLoanController::class, 'destroy']);
                Route::post('/{id}/approve', [AdminLoanController::class, 'approve']);
                Route::post('/{id}/reject', [AdminLoanController::class, 'reject']);
                Route::post('/{id}/disburse', [AdminLoanController::class, 'disburse']);
                Route::post('/{id}/mark-defaulted', [AdminLoanController::class, 'markDefaulted']);
                Route::post('/{id}/add-late-fees', [AdminLoanController::class, 'addLateFees']);
            });
        });
    });
});