<?php
// app/Repositories/UserRepository.php
namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    protected $model;

    public function __construct(User $model)
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
    public function findByUserName($user_name)
    {
        return $this->model->where('user_name', $user_name)->first();
    }

    public function findByPhone($phone)
    {
        return $this->model->where('phone', $phone)->first();
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
        $user = $this->find($id);
        
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } elseif (isset($data['password'])) {
            unset($data['password']);
        }
        
        $user->update($data);
        return $user;
    }

    public function delete($id)
    {
        $user = $this->find($id);
        return $user->delete();
    }

    public function paginate($perPage = 15)
    {
        return $this->model->paginate($perPage);
    }

    public function search($query, $perPage = 15)
    {
        return $this->model->where(function($q) use ($query) {
            $q->where('first_name', 'like', "%{$query}%")
              ->orWhere('last_name', 'like', "%{$query}%")
              ->orWhere('email', 'like', "%{$query}%")
              ->orWhere('phone', 'like', "%{$query}%")
              ->orWhere('id_number', 'like', "%{$query}%");
        })->paginate($perPage);
    }

    public function getUserLoans($userId)
    {
        $user = $this->find($userId);
        return $user->loans()->with('repayments')->latest()->get();
    }

    public function updateStatus($id, $status)
    {
        $user = $this->find($id);
        $user->status = $status;
        $user->save();
        return $user;
    }

    public function getUsersWithLoans($perPage = 15)
    {
        return $this->model->withCount(['loans'])
            ->withSum('loans', 'amount')
            ->orderBy('loans_count', 'desc')
            ->paginate($perPage);
    }

    public function getUserByCredentials($email, $password)
    {
        $user = $this->findByEmail($email);
        
        if (!$user || !Hash::check($password, $user->password)) {
            return null;
        }
        
        return $user;
    }
}