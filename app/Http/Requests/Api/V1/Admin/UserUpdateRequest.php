<?php
// app/Http/Requests/Api/V1/Admin/UserUpdateRequest.php
namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user') ? $this->route('user')->id : null;

        return [
            'first_name' => 'sometimes|string|max:50',
            'last_name' => 'sometimes|string|max:50',
            'email' => 'sometimes|string|email|max:100|unique:users,email,' . $userId,
            'phone' => 'sometimes|string|max:20|unique:users,phone,' . $userId,
            'password' => 'sometimes|string|min:8',
            'user_type' => 'sometimes|in:individual,business',
            'id_number' => 'sometimes|string|unique:users,id_number,' . $userId,
            'date_of_birth' => 'sometimes|date',
            'address' => 'sometimes|string|max:500',
            'employment_status' => 'sometimes|in:employed,self_employed,unemployed,student',
            'monthly_income' => 'sometimes|numeric|min:0',
            'bank_name' => 'sometimes|string|max:100',
            'account_number' => 'sometimes|string|max:50',
            'status' => 'sometimes|in:active,inactive,suspended',
        ];
    }
}