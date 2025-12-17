<?php
// app/Http/Requests/Api/V1/Admin/LoginRequest.php
namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_name' => 'required|string',
            'email' => 'sometimes|string|email',
            'password' => 'required|string',
            'device_name' => 'sometimes|string',
        ];
    }
}