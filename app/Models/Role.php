<?php
// app/Models/UserAccount.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'key'
    ];

    // Relationships
    public function role_modules(): BelongsToMany
    {
        return $this->belongsToMany(RoleModule::class);
    }

    public function admins(): HasMany
    {
        return $this->hasMany(Admin::class);
    }
  public function roleModules()
    {
        return $this->hasMany(RoleModule::class);
    }
    
    public function modules()
    {
        return $this->belongsToMany(Module::class, 'role_modules');
    }

}