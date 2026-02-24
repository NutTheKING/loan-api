<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Loan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalUsers = User::count();
        $totalLoans = Loan::count();
        $pendingLoans = Loan::where('status', 'pending')->count();
        $approvedLoans = Loan::where('status', 'approved')->count();

        // Chart: monthly approved disbursed amounts (last 6 months)
        $chartLabels = [];
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $start = now()->subMonths($i)->startOfMonth();
            $end = now()->subMonths($i)->endOfMonth();
            $chartLabels[] = $start->format('M Y');
            $chartData[] = Loan::where('status', 'approved')
                ->whereBetween('created_at', [$start, $end])
                ->sum('amount');
        }

        // Recent activities (latest loans)
        $recentActivities = Loan::orderBy('created_at', 'desc')
            ->take(6)
            ->get(['id', 'user_id', 'amount', 'status', 'created_at']);

        // Top loans (by amount)
        $topLoans = Loan::orderBy('amount', 'desc')
            ->take(5)
            ->get(['id', 'user_id', 'amount', 'status']);

        return view('admin.dashboard', compact(
            'totalUsers', 'totalLoans', 'pendingLoans', 'approvedLoans',
            'chartLabels', 'chartData', 'recentActivities', 'topLoans'
        ));
    }
}
