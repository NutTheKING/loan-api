<?php
// app/Http/Controllers/Api/V1/User/ProfileController.php
namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Api\V1\BaseController;
use App\Services\V1\User\ProfileService;
use Illuminate\Http\Request;

class ProfileController extends BaseController
{
    protected $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    /**
     * Get user profile
     */
    public function show(Request $request)
    {
        try {
            $profile = $this->profileService->getProfile($request->user()->id);
            return $this->success($profile, 'Profile retrieved successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }


    /**
     * Update contact information
     */
    public function updateContact(Request $request)
    {
        try {
            $request->validate([
                'phone' => 'sometimes|string|max:20',
                'address' => 'sometimes|string|max:500',
            ]);

            $profile = $this->profileService->updateContactInfo(
                $request->user()->id,
                $request->only(['phone', 'address'])
            );
            
            return $this->success($profile, 'Contact information updated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationError($e->errors());
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Update employment information
     */
    public function updateEmployment(Request $request)
    {
        try {
            $request->validate([
                'employment_status' => 'sometimes|in:employed,self_employed,unemployed,student',
                'monthly_income' => 'sometimes|numeric|min:0',
                'bank_name' => 'sometimes|string|max:100',
                'account_number' => 'sometimes|string|max:50',
            ]);

            $profile = $this->profileService->updateEmploymentInfo(
                $request->user()->id,
                $request->only(['employment_status', 'monthly_income', 'bank_name', 'account_number'])
            );
            
            return $this->success($profile, 'Employment information updated successfully');
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

            $this->profileService->changePassword(
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