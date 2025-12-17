<?php
// app/Http/Requests/Api/V1/Admin/LoanUpdateRequest.php
namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;

class LoanUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => 'sometimes|numeric|min:1000',
            'interest_rate' => 'sometimes|numeric|min:1|max:30',
            'term_months' => 'sometimes|integer|min:1|max:60',
            'monthly_payment' => 'sometimes|numeric|min:0',
            'purpose' => 'sometimes|in:personal,business,emergency,education,medical,home_improvement,vehicle,other',
            'description' => 'nullable|string|max:1000',
            'status' => 'sometimes|in:pending,under_review,approved,rejected,disbursed,active,completed,defaulted',
            'rejection_reason' => 'nullable|string|max:500',
            'remaining_balance' => 'sometimes|numeric|min:0',
            'total_paid' => 'sometimes|numeric|min:0',
            'days_overdue' => 'sometimes|integer|min:0',
            'collateral_info' => 'nullable|array',
            'guarantor_info' => 'nullable|array',
        ];
    }
}