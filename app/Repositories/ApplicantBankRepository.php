<?php

namespace App\Repositories;

use App\Models\ApplicantBank;
use App\Repositories\Contracts\ApplicantBankRepositoryInterface;

class ApplicantBankRepository implements ApplicantBankRepositoryInterface
{
    protected $model;
    
    public function __construct(ApplicantBank $model)
    {
        $this->model = $model;
    }
    
    public function create(array $data)
    {
        return $this->model->create($data);
    }

}