<?php
// app/Services/V1/Admin/AuthService.php
namespace App\Services\V1\Admin;

use App\Models\Module;
use App\Repositories\Contracts\AdminRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AuthService
{
    protected $adminRepository;

    public function __construct(AdminRepositoryInterface $adminRepository)
    {
        $this->adminRepository = $adminRepository;
    }

    // register
    public function register(array $data):array{
   // Check if user already exists
        $existingUserName = $this->adminRepository->findByUserName($data['user_name']);
        if ($existingUserName) {
            throw ValidationException::withMessages([
                'user_name' => ['UserName already registered.'],
            ]);
        }


        // Create user
        $user = $this->adminRepository->create($data);

        // Generate token
        $token = $user->createToken('admin-user-' . Str::random(10))->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function login(array $data): array
    {
        // Support login by user_name or email
        $admin = null;
        if (!empty($data['user_name'])) {
            $admin = $this->adminRepository->findByUserName($data['user_name']);
        }
        if (!$admin && !empty($data['email'])) {
            $admin = $this->adminRepository->findByEmail($data['email']);
        }
        // $modules = ; 
       
        if (!$admin || !Hash::check($data['password'], $admin->password)) {
            throw ValidationException::withMessages([
                'user_name' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$admin->is_active) {
            throw ValidationException::withMessages([
                'user_name' => ['Your account has been deactivated. Please contact system administrator.'],
            ]);
        }

        // Revoke old tokens
        $admin->tokens()->delete();

        // Generate new token
        $token = $admin->createToken('admin-dashboard' . Str::random(10))->plainTextToken;

        return [
                'admin' => $admin,
                'admin_type' => $admin->admin_type,
                'modules_base_role' =>  $admin->role->modules,
                'token' => $token,
                'token_type' => 'Bearer'
        ];
    }

    public function logout($admin)
    {
        return $admin->currentAccessToken()->delete();
    }

    public function getProfile($adminId)
    {
        return $this->adminRepository->find($adminId);
    }

    public function updateProfile($adminId, array $data)
    {
        $admin = $this->adminRepository->find($adminId);
        
        // Remove sensitive fields
        unset($data['email'], $data['role'], $data['permissions']);
        
        return $this->adminRepository->update($adminId, $data);
    }

    public function changePassword($adminId, $currentPassword, $newPassword)
    {
        $admin = $this->adminRepository->find($adminId);
        
        if (!Hash::check($currentPassword, $admin->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Current password is incorrect.'],
            ]);
        }
        
        return $this->adminRepository->update($adminId, [
            'password' => $newPassword,
        ]);
    }
}