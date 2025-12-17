<?php
// app/Models/User.php
namespace App\Models;

use App\Traits\HasOrderId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class ApplicantBank extends Model
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'loan_id',
        'beneficiary_bank',
        'bank_acc_name',
        'bank_acc_num',
        'balance_amount',
    ];


    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

}