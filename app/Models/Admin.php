<?php
// app/Models/Admin.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'full_name',
        'user_name',
        'email',
        'password',
        'profile',
        'role_id',
        'phone',
        'is_active',
        
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function approvedLoans()
    {
        return $this->hasMany(Loan::class, 'approved_by');
    }

    public function disbursedLoans()
    {
        return $this->hasMany(Loan::class, 'approved_by')->where('status', 'disbursed');
    }
   
    public function role(){
        return $this->belongsTo(Role::class, 'role_id');
    }

 public function roleModules(): HasManyThrough
{  
    return $this->hasManyThrough(
        RoleModule::class,      // Final: Pivot records
        Role::class,            // Intermediate: Role
        'id',                   // role.id
        'role_id',              // role_modules.role_id  
        'role_id',              // admins.role_id
        'id'                    // role.id
    );
}
  // Scopes
    public function scopeClients($query)
    {
        return $query->where('user_type', '');
    }
    
    public function scopeAdmins($query)
    {
        return $query->where('user_type', 'admin');
    }
    // Methods
    public function isRoleSuperAdmin()
    {
        return in_array('super_admin', $this->role);
    }
    public function isRoleAdmin()
    {
        return in_array('admin', $this->role);
    }
    public function isRoleLoanOperator()
    {
        return in_array('loan_opperator', $this->role);
    }

    public function isOperationsAdmin()
    {
        return $this->admin_type  === 'operations';
    }
    public function isSystemAdmin()
        {
            return $this->admin_type === 'system_admin';
        }
        
}