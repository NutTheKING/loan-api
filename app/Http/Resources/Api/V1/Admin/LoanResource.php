<?php
// app/Http/Resources/Api/V1/Admin/LoanResource.php
namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class LoanResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'amount' => (float) $this->amount,
            'amount_formatted' => '₦' . number_format($this->amount, 2),
            'interest_rate' => (float) $this->interest_rate,
            'term_months' => $this->term_months,
            'monthly_payment' => (float) $this->monthly_payment,
            'monthly_payment_formatted' => '₦' . number_format($this->monthly_payment, 2),
            'purpose' => $this->purpose,
            'purpose_formatted' => ucfirst(str_replace('_', ' ', $this->purpose)),
            'description' => $this->description,
            'status' => $this->status,
            'status_formatted' => ucfirst(str_replace('_', ' ', $this->status)),
            'rejection_reason' => $this->rejection_reason,
            'approved_at' => $this->approved_at?->format('Y-m-d H:i:s'),
            'disbursed_at' => $this->disbursed_at?->format('Y-m-d H:i:s'),
            'start_date' => $this->start_date?->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'remaining_balance' => (float) $this->remaining_balance,
            'remaining_balance_formatted' => '₦' . number_format($this->remaining_balance, 2),
            'total_paid' => (float) $this->total_paid,
            'total_paid_formatted' => '₦' . number_format($this->total_paid, 2),
            'days_overdue' => $this->days_overdue,
            'is_overdue' => $this->is_overdue,
            'total_amount' => (float) $this->total_amount,
            'total_amount_formatted' => '₦' . number_format($this->total_amount, 2),
            'remaining_installments' => $this->remaining_installments,
            'next_payment_date' => $this->next_payment_date?->format('Y-m-d'),
            'collateral_info' => $this->collateral_info,
            'guarantor_info' => $this->guarantor_info,
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->full_name,
                    'email' => $this->user->email,
                    'phone' => $this->user->phone,
                ];
            }),
            'approver' => $this->whenLoaded('approver', function () {
                return $this->approver ? [
                    'id' => $this->approver->id,
                    'name' => $this->approver->name,
                    'email' => $this->approver->email,
                ] : null;
            }),
            'repayments' => $this->whenLoaded('repayments'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}