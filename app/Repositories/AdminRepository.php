<?php
// app/Repositories/AdminRepository.php
namespace App\Repositories;

use App\Models\Admin;
use App\Repositories\Contracts\AdminRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class AdminRepository implements AdminRepositoryInterface
{
    protected $model;

    public function __construct(Admin $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function findByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function create(array $data)
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $admin = $this->find($id);
        
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } elseif (isset($data['password'])) {
            unset($data['password']);
        }
        
        $admin->update($data);
        return $admin;
    }

    public function delete($id)
    {
        $admin = $this->find($id);
        return $admin->delete();
    }

    public function paginate($perPage = 15)
    {
        return $this->model->paginate($perPage);
    }

    public function updateStatus($id, $status)
    {
        $admin = $this->find($id);
        $admin->is_active = $status === 'active';
        $admin->save();
        return $admin;
    }

    public function getByRole($role)
    {
        return $this->model->where('role', $role)->get();
    }

    public function search($query, $perPage = 15)
    {
        return $this->model->where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->paginate($perPage);
    }

    
   public function findByUserName($user_name)
    {
        return $this->model->where('user_name', $user_name)->first();
    }
}