<?php
// app/Repositories/Contracts/DashboardRepositoryInterface.php
namespace App\Repositories\Contracts;

interface DashboardRepositoryInterface
{
    public function getAdminDashboardStats();
    public function getUserDashboardStats($userId);
    public function getLoanAnalytics($period = 'monthly');
    public function getRevenueAnalytics($period = 'monthly');
    public function getUserAnalytics($period = 'monthly');
    public function getRecentActivities($limit = 10);
    public function getTopPerformingLoans($limit = 10);
    public function getDefaultRiskAnalysis();
}