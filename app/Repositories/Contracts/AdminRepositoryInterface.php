<?php
// app/Repositories/Contracts/AdminRepositoryInterface.php
namespace App\Repositories\Contracts;

interface AdminRepositoryInterface
{
    public function all();
    public function find($id);
    public function findByEmail($email);
    public function findByUserName($user_name);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function paginate($perPage = 15);
    public function updateStatus($id, $status);
    public function getByRole($role);
    public function search($query, $perPage = 15);

}