<?php
// app/Repositories/Contracts/LoanRepositoryInterface.php
namespace App\Repositories\Contracts;

interface LoanRepositoryInterface
{
    public function all();
    public function find($id);
    public function findWithRelations($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function paginate($perPage = 15);
    public function getUserLoans($userId, $perPage = 15);
    public function getLoansByStatus($status, $perPage = 15);
    public function search($query, $perPage = 15);
    public function approve($id, $adminId, $data = []);
    public function reject($id, $adminId);
    public function disburse($id, $adminId, $data = []);
    public function getOverdueLoans($perPage = 15);
    public function getActiveLoans($perPage = 15);
    public function getStatistics($period = 'monthly');
    public function getLoanStats($userId = null);
    public function filter($filters, $perPage = 15);
}