<?php
// app/Models/Loan.php
namespace App\Models;

use App\Traits\HasOrderId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Loan extends Model
{
    use HasFactory, HasOrderId, SoftDeletes;
    protected $orderIdPrefix = 'LOA';
    protected $fillable = [
        'user_id',
        'order_num',
        'loan_amount',
        'loan_period',
        'principle',
        'interest_rate',
        'interest_amount',
        'total_payment',
        'front_remark',
        'back_remark',
        'status',
        'approved_at',
        'approved_by',
        'disbursed_at',
        'updated_by',
        // Fields from your original model that might still be needed
        'purpose',
        'description',
        'rejection_reason',
        'start_date',
        'end_date',
        'remaining_balance',
        'total_paid',
        'days_overdue',
        'collateral_info',
        'guarantor_info',
        
    ];

    protected $casts = [
        'loan_amount' => 'decimal:2',
        'principle' => 'decimal:2',
        'interest_rate' => 'decimal:1',
        'interest_amount' => 'decimal:2',
        'total_payment' => 'decimal:2',
        'remaining_balance' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'front_remark' => 'date',
        'back_remark' => 'date',
        'approved_at' => 'datetime',
        'disbursed_at' => 'datetime',
        'start_date' => 'date',
        'end_date' => 'date',
        'collateral_info' => 'array',
        'guarantor_info' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];


    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(Admin::class, 'approved_by');
    }

    public function repayments()
    {
        return $this->hasMany(LoanRepayment::class);
    }

    // Accessors
    public function getTotalAmountAttribute()
    {
        return $this->amount + ($this->amount * $this->interest_rate / 100);
    }

    public function getRemainingInstallmentsAttribute()
    {
        if (!$this->start_date) {
            return $this->term_months;
        }
        
        $paidInstallments = $this->repayments()->where('status', 'paid')->count();
        return max(0, $this->term_months - $paidInstallments);
    }

    public function getNextPaymentDateAttribute()
    {
        $lastPaid = $this->repayments()->where('status', 'paid')->orderBy('due_date', 'desc')->first();
        
        if ($lastPaid) {
            return $lastPaid->due_date->addMonth();
        }
        
        return $this->start_date ? $this->start_date->addMonth() : null;
    }

    public function getIsOverdueAttribute()
    {
        return $this->days_overdue > 0;
    }

    // Methods
    public function calculateMonthlyPayment()
    {
        $monthlyRate = $this->interest_rate / 100 / 12;
        $numerator = $this->amount * $monthlyRate * pow(1 + $monthlyRate, $this->term_months);
        $denominator = pow(1 + $monthlyRate, $this->term_months) - 1;
        
        return round($numerator / $denominator, 2);
    }

    public function updateStatus($status)
    {
        $this->status = $status;
        
        if ($status === 'approved') {
            $this->approved_at = now();
        } elseif ($status === 'disbursed') {
            $this->disbursed_at = now();
            $this->start_date = now();
            $this->end_date = now()->addMonths($this->term_months);
            $this->remaining_balance = $this->total_amount;
        } elseif ($status === 'completed') {
            $this->remaining_balance = 0;
        }
        
        $this->save();
    }

    public function canBeApproved()
    {
        return $this->status === 'pending' || $this->status === 'under_review';
    }

    public function canBeDisbursed()
    {
        return $this->status === 'approved';
    }
}