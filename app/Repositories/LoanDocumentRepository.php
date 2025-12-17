<?php
// app/Repositories/Eloquent/LoanDocumentRepository.php

namespace App\Repositories;

use App\Models\LoanDocument;
use App\Repositories\Contracts\LoanDocumentRepositoryInterface;

class LoanDocumentRepository implements LoanDocumentRepositoryInterface
{
    protected $model;
    
    public function __construct(LoanDocument $model)
    {
        $this->model = $model;
    }
    
    public function create(array $data)
    {
        return $this->model->create($data);
    }
    
    // public function findByLoanId($loanId)
    // {
    //     return $this->model->where('loan_id', $loanId)->first();
    // }
    
    // public function updateByLoanId($loanId, array $data)
    // {
    //     $document = $this->findByLoanId($loanId);
    //     if ($document) {
    //         $document->update($data);
    //         return $document->fresh();
    //     }
    //     return null;
    // }
}