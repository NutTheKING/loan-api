<?php
// app/Repositories/LoanRepository.php
namespace App\Repositories;

use App\Models\Module;
use App\Repositories\Contracts\ModuleRepositoryInterface;

class ModuleRepository implements ModuleRepositoryInterface
{
    protected $model;

    public function __construct(Module $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function getModulesByRole($roleId)
    {
        return $this->model->where('role_id', $roleId)->get();
    }
}