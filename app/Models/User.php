<?php
// app/Models/User.php
namespace App\Models;

use App\Traits\HasOrderId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasOrderId, Notifiable, SoftDeletes;

    protected $orderIdPrefix = 'CUST';
    protected $fillable = [
        'order_id',
        'full_name',
        'user_name',
        'email',
        'phone',
        'password',
        'profile',
        'ip_address',
        'device',
        'credit_score',
        'account_status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    // Accessors

    public function getAgeAttribute()
    {
        return $this->dob ? now()->diffInYears($this->dob) : null;
    }

    // Methods

    public function hasActiveLoans()
    {
        return $this->loans()->whereIn('status', ['active', 'disbursed'])->exists();
    }

    public function totalLoanAmount()
    {
        return $this->loans()->sum('amount');
    }

    public function totalOutstandingBalance()
    {
        return $this->loans()->sum('remaining_balance');
    }
}