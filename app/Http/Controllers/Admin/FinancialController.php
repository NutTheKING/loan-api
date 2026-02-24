<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LoanRepayment;
use Illuminate\Http\Request;

class FinancialController extends Controller
{
    public function index(Request $request)
    {
        $totalDisbursed = Loan::where('status', 'approved')->sum('amount');
        $totalRepaid = LoanRepayment::sum('amount');

        return view('admin.financial.index', compact('totalDisbursed', 'totalRepaid'));
    }
}
