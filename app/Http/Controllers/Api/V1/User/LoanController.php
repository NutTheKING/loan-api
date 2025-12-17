<?php
// app/Http/Controllers/Api/V1/User/LoanController.php
namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Requests\Api\V1\User\LoanRequest;
use App\Services\V1\User\LoanService;
use Illuminate\Http\Request;

class LoanController extends BaseController
{
    protected $loanService;

    public function __construct(LoanService $loanService)
    {
        $this->loanService = $loanService;
    }

    /**
     * Get all user loans
     */
    public function index(Request $request)
    {
        try {
            $filters = $request->only(['status', 'per_page']);
            $loans = $this->loanService->getUserLoans($request->user()->id, $filters);
            
            return $this->paginated($loans, 'Loans retrieved successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

     /**
     * Get Calculate Loan
     */
    public function calculateLoan(Request $request)
    {
        try {
            $loan_rate = 5;
            $loans = $this->loanService->calculateLoanSummaryDirect($request['loan_amount'], $request['loan_period'], $loan_rate);
            return $this->success($loans, 'Loan details retrieved successfully');
            
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Apply for a new loan
     */
    public function store(LoanRequest $request)
    {
        try {
            $loan = $this->loanService->applyForLoan(
                $request->user()->id,
                $request->validated()
            );
            
            return $this->created($loan, 'Loan application submitted successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationError($e->errors());
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }


    /**
     * Get loan details
     */
    public function show(Request $request, $id)
    {
        try {
            $loan = $this->loanService->getLoanDetails($request->user()->id, $id);
            return $this->success($loan, 'Loan details retrieved successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Make loan repayment
     */
    public function makeRepayment(Request $request, $loanId)
    {
        try {
            $request->validate([
                'amount' => 'required|numeric|min:1',
                'payment_method' => 'required|string|in:bank,card,mobile_money',
                'transaction_reference' => 'required|string|unique:loan_repayments,transaction_reference',
            ]);

            $repayment = $this->loanService->makeRepayment(
                $request->user()->id,
                $loanId,
                $request->amount,
                $request->payment_method,
                $request->transaction_reference
            );

            return $this->success($repayment, 'Payment successful');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationError($e->errors());
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Cancel loan application
     */
    public function cancel(Request $request, $loanId)
    {
        try {
            $loan = $this->loanService->cancelLoanApplication($request->user()->id, $loanId);
            return $this->success($loan, 'Loan application cancelled successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}