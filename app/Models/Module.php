<?php
// app/Models/UserAccount.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'modules';
    protected $fillable = [
        'name',
        'key',
        'order_sequence' 
    ];

    // Relationships
    public function role_modules(): HasMany
    {
        return $this->hasMany(RoleModule::class);
    }

    

}