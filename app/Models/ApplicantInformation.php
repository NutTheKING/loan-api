<?php
// app/Models/ApplicantInformation.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApplicantInformation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'loan_id',
        'actual_name',
        'id_card_num',
        'current_job',
        'gender',
        'stable_income',
        'loan_purpose',
        'current_address',
        'guarantor_name',
        'guarantor_phone',
        'updated_by',
    ];

    protected $casts = [
        'stable_income' => 'decimal:2',
        'gender' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function updatedByAdmin()
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }

    // Accessors
    public function getFormattedIncomeAttribute()
    {
        return '$' . number_format($this->stable_income, 2);
    }

    // Methods
    public function hasGuarantor()
    {
        return !empty($this->guarantor_name) && !empty($this->guarantor_phone);
    }

    // Scopes
    public function scopeWithGuarantor($query)
    {
        return $query->whereNotNull('guarantor_name')
                    ->whereNotNull('guarantor_phone');
    }

    public function scopeByGender($query, $gender)
    {
        return $query->where('gender', $gender);
    }

    public function scopeWithStableIncome($query, $minIncome = 0)
    {
        return $query->where('stable_income', '>=', $minIncome);
    }
}