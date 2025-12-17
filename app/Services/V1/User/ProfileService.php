<?php
// app/Services/V1/User/ProfileService.php
namespace App\Services\V1\User;

use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileService
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getProfile($userId)
    {
        return $this->userRepository->find($userId);
    }

    public function updateProfile($userId, array $data)
    {
        $user = $this->userRepository->find($userId);
        
        // Fields that cannot be updated via profile update
        $restrictedFields = ['email', 'id_number', 'status', 'user_type'];
        foreach ($restrictedFields as $field) {
            if (isset($data[$field])) {
                unset($data[$field]);
            }
        }
        
        return $this->userRepository->update($userId, $data);
    }

    public function updateContactInfo($userId, array $data)
    {
        $user = $this->userRepository->find($userId);
        
        $updateData = [];
        
        if (isset($data['phone'])) {
            $existingPhone = $this->userRepository->findByPhone($data['phone']);
            if ($existingPhone && $existingPhone->id != $userId) {
                throw ValidationException::withMessages([
                    'phone' => ['Phone number already in use by another account.'],
                ]);
            }
            $updateData['phone'] = $data['phone'];
        }
        
        if (isset($data['address'])) {
            $updateData['address'] = $data['address'];
        }
        
        if (!empty($updateData)) {
            return $this->userRepository->update($userId, $updateData);
        }
        
        return $user;
    }

    public function updateEmploymentInfo($userId, array $data)
    {
        $allowedFields = ['employment_status', 'monthly_income', 'bank_name', 'account_number'];
        $updateData = array_intersect_key($data, array_flip($allowedFields));
        
        if (!empty($updateData)) {
            return $this->userRepository->update($userId, $updateData);
        }
        
        return $this->userRepository->find($userId);
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

    public function deactivateAccount($userId, $reason)
    {
        return $this->userRepository->update($userId, [
            'status' => 'inactive',
        ]);
    }
}