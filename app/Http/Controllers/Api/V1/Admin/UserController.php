<?php
// app/Http/Controllers/Api/V1/Admin/UserController.php
namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Requests\Api\V1\Admin\UserUpdateRequest;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;

class UserController extends BaseController
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Get all users
     */
    public function index(Request $request)
    {
        try {
            $users = $this->userRepository->paginate(
                $request->input('per_page', 15)
            );
            return $this->paginated($users, 'Users retrieved successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Get user details
     */
    public function show($id)
    {
        try {
            $user = $this->userRepository->find($id);
            $loans = $this->userRepository->getUserLoans($id);
            
            return $this->success([
                'user' => $user,
                'loans' => $loans,
            ], 'User details retrieved successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Update user
     */
    public function update(UserUpdateRequest $request, $id)
    {
        try {
            $user = $this->userRepository->update($id, $request->validated());
            return $this->success($user, 'User updated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationError($e->errors());
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Delete user
     */
    public function destroy($id)
    {
        try {
            $this->userRepository->delete($id);
            return $this->success([], 'User deleted successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Update user status
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:active,inactive,suspended',
            ]);

            $user = $this->userRepository->updateStatus($id, $request->status);
            return $this->success($user, 'User status updated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationError($e->errors());
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Get users with loans
     */
    public function withLoans(Request $request)
    {
        try {
            $users = $this->userRepository->getUsersWithLoans(
                $request->input('per_page', 15)
            );
            return $this->paginated($users, 'Users with loans retrieved successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Search users
     */
    public function search(Request $request)
    {
        try {
            $request->validate([
                'query' => 'required|string|min:2',
            ]);

            $users = $this->userRepository->search(
                $request->query,
                $request->input('per_page', 15)
            );

            return $this->paginated($users, 'Search results');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationError($e->errors());
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Get user statistics
     */
    public function statistics($id)
    {
        try {
            $stats = $this->userRepository->getUserStats($id);
            return $this->success($stats, 'User statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}