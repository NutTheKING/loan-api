<?php
// app/Models/LoanRepayment.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanRepayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'installment_number',
        'amount_due',
        'amount_paid',
        'late_fee',
        'due_date',
        'paid_date',
        'status',
        'payment_method',
        'transaction_reference',
        'notes',
    ];

    protected $casts = [
        'amount_due' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'late_fee' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    // Accessors
    public function getRemainingAmountAttribute()
    {
        return $this->amount_due - $this->amount_paid;
    }

    public function getIsOverdueAttribute()
    {
        return $this->status === 'overdue' || ($this->due_date < now() && $this->status !== 'paid');
    }

    public function getDaysOverdueAttribute()
    {
        if ($this->due_date >= now() || $this->status === 'paid') {
            return 0;
        }
        
        return now()->diffInDays($this->due_date);
    }

    // Methods
    public function markAsPaid($amount, $paymentMethod, $reference, $date = null)
    {
        $this->amount_paid = $amount;
        $this->status = $amount >= $this->amount_due ? 'paid' : 'partial';
        $this->paid_date = $date ?? now();
        $this->payment_method = $paymentMethod;
        $this->transaction_reference = $reference;
        $this->save();

        // Update loan total paid
        $this->loan->total_paid += $amount;
        $this->loan->remaining_balance = max(0, $this->loan->remaining_balance - $amount);
        
        if ($this->loan->remaining_balance <= 0) {
            $this->loan->updateStatus('completed');
        }
        
        $this->loan->save();
    }

    public function addLateFee($fee)
    {
        $this->late_fee += $fee;
        $this->amount_due += $fee;
        $this->status = 'overdue';
        $this->save();
    }
}