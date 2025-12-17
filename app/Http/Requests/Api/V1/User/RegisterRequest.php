<?php
// app/Http/Requests/Api/V1/User/RegisterRequest.php
namespace App\Http\Requests\Api\V1\User;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => 'required|string|max:50',
            'email' => 'required|string|email|max:100|unique:users',
            'user_name' => 'required|string|max:100|unique:users',
            'password' => 'required|string|min:8',
        ];
    }

    public function messages(): array
    {
        return [
            // 'dob.before' => 'You must be at least 18 years old.',
            // 'terms_accepted.accepted' => 'You must accept the terms and conditions.',
            // 'password.confirmed' => 'Password confirmation does not match.',
        ];
    }
}