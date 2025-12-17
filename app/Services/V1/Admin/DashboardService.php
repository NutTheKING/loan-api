<?php
// app/Services/V1/Admin/DashboardService.php
namespace App\Services\V1\Admin;

use App\Repositories\Contracts\DashboardRepositoryInterface;
use App\Repositories\Contracts\LoanRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;

class DashboardService
{
    protected $dashboardRepository;
    protected $loanRepository;
    protected $userRepository;

    public function __construct(
        DashboardRepositoryInterface $dashboardRepository,
        LoanRepositoryInterface $loanRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->dashboardRepository = $dashboardRepository;
        $this->loanRepository = $loanRepository;
        $this->userRepository = $userRepository;
    }

    public function getDashboardStats()
    {
        return $this->dashboardRepository->getAdminDashboardStats();
    }

    public function getLoanAnalytics($period = 'monthly')
    {
        return $this->dashboardRepository->getLoanAnalytics($period);
    }

    public function getRevenueAnalytics($period = 'monthly')
    {
        return $this->dashboardRepository->getRevenueAnalytics($period);
    }

    public function getUserAnalytics($period = 'monthly')
    {
        return $this->dashboardRepository->getUserAnalytics($period);
    }

    public function getRecentActivities($limit = 10)
    {
        return $this->dashboardRepository->getRecentActivities($limit);
    }

    public function getTopPerformingLoans($limit = 10)
    {
        return $this->dashboardRepository->getTopPerformingLoans($limit);
    }

    public function getDefaultRiskAnalysis()
    {
        return $this->dashboardRepository->getDefaultRiskAnalysis();
    }

    public function getPerformanceMetrics()
    {
        $stats = $this->dashboardRepository->getAdminDashboardStats();
        
        $approvalRate = $stats['total_loans'] > 0 
            ? (($stats['approved_count'] ?? 0) / $stats['total_loans']) * 100 
            : 0;
        
        $defaultRate = $stats['total_loans'] > 0 
            ? (($stats['defaulted_count'] ?? 0) / $stats['total_loans']) * 100 
            : 0;
        
        $recoveryRate = $stats['total_loan_amount'] > 0 
            ? ($stats['total_revenue'] / $stats['total_loan_amount']) * 100 
            : 0;
        
        return [
            'approval_rate' => round($approvalRate, 2),
            'default_rate' => round($defaultRate, 2),
            'recovery_rate' => round($recoveryRate, 2),
            'average_loan_size' => $stats['total_loans'] > 0 
                ? $stats['total_loan_amount'] / $stats['total_loans'] 
                : 0,
            'active_users_percentage' => $stats['total_users'] > 0 
                ? ($this->userRepository->getUsersWithLoans(1)->count() / $stats['total_users']) * 100 
                : 0,
        ];
    }

    public function getMonthlyReport($year, $month)
    {
        $startDate = now()->setYear($year)->setMonth($month)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        
        return [
            'period' => $startDate->format('F Y'),
            'new_loans' => $this->loanRepository->filter([
                'date_from' => $startDate,
                'date_to' => $endDate,
            ])->total(),
            'new_users' => $this->userRepository->search('', 1)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'total_disbursed' => $this->loanRepository->filter([
                'date_from' => $startDate,
                'date_to' => $endDate,
                'status' => 'disbursed',
            ])->sum('amount'),
            'total_repaid' => $this->dashboardRepository->getRevenueAnalytics('monthly')
                ->where('period', $startDate->format('Y-m'))
                ->first()->total_revenue ?? 0,
            'defaulted_amount' => $this->loanRepository->filter([
                'date_from' => $startDate,
                'date_to' => $endDate,
                'status' => 'defaulted',
            ])->sum('remaining_balance'),
        ];
    }
}