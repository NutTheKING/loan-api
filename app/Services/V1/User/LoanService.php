<?php
// app/Services/V1/User/LoanService.php
namespace App\Services\V1\User;

use App\Repositories\Contracts\ApplicantContractRepositoryInterface;
use App\Repositories\Contracts\ApplicantInformationRepositoryInterface;
use App\Repositories\Contracts\LoanDocumentRepositoryInterface;
use App\Repositories\Contracts\LoanRepositoryInterface;
use App\Repositories\Contracts\LoanRepaymentRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\ApplicantBankRepositoryInterface;
// use App\Repositories\Contracts\ApplicantContractRepositoryInterface;        
// use App\Repositories\Contracts\LoanDocumentRepositoryInterface;    
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LoanService
{
    protected $loanRepository;
    protected $repaymentRepository;
    protected $userRepository;
    protected $applicantInformationRepository;
    protected $applicantBankRepository;
    protected $applicantContractRepository;
    protected $loanDocumentRepository;

    public function __construct(
        LoanRepositoryInterface $loanRepository,
        LoanRepaymentRepositoryInterface $repaymentRepository,
        UserRepositoryInterface $userRepository,
        ApplicantInformationRepositoryInterface $applicantInformationRepository,
        ApplicantBankRepositoryInterface $applicantBankRepository,
        LoanDocumentRepositoryInterface $loanDocumentRepository,
        ApplicantContractRepositoryInterface $applicantContractRepository
    ) {
        $this->loanRepository = $loanRepository;
        $this->repaymentRepository = $repaymentRepository;
        $this->userRepository = $userRepository;
        $this->applicantInformationRepository = $applicantInformationRepository;
        $this->applicantBankRepository =  $applicantBankRepository;
        $this->loanDocumentRepository = $loanDocumentRepository;
        $this->applicantContractRepository = $applicantContractRepository;
    }

    public function applyForLoan($userId, array $data)
    {
        // Validate input data
        $validator = Validator::make($data, [
            'loan_type' => 'required|string',
            'loan_amount' => 'required|numeric|min:0',
            'loan_period' => 'required|integer|min:1',
            'actual_name' => 'required|string',
            'id_card_num' => 'required|string',
            'current_job' => 'required|string',
            'gender' => 'required|string|in:male,female,other',
            'stable_income' => 'required|numeric|min:0',
            'current_address' => 'required|string',
            'guarantor_name' => 'required|string',
            'guarantor_phone' => 'required|string',
            'front_id_card' => 'required|string',
            'back_id_card' => 'required|string',
            'selfie' => 'required|string',
            'beneficiary_bank' => 'required|string',
            'bank_acc_name' => 'required|string',
            'bank_acc_num' => 'required|string',
            'balance_amount' => 'required|numeric|min:0',
            'signature_url' => 'required|string',
            'terms_accepted' => 'required|boolean',
            'privacy_policy_accepted' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()->toArray()
            ];
        }

        try {
            // 1. Calculate loan summary - FIXED: Correct parameter order
            $summaryPayment = $this->calculateLoanSummaryDirect(
                $data['loan_amount'],
                $data['loan_period'],
                $data['interest_rate'] ?? 5.0  // Default interest rate
            );
             
            // Start transaction
            DB::beginTransaction();
            
            // STEP 1: Store in loans table
            $loanData = [
                'user_id' => $userId,
                'loan_type' => $data['loan_type'],
                'loan_amount' => $data['loan_amount'],
                'interest_rate' => $summaryPayment['interest_rate'],
                'loan_period' => $data['loan_period'],
                'principle' => $data['loan_amount'],
                'interest_amount' => $summaryPayment['interest_amount_total'],
                'total_payment' => $summaryPayment['total_payments'],
                'status' => 'pending',
            ];
            
            $loan = $this->loanRepository->create($loanData);
            
            if (!$loan) {
                throw new \Exception('Failed to create loan record');
            }
            
            // STEP 2: Store in applicant_information table
            $applicantInfo = [
                'loan_id' => $loan->id,
                'actual_name' => $data['actual_name'],
                'id_card_num' => $data['id_card_num'],
                'current_job' => $data['current_job'],
                'gender' => $data['gender'],
                'stable_income' => $data['stable_income'],
                'loan_purpose' => $data['loan_purpose'] ?? null,
                'current_address' => $data['current_address'],
                'guarantor_name' => $data['guarantor_name'],
                'guarantor_phone' => $data['guarantor_phone'],
                'updated_by' => null
            ];
            
            $applicant = $this->applicantInformationRepository->create($applicantInfo);
            
            if (!$applicant) {
                throw new \Exception('Failed to store applicant information');
            }
            
            // Update loan step to 2 - FIXED: Added step parameter
            $this->loanRepository->update($loan->id, ['current_step' => 2]);
            
            // STEP 3: Store in loan_document table
            $documentData = [
                'loan_id' => $loan->id,
                'front_id_card' => $data['front_id_card'],
                'back_id_card' => $data['back_id_card'],
                'selfie' => $data['selfie'],
            ];
            
            $documents = $this->loanDocumentRepository->create($documentData);
            
            if (!$documents) {
                throw new \Exception('Failed to store loan documents');
            }
            
            // Update loan step to 3
            $this->loanRepository->update($loan->id, ['current_step' => 3]);
            
            // STEP 4: Store in applicant_bank table
            $bankData = [
                'loan_id' => $loan->id,
                'beneficiary_bank' => $data['beneficiary_bank'],
                'bank_acc_name' => $data['bank_acc_name'],
                'bank_acc_num' => $data['bank_acc_num'],
                'balance_amount' => $data['balance_amount'],
                'updated_by' => null
            ];
            
            $bank = $this->applicantBankRepository->create($bankData);
            
            if (!$bank) {
                throw new \Exception('Failed to store bank information');
            }
            
            // Update loan step to 4
            $this->loanRepository->update($loan->id, ['current_step' => 4]);
            
            // STEP 5: Store in applicant_contract table
            $contractData = [
                'loan_id' => $loan->id,
                'signature_url' => $data['signature_url'],
                'terms_accepted' => $data['terms_accepted'] ?? false,
                'privacy_policy_accepted' => $data['privacy_policy_accepted'] ?? false,
                'updated_by' => null
            ];
            
            $contract = $this->applicantContractRepository->create($contractData);
            
            if (!$contract) {
                throw new \Exception('Failed to store contract information');
            }
            
            // Final update - mark loan as completed
            $this->loanRepository->update($loan->id, [
                'status' => 'pending',
                'current_step' => 5,
                'submitted_at' => now()
            ]);
            
            // Commit transaction
            DB::commit();
            
            return [
                'success' => true,
                'loan' => $loan,
                'applicant_information' => $applicant,
                'loan_document' => $documents,
                'applicant_bank' => $bank,
                'applicant_contract' => $contract,
                'message' => 'Loan application submitted successfully!'
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Loan application failed: ' . $e->getMessage(), [
                'user_id' => $userId,
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Loan application failed: ' . $e->getMessage(),
            ];
        }
    }

    public function getUserLoans($userId, $filters = [])
    {
        $filters['user_id'] = $userId;
        return $this->loanRepository->filter($filters);
    }

    public function getLoanDetails($userId, $loanId)
    {
        $loan = $this->loanRepository->findWithRelations($loanId);
        
        if (!$loan) {
            throw new \Exception('Loan not found.');
        }
        
        if ($loan->user_id != $userId) {
            throw new \Exception('Unauthorized access to loan.');
        }
        
        return $loan;
    }

    public function makeRepayment($userId, $loanId, $amount, $paymentMethod, $reference)
    {
        DB::beginTransaction();
        
        try {
            $loan = $this->loanRepository->find($loanId);
            
            if (!$loan) {
                throw new \Exception('Loan not found.');
            }
            
            if ($loan->user_id != $userId) {
                throw new \Exception('Unauthorized access to loan.');
            }
            
            if (!in_array($loan->status, ['active', 'disbursed'])) {
                throw new \Exception('Loan is not active for repayment.');
            }
            
            if ($amount <= 0) {
                throw new \Exception('Invalid payment amount.');
            }
            
            // Find next pending repayment
            $repayment = $this->repaymentRepository->getNextPendingRepayment($loanId);
            
            if (!$repayment) {
                throw new \Exception('No pending repayments found.');
            }
            
            // Process payment
            $processedRepayment = $this->repaymentRepository->makePayment(
                $repayment->id,
                $amount,
                $paymentMethod,
                $reference
            );
            
            DB::commit();
            return [
                'success' => true,
                'repayment' => $processedRepayment,
                'message' => 'Payment processed successfully'
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function calculateLoanSummaryDirect($loan_amount, $loan_period, $interest_rate) 
    {
        // Validate inputs
        if ($loan_amount <= 0 || $loan_period <= 0 || $interest_rate < 0) {
            throw new \InvalidArgumentException('Invalid loan parameters');
        }
        
        // Convert annual rate to monthly decimal
        $monthlyRate = $interest_rate / 100 / 12;
        
        if ($monthlyRate > 0) {
            // Direct formula for total interest (annuity formula)
            $compound = pow(1 + $monthlyRate, $loan_period);
            $monthlyPayment = $loan_amount * ($monthlyRate * $compound) / ($compound - 1);
            $total_payments = $monthlyPayment * $loan_period;
        } else {
            // Handle zero interest rate
            $total_payments = $loan_amount;
        }
        
        $total_interest = $total_payments - $loan_amount;
        
        return [
            'total_principal' => round($loan_amount, 2),
            'interest_rate' => round($interest_rate, 2),
            'interest_amount_total' => round($total_interest, 2),
            'total_payments' => round($total_payments, 2),
            'monthly_payment' => isset($monthlyPayment) ? round($monthlyPayment, 2) : round($loan_amount / $loan_period, 2)
        ];
    }
}