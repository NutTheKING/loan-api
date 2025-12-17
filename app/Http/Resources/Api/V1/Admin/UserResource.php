<?php
// app/Http/Resources/Api/V1/Admin/UserResource.php
namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'user_type' => $this->user_type,
            'user_type_formatted' => ucfirst(str_replace('_', ' ', $this->user_type)),
            'id_number' => $this->id_number,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'age' => $this->age,
            'address' => $this->address,
            'employment_status' => $this->employment_status,
            'employment_status_formatted' => ucfirst(str_replace('_', ' ', $this->employment_status)),
            'monthly_income' => (float) $this->monthly_income,
            'monthly_income_formatted' => '₦' . number_format($this->monthly_income, 2),
            'bank_name' => $this->bank_name,
            'account_number' => $this->account_number,
            'status' => $this->status,
            'status_formatted' => ucfirst($this->status),
            'email_verified_at' => $this->email_verified_at?->format('Y-m-d H:i:s'),
            'has_active_loans' => $this->hasActiveLoans(),
            'total_loan_amount' => $this->totalLoanAmount(),
            'total_outstanding_balance' => $this->totalOutstandingBalance(),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}