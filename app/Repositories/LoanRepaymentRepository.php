<?php
// app/Repositories/LoanRepaymentRepository.php
namespace App\Repositories;

use App\Models\LoanRepayment;
use App\Repositories\Contracts\LoanRepaymentRepositoryInterface;
use Illuminate\Support\Facades\DB;

class LoanRepaymentRepository implements LoanRepaymentRepositoryInterface
{
    protected $model;

    public function __construct(LoanRepayment $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $repayment = $this->find($id);
        $repayment->update($data);
        return $repayment;
    }

    public function delete($id)
    {
        $repayment = $this->find($id);
        return $repayment->delete();
    }

    public function getLoanRepayments($loanId)
    {
        return $this->model->where('loan_id', $loanId)
            ->orderBy('installment_number')
            ->get();
    }

    public function getNextPendingRepayment($loanId)
    {
        return $this->model->where('loan_id', $loanId)
            ->whereIn('status', ['pending', 'overdue', 'partial'])
            ->orderBy('due_date')
            ->first();
    }

    public function getOverdueRepayments($loanId = null)
    {
        $query = $this->model->where('status', 'overdue')
            ->orWhere(function($q) {
                $q->where('due_date', '<', now())
                  ->where('status', 'pending');
            });
        
        if ($loanId) {
            $query->where('loan_id', $loanId);
        }
        
        return $query->with('loan')->get();
    }

    public function makePayment($repaymentId, $amount, $method, $reference)
    {
        $repayment = $this->find($repaymentId);
        $repayment->markAsPaid($amount, $method, $reference);
        return $repayment;
    }

    public function getTotalPaid($loanId)
    {
        return $this->model->where('loan_id', $loanId)
            ->where('status', 'paid')
            ->sum('amount_paid');
    }

  // App\Repositories\RepaymentRepository.php
public function createRepaymentSchedule($loan)
{
    $repayments = [];
    $loanAmount = $loan->loan_amount;
    $interestRate = $loan->interest_rate / 100 / 12; // Monthly interest rate
    $loanPeriod = $loan->loan_period; // in months
    $startDate = $loan->start_date ?? now();
    
    // Calculate monthly payment using annuity formula
    if ($interestRate > 0) {
        $monthlyPayment = $loanAmount * ($interestRate * pow(1 + $interestRate, $loanPeriod)) 
                          / (pow(1 + $interestRate, $loanPeriod) - 1);
    } else {
        $monthlyPayment = $loanAmount / $loanPeriod;
    }
    
    $remainingBalance = $loanAmount;
    
    for ($i = 1; $i <= $loanPeriod; $i++) {
        $interest = $remainingBalance * $interestRate;
        $principal = $monthlyPayment - $interest;
        $endingBalance = $remainingBalance - $principal;
        
        $repayments[] = [
            'loan_id' => $loan->id,
            'installment_number' => $i,
            'amount_due' => round($monthlyPayment, 2),
            'amount_paid' => 0,
            'late_fee' => 0,
            'due_date' => $startDate->copy()->addMonths($i)->format('Y-m-d'),
            'status' => 'pending',
            'begining_balance' => round($remainingBalance, 2),
            'interest' => round($interest, 2),
            'principal' => round($principal, 2),
            'ending_balance' => round($endingBalance, 2),
            'created_at' => now(),
            'updated_at' => now(),
        ];
        
        $remainingBalance = $endingBalance;
    }
    
    // Batch insert all repayments
    if (!empty($repayments)) {
        DB::table('loan_repayments')->insert($repayments);
    }
    
    return count($repayments);
}
    public function calculateLateFees($loanId)
    {
        $overdueRepayments = $this->getOverdueRepayments($loanId);
        $totalLateFee = 0;
        
        foreach ($overdueRepayments as $repayment) {
            $daysLate = $repayment->getDaysOverdueAttribute();
            $lateFee = $repayment->amount_due * 0.02 * $daysLate; // 2% per day
            $repayment->addLateFee($lateFee);
            $totalLateFee += $lateFee;
        }
        
        return $totalLateFee;
    }

    public function getUpcomingRepayments($userId, $days = 7)
    {
        return $this->model->whereHas('loan', function($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->whereIn('status', ['active', 'disbursed']);
        })
        ->whereIn('status', ['pending', 'partial'])
        ->where('due_date', '<=', now()->addDays($days))
        ->with('loan')
        ->orderBy('due_date')
        ->get();
    }
}