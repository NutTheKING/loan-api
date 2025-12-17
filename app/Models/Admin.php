<?php
// app/Models/Admin.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        'role',
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

    // Accessors
    public function getPermissionsArrayAttribute()
    {
        return $this->permissions ?? [];
    }

    // Methods
    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isLoanOfficer()
    {
        return $this->role === 'loan_officer';
    }

    public function hasPermission($permission)
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $permissions = $this->permissions_array;
        return in_array($permission, $permissions);
    }

    public function canManageLoans()
    {
        return $this->hasPermission('manage_loans') || $this->isSuperAdmin();
    }

    public function canManageUsers()
    {
        return $this->hasPermission('manage_users') || $this->isSuperAdmin();
    }

    public function canApproveLoans()
    {
        return $this->hasPermission('approve_loans') || $this->isSuperAdmin() || $this->isAdmin();
    }
}