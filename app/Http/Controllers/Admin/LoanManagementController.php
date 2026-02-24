<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use Illuminate\Http\Request;

class LoanManagementController extends Controller
{
    public function index(Request $request)
    {
        $loans = Loan::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.loans.index', compact('loans'));
    }
}
