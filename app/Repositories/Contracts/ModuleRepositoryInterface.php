<?php
// app/Repositories/Contracts/LoanRepositoryInterface.php
namespace App\Repositories\Contracts;

interface ModuleRepositoryInterface
{
    public function all();
    public function getModulesByRole($roleId);

}