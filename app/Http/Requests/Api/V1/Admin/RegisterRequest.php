<?php
namespace App\Http\Requests\Api\V1\Admin;

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
            'email' => 'sometimes|string|email|max:100|unique:users',
            'user_name' => 'required|string|max:100|unique:users',
            'password' => 'required|string|min:8',
            'role_id' => 'required',
            'phone' => 'required|string|min:8|max:10',
        ];
    }

    public function messages(): array
    {
        return [
        ];
    }
}