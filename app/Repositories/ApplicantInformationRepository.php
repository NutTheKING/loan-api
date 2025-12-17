<?php
namespace App\Repositories;

use App\Models\ApplicantInformation;
use App\Repositories\Contracts\ApplicantInformationRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ApplicantInformationRepository implements ApplicantInformationRepositoryInterface
{
    protected $model;

    public function __construct(ApplicantInformation $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function find($id): ?ApplicantInformation
    {
        return $this->model->find($id);
    }

    public function create(array $data): ApplicantInformation
    {
        return $this->model->create($data);
    }

    public function update($id, array $data): bool
    {
        return $this->model->find($id)->update($data);
    }

    public function delete($id): bool
    {
        return $this->model->find($id)->delete();
    }

    // public function findByLoanId(int $loanId): ?ApplicantInformation
    // {
    //     return $this->model->where('loan_id', $loanId)->first();
    // }
}