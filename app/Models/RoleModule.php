<?php
// app/Models/UserAccount.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoleModule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'role_id',
        'module_id'
    ];
     
    //Relationships
    public function roles():BelongsToMany{
        return $this->belongsToMany(Role::class);
    }
    public function modules():BelongsToMany{
        return $this->belongsToMany(related: Module::class);
    }

}