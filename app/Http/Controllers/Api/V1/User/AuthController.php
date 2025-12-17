<?php
// app/Http/Controllers/Api/V1/User/AuthController.php
namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Requests\Api\V1\User\RegisterRequest;
use App\Http\Requests\Api\V1\User\LoginRequest;
use App\Services\V1\User\AuthService;
use Illuminate\Http\Request;

class AuthController extends BaseController
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Register a new user
     */
    public function register(RegisterRequest $request)
    {
        try {
            $result = $this->authService->register($request->validated());
            return $this->created($result, 'Registration successful');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationError($e->errors());
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Login user
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
     * Logout user
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
     * Get user profile
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
     * Refresh token
     */
    public function refresh(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            $token = $request->user()->createToken('mobile-user-refresh')->plainTextToken;
            
            return $this->success([
                'user' => $request->user(),
                'token' => $token,
            ], 'Token refreshed successfully');
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
}