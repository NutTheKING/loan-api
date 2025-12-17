<?php
// app/Repositories/Contracts/UserRepositoryInterface.php
namespace App\Repositories\Contracts;

interface UserRepositoryInterface
{
    public function all();
    public function find($id);
    public function findByEmail($email);
    public function findByUserName($user_name);
    public function findByPhone($phone);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function paginate($perPage = 15);
    public function search($query, $perPage = 15);
    public function getUserLoans($userId);
    public function updateStatus($id, $status);
    public function getUsersWithLoans($perPage = 15);
    public function getUserByCredentials($email, $password);
}