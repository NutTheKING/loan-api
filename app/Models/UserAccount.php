<?php
// app/Models/UserAccount.php
namespace App\Models;

use App\Traits\HasAccountNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAccount extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasAccountNumber;

    protected $fillable = [
        'account_num',
        'real_name',
         'acc_type',
        'bank_name',
        'user_id',
        'balance_amount',
        'account_status',
        'currency',
    ];

    // Relationships
    public function user(): HasOne
    {
        return $this->HasOne(User::class);
    }

    // Accessors
    public function accountNumberFormat()
        {
            return 'ACC' . now()->format('Y') . str_pad($this->id, 8, '0', STR_PAD_LEFT);
        }
    // Methods
    

}