<?php
// app/Http/Resources/Api/V1/User/UserResource.php
namespace App\Http\Resources\Api\V1\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'full_name' => $this->first_name,
            'user_name' => $this->user_name,
            'email' => $this->email,
            'profile' => $this->profile,
            'ip_address' => $this->ip_address,
            'device' => $this->device,
            'credit_score' => $this->credit_score,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}