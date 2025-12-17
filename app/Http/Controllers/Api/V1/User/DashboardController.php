<?php
// app/Http/Controllers/Api/V1/User/DashboardController.php
namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Api\V1\BaseController;
use App\Services\V1\User\LoanService;
use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    protected $loanService;

    public function __construct(LoanService $loanService)
    {
        $this->loanService = $loanService;
    }

    /**
     * Get dashboard overview
     */
    public function index(Request $request)
    {
        try {
            $stats = $this->loanService->getUserLoanStats($request->user()->id);
            $upcomingPayments = $this->loanService->getUpcomingPayments($request->user()->id, 7);
            
            return $this->success([
                'stats' => $stats,
                'upcoming_payments' => $upcomingPayments,
            ], 'Dashboard data retrieved successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Get dashboard statistics
     */
    public function statistics(Request $request)
    {
        try {
            $stats = $this->loanService->getUserLoanStats($request->user()->id);
            return $this->success($stats, 'Statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Get recent activities
     */
    public function activities(Request $request)
    {
        try {
            // Get recent loans
            $loans = $this->loanService->getUserLoans($request->user()->id, [
                'per_page' => 5,
            ]);
            
            return $this->success([
                'recent_loans' => $loans,
            ], 'Recent activities retrieved successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}