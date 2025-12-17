<?php
// app/Http/Resources/Api/V1/Admin/AdminResource.php
namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => $this->role,
            'role_formatted' => ucfirst(str_replace('_', ' ', $this->role)),
            'permissions' => $this->permissions_array,
            'is_active' => (bool) $this->is_active,
            'is_super_admin' => $this->isSuperAdmin(),
            'is_admin' => $this->isAdmin(),
            'is_loan_officer' => $this->isLoanOfficer(),
            'email_verified_at' => $this->email_verified_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}