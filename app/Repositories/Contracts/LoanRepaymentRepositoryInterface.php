<?php
// app/Repositories/Contracts/LoanRepaymentRepositoryInterface.php
namespace App\Repositories\Contracts;

interface LoanRepaymentRepositoryInterface
{
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getLoanRepayments($loanId);
    public function getNextPendingRepayment($loanId);
    public function getOverdueRepayments($loanId = null);
    public function makePayment($repaymentId, $amount, $method, $reference);
    public function getTotalPaid($loanId);
    public function createRepaymentSchedule($loan);
    public function calculateLateFees($loanId);
    public function getUpcomingRepayments($userId, $days = 7);
}