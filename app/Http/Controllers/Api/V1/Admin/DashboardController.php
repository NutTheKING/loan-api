<?php
// app/Http/Controllers/Api/V1/Admin/DashboardController.php
namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\BaseController;
use App\Services\V1\Admin\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Get dashboard overview
     */
    public function index(Request $request)
    {
        try {
            $stats = $this->dashboardService->getDashboardStats();
            $recentActivities = $this->dashboardService->getRecentActivities(10);
            $topLoans = $this->dashboardService->getTopPerformingLoans(5);
            
            return $this->success([
                'stats' => $stats,
                'recent_activities' => $recentActivities,
                'top_performing_loans' => $topLoans,
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
            $stats = $this->dashboardService->getDashboardStats();
            return $this->success($stats, 'Dashboard statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Get loan analytics
     */
    public function loanAnalytics(Request $request)
    {
        try {
            $analytics = $this->dashboardService->getLoanAnalytics(
                $request->input('period', 'monthly')
            );
            return $this->success($analytics, 'Loan analytics retrieved successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Get revenue analytics
     */
    public function revenueAnalytics(Request $request)
    {
        try {
            $analytics = $this->dashboardService->getRevenueAnalytics(
                $request->input('period', 'monthly')
            );
            return $this->success($analytics, 'Revenue analytics retrieved successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Get user analytics
     */
    public function userAnalytics(Request $request)
    {
        try {
            $analytics = $this->dashboardService->getUserAnalytics(
                $request->input('period', 'monthly')
            );
            return $this->success($analytics, 'User analytics retrieved successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Get performance metrics
     */
    public function performanceMetrics(Request $request)
    {
        try {
            $metrics = $this->dashboardService->getPerformanceMetrics();
            return $this->success($metrics, 'Performance metrics retrieved successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Get default risk analysis
     */
    public function riskAnalysis(Request $request)
    {
        try {
            $analysis = $this->dashboardService->getDefaultRiskAnalysis();
            return $this->success($analysis, 'Risk analysis retrieved successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Get monthly report
     */
    public function monthlyReport(Request $request)
    {
        try {
            $request->validate([
                'year' => 'required|integer',
                'month' => 'required|integer|between:1,12',
            ]);

            $report = $this->dashboardService->getMonthlyReport(
                $request->year,
                $request->month
            );
            
            return $this->success($report, 'Monthly report retrieved successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationError($e->errors());
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Get recent activities
     */
    public function recentActivities(Request $request)
    {
        try {
            $activities = $this->dashboardService->getRecentActivities(
                $request->input('limit', 10)
            );
            return $this->success($activities, 'Recent activities retrieved successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}