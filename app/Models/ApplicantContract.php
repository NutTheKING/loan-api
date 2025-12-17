<?php
// app/Models/User.php
namespace App\Models;

use App\Traits\HasOrderId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class ApplicantContract extends Model
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'loan_id',
        'signature_url',
        'terms_accepted',
        'privacy_policy_accepted',
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