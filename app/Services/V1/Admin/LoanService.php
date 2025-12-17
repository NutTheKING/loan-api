<?php
// app/Services/V1/Admin/LoanService.php
namespace App\Services\V1\Admin;

use App\Repositories\Contracts\LoanRepositoryInterface;
use App\Repositories\Contracts\LoanRepaymentRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;

class LoanService
{
    protected $loanRepository;
    protected $repaymentRepository;
    protected $userRepository;

    public function __construct(
        LoanRepositoryInterface $loanRepository,
        LoanRepaymentRepositoryInterface $repaymentRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->loanRepository = $loanRepository;
        $this->repaymentRepository = $repaymentRepository;
        $this->userRepository = $userRepository;
    }

    public function getAllLoans($filters = [])
    {
        return $this->loanRepository->filter($filters);
    }

    public function getLoanDetails($loanId)
    {
        return $this->loanRepository->findWithRelations($loanId);
    }

    public function approveLoan($loanId, $adminId, array $data = [])
    {
        DB::beginTransaction();
        
        try {
            $loan = $this->loanRepository->find($loanId);
            
            if (!$loan->canBeApproved()) {
                throw new \Exception('Loan cannot be approved in its current status.');
            }
    
            $approvedLoan = $this->loanRepository->approve($loanId, $adminId, $data);
            
            DB::commit();
            return $approvedLoan;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function rejectLoan($loanId, $adminId, $reason)
    {
        DB::beginTransaction();
        
        try {
            $loan = $this->loanRepository->find($loanId);
            
            if (!in_array($loan->status, ['pending', 'under_review'])) {
                throw new \Exception('Loan cannot be rejected in its current status.');
            }
            
            $rejectedLoan = $this->loanRepository->reject($loanId, $adminId);
            
            DB::commit();
            return $rejectedLoan;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

   public function disburseLoan($loanId, $adminId, array $data = [])
{
    DB::beginTransaction();
    
    try {
        $loan = $this->loanRepository->find($loanId);
        
        \Log::info('Attempting to disburse loan', [
            'loan_id' => $loanId,
            'current_status' => $loan->status,
            'can_be_disbursed' => $loan->canBeDisbursed()
        ]);
        
        if (!$loan->canBeDisbursed()) {
            throw new \Exception("Loan cannot be disbursed. Status: {$loan->status}");
        }
        
        $disbursedLoan = $this->loanRepository->disburse($loanId, $adminId, $data);
        
        \Log::info('Loan disbursed, creating repayment schedule', [
            'loan_id' => $disbursedLoan->id,
            'loan_amount' => $disbursedLoan->loan_amount,
            'loan_period' => $disbursedLoan->loan_period
        ]);
        
        // Create repayment schedule
        $repaymentCount = $this->repaymentRepository->createRepaymentSchedule($disbursedLoan);
        
        \Log::info('Repayment schedule created', [
            'loan_id' => $disbursedLoan->id,
            'repayment_count' => $repaymentCount
        ]);
        
        DB::commit();
        
        return $disbursedLoan;
        
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Loan disbursement failed', [
            'loan_id' => $loanId,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        throw $e;
    }
}

    public function updateLoan($loanId, array $data)
    {
        $loan = $this->loanRepository->find($loanId);
        
        // Remove fields that shouldn't be updated directly
        $restrictedFields = ['status', 'approved_by', 'approved_at', 'disbursed_at'];
        foreach ($restrictedFields as $field) {
            if (isset($data[$field])) {
                unset($data[$field]);
            }
        }
        
        return $this->loanRepository->update($loanId, $data);
    }

    public function deleteLoan($loanId)
    {
        $loan = $this->loanRepository->find($loanId);
        
        if (!in_array($loan->status, ['pending', 'rejected', 'cancelled'])) {
            throw new \Exception('Only pending, rejected, or cancelled loans can be deleted.');
        }
        
        return $this->loanRepository->delete($loanId);
    }

    public function getOverdueLoans($perPage = 15)
    {
        return $this->loanRepository->getOverdueLoans($perPage);
    }

    public function getActiveLoans($perPage = 15)
    {
        return $this->loanRepository->getActiveLoans($perPage);
    }

    public function getLoanStatistics($period = 'monthly')
    {
        return $this->loanRepository->getStatistics($period);
    }

    public function markLoanAsDefaulted($loanId)
    {
        $loan = $this->loanRepository->find($loanId);
        
        if (!in_array($loan->status, ['active', 'disbursed'])) {
            throw new \Exception('Only active or disbursed loans can be marked as defaulted.');
        }
        
        $daysOverdue = now()->diffInDays($loan->end_date);
        
        return $this->loanRepository->update($loanId, [
            'status' => 'defaulted',
            'days_overdue' => $daysOverdue,
        ]);
    }

    public function addLateFees($loanId)
    {
        return $this->repaymentRepository->calculateLateFees($loanId);
    }

    public function getLoanAnalytics()
    {
        return [
            'daily' => $this->loanRepository->getStatistics('daily'),
            'weekly' => $this->loanRepository->getStatistics('weekly'),
            'monthly' => $this->loanRepository->getStatistics('monthly'),
            'yearly' => $this->loanRepository->getStatistics('yearly'),
        ];
    }

    public function searchLoans($query, $perPage = 15)
    {
        return $this->loanRepository->search($query, $perPage);
    }
}