<?php
// app/Http/Controllers/Api/V1/Admin/AuthController.php
namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Requests\Api\V1\Admin\LoginRequest;
use App\Http\Requests\Api\V1\Admin\RegisterRequest;
use App\Services\V1\Admin\AuthService;
use Illuminate\Http\Request;

class AuthController extends BaseController
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }


    /**
     * Register admin
     */
    public function register(RegisterRequest $request)
    {
        try {
            $result = $this->authService->register($request->validated());
            return $this->success($result, 'Register successful');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationError($e->errors());
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Login admin
     */
    public function login(LoginRequest $request)
    {
        try {
            $result = $this->authService->login($request->validated());
            return $this->success($result, 'Login successful');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationError($e->errors());
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Logout admin
     */
    public function logout(Request $request)
    {
        try {
            $this->authService->logout($request->user());
            return $this->success([], 'Logged out successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Get admin profile
     */
    public function profile(Request $request)
    {
        try {
            $profile = $this->authService->getProfile($request->user()->id);
            return $this->success($profile, 'Profile retrieved successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Update admin profile
     */
    public function update(Request $request)
    {
        try {
            $request->validate([
                'name' => 'sometimes|string|max:255',
                'phone' => 'sometimes|string|max:20',
            ]);

            $profile = $this->authService->updateProfile(
                $request->user()->id,
                $request->only(['name', 'phone'])
            );
            
            return $this->success($profile, 'Profile updated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationError($e->errors());
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        try {
            $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed',
            ]);

            $this->authService->changePassword(
                $request->user()->id,
                $request->current_password,
                $request->new_password
            );

            return $this->success([], 'Password changed successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationError($e->errors());
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Refresh token
     */
    public function refresh(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            $token = $request->user()->createToken('admin-dashboard-refresh')->plainTextToken;
            
            return $this->success([
                'admin' => $request->user(),
                'token' => $token,
            ], 'Token refreshed successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}