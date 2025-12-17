<?php
// app/Services/V1/User/AuthService.php
namespace App\Services\V1\User;

use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AuthService
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(array $data)
    {
        // Check if user already exists
        $existingUserName = $this->userRepository->findByUserName($data['user_name']);
        if ($existingUserName) {
            throw ValidationException::withMessages([
                'user_name' => ['UserName already registered.'],
            ]);
        }


        // Create user
        $user = $this->userRepository->create($data);

        // Generate token
        $token = $user->createToken('mobile-user-' . Str::random(10))->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function login(array $credentials)
    {
        $user = $this->userRepository->findByUserName($credentials['user_name']);

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if ($user->account_status !== 'normal') {
            throw ValidationException::withMessages([
                'email' => ['Your account is ' . $user->status . '. Please contact support.'],
            ]);
        }

        // Revoke old tokens
        $user->tokens()->delete();

        // Generate new token
        $token = $user->createToken('mobile-user-' . Str::random(10))->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function logout($user)
    {
        return $user->currentAccessToken()->delete();
    }

    public function getProfile($userId)
    {
        $user = $this->userRepository->find(id: $userId);
        // $stats = $this->userRepository->getUserStats($userId);
        
        return [
            'user' => $user,
            // 'stats' => $stats,
        ];
    }

    public function updateProfile($userId, array $data)
    {
        $user = $this->userRepository->find($userId);
        
        // Remove sensitive fields that shouldn't be updated
        unset($data['email'], $data['id_number']);
        
        return $this->userRepository->update($userId, $data);
    }

    public function changePassword($userId, $currentPassword, $newPassword)
    {
        $user = $this->userRepository->find($userId);
        
        if (!Hash::check($currentPassword, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Current password is incorrect.'],
            ]);
        }
        
        return $this->userRepository->update($userId, [
            'password' => $newPassword,
        ]);
    }
}