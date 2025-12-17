<?php
// app/Http/Requests/Api/V1/User/ProfileRequest.php
namespace App\Http\Requests\Api\V1\User;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user() ? $this->user()->id : null;

        return [
            'first_name' => 'sometimes|string|max:50',
            'last_name' => 'sometimes|string|max:50',
            'phone' => 'sometimes|string|max:20|unique:users,phone,' . $userId,
            'address' => 'sometimes|string|max:500',
            'employment_status' => 'sometimes|in:employed,self_employed,unemployed,student',
            'monthly_income' => 'sometimes|numeric|min:0',
            'bank_name' => 'sometimes|string|max:100',
            'account_number' => 'sometimes|string|max:50',
        ];
    }
}