<?php
// app/Http/Requests/Api/V1/User/LoanRequest.php
namespace App\Http\Requests\Api\V1\User;

use Illuminate\Foundation\Http\FormRequest;

class LoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Loan details (loans table)
            'loan_type' => 'required|string|in:personal,business,emergency',
            'loan_amount' => 'required|numeric|min:1000|max:5000000',
            'interest_rate' => 'sometimes|numeric|min:1|max:99',
            'loan_period' => 'required|integer|min:1|max:60',
            
            // Applicant Information (applicant_information table)
            'actual_name' => 'required|string|max:255',
            'id_card_num' => 'required|string|max:50',
            'current_job' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'stable_income' => 'required|numeric|min:0',
            'loan_purpose' => 'nullable|string|max:500',
            'current_address' => 'required|string|max:1000',
            'guarantor_name' => 'required|string|max:255',
            'guarantor_phone' => 'required|string|max:20',
            
            // Loan Documents (loan_document table)
            'front_id_card' => 'required|string|max:500',
            'back_id_card' => 'required|string|max:500',
            'selfie' => 'required|string|max:500',
            
            // Applicant Bank (applicant_bank table)
            'beneficiary_bank' => 'required|string|max:255',
            'bank_acc_name' => 'required|string|max:255',
            'bank_acc_num' => 'required|string|max:50',
            'balance_amount' => 'required|numeric|min:0',
            
            // Applicant Contract (applicant_contract table)
            'signature_url' => 'required|string|max:500',
            'terms_accepted' => 'required|boolean',
            'privacy_policy_accepted' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            // Loan amount messages
            'loan_amount.required' => 'Loan amount is required.',
            'loan_amount.numeric' => 'Loan amount must be a number.',
            'loan_amount.min' => 'Minimum loan amount is ₦1,000.',
            'loan_amount.max' => 'Maximum loan amount is ₦5,000,000.',
            
            // Loan period messages
            'loan_period.required' => 'Loan period is required.',
            'loan_period.integer' => 'Loan period must be a whole number.',
            'loan_period.min' => 'Minimum loan period is 1 month.',
            'loan_period.max' => 'Maximum loan period is 60 months.',
            
            // Interest rate messages
            'interest_rate.numeric' => 'Interest rate must be a number.',
            'interest_rate.min' => 'Minimum interest rate is 1%.',
            'interest_rate.max' => 'Maximum interest rate is 99%.',
            
            // Loan type messages
            'loan_type.required' => 'Loan type is required.',
            'loan_type.in' => 'Loan type must be personal, business, or emergency.',
            
            // Applicant information messages
            'actual_name.required' => 'Your full name is required.',
            'id_card_num.required' => 'ID card number is required.',
            'current_job.required' => 'Current job/occupation is required.',
            'gender.required' => 'Gender is required.',
            'gender.in' => 'Gender must be male or female.',
            'stable_income.required' => 'Stable monthly income is required.',
            'stable_income.numeric' => 'Stable income must be a number.',
            'current_address.required' => 'Current address is required.',
            'guarantor_name.required' => 'Guarantor name is required.',
            'guarantor_phone.required' => 'Guarantor phone number is required.',
            
            // Document messages
            'front_id_card.required' => 'Front of ID card is required.',
            'back_id_card.required' => 'Back of ID card is required.',
            'selfie.required' => 'Selfie photo is required.',
            
            // Bank information messages
            'beneficiary_bank.required' => 'Beneficiary bank name is required.',
            'bank_acc_name.required' => 'Bank account name is required.',
            'bank_acc_num.required' => 'Bank account number is required.',
            'balance_amount.required' => 'Account balance is required.',
            'balance_amount.numeric' => 'Account balance must be a number.',
            
            // Contract messages
            'signature_url.required' => 'Signature is required.',
            'terms_accepted.required' => 'You must accept the terms and conditions.',
            'terms_accepted.boolean' => 'Terms acceptance must be true or false.',
            'privacy_policy_accepted.required' => 'You must accept the privacy policy.',
            'privacy_policy_accepted.boolean' => 'Privacy policy acceptance must be true or false.',
        ];
    }

    public function attributes(): array
    {
        return [
            'loan_amount' => 'loan amount',
            'loan_period' => 'loan period',
            'interest_rate' => 'interest rate',
            'actual_name' => 'full name',
            'id_card_num' => 'ID card number',
            'current_job' => 'current job',
            'stable_income' => 'monthly income',
            'current_address' => 'current address',
            'guarantor_name' => 'guarantor name',
            'guarantor_phone' => 'guarantor phone',
            'front_id_card' => 'front ID card',
            'back_id_card' => 'back ID card',
            'beneficiary_bank' => 'bank name',
            'bank_acc_name' => 'account name',
            'bank_acc_num' => 'account number',
            'balance_amount' => 'account balance',
            'signature_url' => 'signature',
            'terms_accepted' => 'terms acceptance',
            'privacy_policy_accepted' => 'privacy policy acceptance',
        ];
    }

    /**
     * Prepare the data for validation.
     * You can modify or format data before validation runs.
     */
    protected function prepareForValidation(): void
    {
        // Convert boolean strings to actual booleans
        $this->merge([
            'terms_accepted' => filter_var($this->terms_accepted, FILTER_VALIDATE_BOOLEAN),
            'privacy_policy_accepted' => filter_var($this->privacy_policy_accepted, FILTER_VALIDATE_BOOLEAN),
            
            // Format numeric values
            'loan_amount' => $this->loan_amount ? floatval($this->loan_amount) : null,
            'stable_income' => $this->stable_income ? floatval($this->stable_income) : null,
            'balance_amount' => $this->balance_amount ? floatval($this->balance_amount) : null,
            
            // Set default interest rate if not provided
            'interest_rate' => $this->interest_rate ?? 5,
            
            // Trim string values
            'actual_name' => trim($this->actual_name ?? ''),
            'id_card_num' => trim($this->id_card_num ?? ''),
            'current_job' => trim($this->current_job ?? ''),
            'current_address' => trim($this->current_address ?? ''),
            'guarantor_name' => trim($this->guarantor_name ?? ''),
            'guarantor_phone' => trim($this->guarantor_phone ?? ''),
            'bank_acc_name' => trim($this->bank_acc_name ?? ''),
            'bank_acc_num' => trim($this->bank_acc_num ?? ''),
            'beneficiary_bank' => trim($this->beneficiary_bank ?? ''),
        ]);
    }

    /**
     * Get validated data with all required fields
     */
    public function validatedLoanData(): array
    {
        $validated = $this->validated();
        
        // Ensure all fields are present even if optional
        return array_merge([
            'loan_purpose' => null,
            'interest_rate' => 5,
        ], $validated);
    }
}