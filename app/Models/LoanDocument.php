<?php
// app/Models/UserAccount.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanDocument extends Model
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'loan_id',
        'front_id_card',
        'back_id_card',
        'selfie'
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