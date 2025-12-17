<?php
// app/Http/Controllers/Api/V1/Admin/LoanController.php
namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Requests\Api\V1\Admin\LoanUpdateRequest;
use App\Services\V1\Admin\LoanService;
use Illuminate\Http\Request;

class LoanController extends BaseController
{
    protected $loanService;

    public function __construct(LoanService $loanService)
    {
        $this->loanService = $loanService;
    }

    /**
     * Get all loans
     */
    public function index(Request $request)
    {
        try {
            $filters = $request->only([
                'status', 'member_acc_id', 'user_id', 'date_from', 'date_to',
                'min_amount', 'max_amount', 'per_page',
                'sort_by', 'sort_order'
            ]);
            
            $loans = $this->loanService->getAllLoans($filters);
            return $this->paginated($loans, 'Loans retrieved successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Get loan details
     */
    public function show($id)
    {
        try {
            $loan = $this->loanService->getLoanDetails($id);
            return $this->success($loan, 'Loan details retrieved successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Update loan
     */
    public function update(LoanUpdateRequest $request, $id)
    {
        try {
            $loan = $this->loanService->updateLoan($id, $request->validated());
            return $this->success($loan, 'Loan updated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationError($e->errors());
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Delete loan
     */
    public function destroy($id)
    {
        try {
            $this->loanService->deleteLoan($id);
            return $this->success([], 'Loan deleted successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Approve loan
     */
    public function approve(Request $request, $id)
    {
        try {
            $request->validate([
                'approved_amount' => 'sometimes|numeric',
                'interest_rate' => 'sometimes|numeric',
                'term_months' => 'sometimes|integer',
                'notes' => 'nullable|string',
            ]);

            $loan = $this->loanService->approveLoan(
                $id,
                $request->user()->id,
                $request->only(['approved_amount', 'interest_rate', 'term_months', 'notes'])
            );

            return $this->success($loan, 'Loan approved successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationError($e->errors());
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Reject loan
     */
    public function reject(Request $request, $id)
    {
        try {
            $request->validate([
                'reason' => 'sometimes|string|min:10|max:500',
            ]);

            $loan = $this->loanService->rejectLoan(
                $id,
                $request->user()->id,
                $request->reason
            );

            return $this->success($loan, 'Loan rejected');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationError($e->errors());
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Disburse loan
     */
    public function disburse(Request $request, $id)
    {
        try {
            $request->validate([
                'disbursement_method' => 'required|in:bank_transfer,mobile_money,cheque',
                'transaction_reference' => 'required|string',
                'disbursement_date' => 'required|date',
                'charges' => 'sometimes|array',
            ]);

            $loan = $this->loanService->disburseLoan(
                $id,
                $request->user()->id,
                $request->all()
            );

            return $this->success($loan, 'Loan disbursed successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationError($e->errors());
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Get overdue loans
     */
    public function overdue(Request $request)
    {
        try {
            $loans = $this->loanService->getOverdueLoans(
                $request->input('per_page', 15)
            );
            return $this->paginated($loans, 'Overdue loans retrieved successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Get active loans
     */
    public function active(Request $request)
    {
        try {
            $loans = $this->loanService->getActiveLoans(
                $request->input('per_page', 15)
            );
            return $this->paginated($loans, 'Active loans retrieved successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Get loan statistics
     */
    public function statistics(Request $request)
    {
        try {
            $stats = $this->loanService->getLoanStatistics(
                $request->input('period', 'monthly')
            );
            return $this->success($stats, 'Loan statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }


    /**
     * Add late fees
     */
    public function addLateFees(Request $request, $id)
    {
        try {
            $lateFees = $this->loanService->addLateFees($id);
            return $this->success(['late_fees' => $lateFees], 'Late fees calculated');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Search loans
     */
    public function search(Request $request)
    {
        try {
            $request->validate([
                'query' => 'required|string|min:2',
            ]);
             dd($request->query);
                $loans = $this->loanService->searchLoans(
                $request->query,
                $request->input('per_page', 15)
            );

            return $this->paginated($loans, 'Search results');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationError($e->errors());
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}