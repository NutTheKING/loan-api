<?php
// app/Repositories/DashboardRepository.php
namespace App\Repositories;

use App\Models\Loan;
use App\Models\User;
use App\Models\Admin;
use App\Models\LoanRepayment;
use App\Repositories\Contracts\DashboardRepositoryInterface;
use Illuminate\Support\Facades\DB;

class DashboardRepository implements DashboardRepositoryInterface
{
    public function getAdminDashboardStats()
    {
        $today = now()->format('Y-m-d');
        $thisMonth = now()->format('Y-m');
        
        return [
            'total_users' => User::count(),
            'total_loans' => Loan::count(),
            'total_loan_amount' => Loan::sum('amount'),
            'active_loans' => Loan::whereIn('status', ['active', 'disbursed'])->count(),
            'pending_loans' => Loan::where('status', 'pending')->count(),
            'overdue_loans' => Loan::where('status', 'active')
                ->where('end_date', '<', now())
                ->where('remaining_balance', '>', 0)
                ->count(),
            'today_loans' => Loan::whereDate('created_at', $today)->count(),
            'month_loans' => Loan::whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->count(),
            'total_revenue' => LoanRepayment::sum('amount_paid'),
            'today_revenue' => LoanRepayment::whereDate('paid_date', $today)->sum('amount_paid'),
            'month_revenue' => LoanRepayment::whereYear('paid_date', now()->year)
                ->whereMonth('paid_date', now()->month)
                ->sum('amount_paid'),
        ];
    }

    public function getUserDashboardStats($userId)
    {
        return [
            'total_loans' => Loan::where('user_id', $userId)->count(),
            'active_loans' => Loan::where('user_id', $userId)
                ->whereIn('status', ['active', 'disbursed'])
                ->count(),
            'pending_loans' => Loan::where('user_id', $userId)
                ->where('status', 'pending')
                ->count(),
            'total_borrowed' => Loan::where('user_id', $userId)->sum('amount'),
            'total_repaid' => Loan::where('user_id', $userId)->sum('total_paid'),
            'outstanding_balance' => Loan::where('user_id', $userId)->sum('remaining_balance'),
            'next_payment_date' => LoanRepayment::whereHas('loan', function($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->whereIn('status', ['active', 'disbursed']);
            })
            ->whereIn('status', ['pending', 'partial'])
            ->orderBy('due_date')
            ->value('due_date'),
            'next_payment_amount' => LoanRepayment::whereHas('loan', function($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->whereIn('status', ['active', 'disbursed']);
            })
            ->whereIn('status', ['pending', 'partial'])
            ->orderBy('due_date')
            ->value('amount_due'),
        ];
    }

    public function getLoanAnalytics($period = 'monthly')
    {
        $groupBy = match($period) {
            'daily' => 'DATE(created_at)',
            'weekly' => 'YEARWEEK(created_at)',
            'monthly' => 'DATE_FORMAT(created_at, "%Y-%m")',
            'yearly' => 'YEAR(created_at)',
            default => 'DATE_FORMAT(created_at, "%Y-%m")',
        };
        
        return Loan::select(
            DB::raw($groupBy . ' as period'),
            DB::raw('COUNT(*) as total_loans'),
            DB::raw('SUM(amount) as total_amount'),
            DB::raw('AVG(amount) as average_amount'),
            DB::raw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved_count'),
            DB::raw('SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected_count')
        )
        ->groupBy('period')
        ->orderBy('period')
        ->get();
    }

    public function getRevenueAnalytics($period = 'monthly')
    {
        $groupBy = match($period) {
            'daily' => 'DATE(paid_date)',
            'weekly' => 'YEARWEEK(paid_date)',
            'monthly' => 'DATE_FORMAT(paid_date, "%Y-%m")',
            'yearly' => 'YEAR(paid_date)',
            default => 'DATE_FORMAT(paid_date, "%Y-%m")',
        };
        
        return LoanRepayment::select(
            DB::raw($groupBy . ' as period'),
            DB::raw('SUM(amount_paid) as total_revenue'),
            DB::raw('SUM(late_fee) as total_late_fees'),
            DB::raw('COUNT(*) as total_payments'),
            DB::raw('AVG(amount_paid) as average_payment')
        )
        ->whereNotNull('paid_date')
        ->groupBy('period')
        ->orderBy('period')
        ->get();
    }

    public function getUserAnalytics($period = 'monthly')
    {
        $groupBy = match($period) {
            'daily' => 'DATE(created_at)',
            'weekly' => 'YEARWEEK(created_at)',
            'monthly' => 'DATE_FORMAT(created_at, "%Y-%m")',
            'yearly' => 'YEAR(created_at)',
            default => 'DATE_FORMAT(created_at, "%Y-%m")',
        };
        
        return User::select(
            DB::raw($groupBy . ' as period'),
            DB::raw('COUNT(*) as total_users'),
            DB::raw('SUM(CASE WHEN user_type = "individual" THEN 1 ELSE 0 END) as individual_users'),
            DB::raw('SUM(CASE WHEN user_type = "business" THEN 1 ELSE 0 END) as business_users')
        )
        ->groupBy('period')
        ->orderBy('period')
        ->get();
    }

    public function getRecentActivities($limit = 10)
    {
        $loanActivities = Loan::with(['user', 'approver'])
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function($loan) {
                return [
                    'type' => 'loan',
                    'title' => 'New Loan Application',
                    'description' => $loan->user->full_name . ' applied for ₦' . number_format($loan->amount),
                    'status' => $loan->status,
                    'date' => $loan->created_at,
                    'user' => $loan->user->full_name,
                ];
            });
        
        $paymentActivities = LoanRepayment::with(['loan.user'])
            ->whereNotNull('paid_date')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function($payment) {
                return [
                    'type' => 'payment',
                    'title' => 'Loan Payment Received',
                    'description' => $payment->loan->user->full_name . ' paid ₦' . number_format($payment->amount_paid),
                    'status' => 'paid',
                    'date' => $payment->paid_date,
                    'user' => $payment->loan->user->full_name,
                ];
            });
        
        return $loanActivities->merge($paymentActivities)
            ->sortByDesc('date')
            ->take($limit)
            ->values();
    }

    public function getTopPerformingLoans($limit = 10)
    {
        return Loan::with(['user'])
            ->where('status', 'active')
            ->orderBy('remaining_balance')
            ->limit($limit)
            ->get()
            ->map(function($loan) {
                $paidPercentage = $loan->total_paid / $loan->total_amount * 100;
                return [
                    'loan_id' => $loan->id,
                    'user' => $loan->user->full_name,
                    'amount' => $loan->amount,
                    'total_paid' => $loan->total_paid,
                    'remaining_balance' => $loan->remaining_balance,
                    'paid_percentage' => round($paidPercentage, 2),
                    'status' => $loan->status,
                ];
            });
    }

    public function getDefaultRiskAnalysis()
    {
        $activeLoans = Loan::where('status', 'active')->get();
        
        $riskCategories = [
            'low' => 0,
            'medium' => 0,
            'high' => 0,
        ];
        
        foreach ($activeLoans as $loan) {
            $daysOverdue = $loan->days_overdue;
            $repaymentHistory = $loan->repayments()->count();
            $onTimePayments = $loan->repayments()
                ->where('status', 'paid')
                ->whereColumn('paid_date', '<=', 'due_date')
                ->count();
            
            $onTimeRate = $repaymentHistory > 0 ? ($onTimePayments / $repaymentHistory) * 100 : 100;
            
            if ($daysOverdue > 30 || $onTimeRate < 50) {
                $riskCategories['high']++;
            } elseif ($daysOverdue > 15 || $onTimeRate < 75) {
                $riskCategories['medium']++;
            } else {
                $riskCategories['low']++;
            }
        }
        
        return [
            'total_active_loans' => $activeLoans->count(),
            'risk_categories' => $riskCategories,
            'total_overdue_amount' => Loan::where('status', 'active')
                ->where('end_date', '<', now())
                ->sum('remaining_balance'),
            'average_days_overdue' => Loan::where('status', 'active')
                ->where('end_date', '<', now())
                ->avg('days_overdue'),
        ];
    }
}