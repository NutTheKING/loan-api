<?php
// app/Repositories/LoanRepository.php
namespace App\Repositories;

use App\Models\Loan;
use App\Repositories\Contracts\LoanRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LoanRepository implements LoanRepositoryInterface
{
    protected $model;

    public function __construct(Loan $model)
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

    public function findWithRelations($id)
    {
        return $this->model->with(['user', 'approver', 'repayments'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $loan = $this->find($id);
        $loan->update($data);
        return $loan;
    }

    public function delete($id)
    {
        $loan = $this->find($id);
        return $loan->delete();
    }

    public function paginate($perPage = 15)
    {
        return $this->model->with(['user'])->latest()->paginate($perPage);
    }

    public function getUserLoans($userId, $perPage = 15)
    {
        return $this->model->where('user_id', $userId)
            ->with(['repayments'])
            ->latest()
            ->paginate($perPage);
    }

    public function getLoansByStatus($status, $perPage = 15)
    {
        return $this->model->where('status', $status)
            ->with(['user'])
            ->latest()
            ->paginate($perPage);
    }

    public function search($query, $perPage = 15)
    {
        return $this->model->where(function($q) use ($query) {
            $q->where('id', $query)
              ->orWhereHas('user', function($userQuery) use ($query) {
                  $userQuery->where('first_name', 'like', "%{$query}%")
                           ->orWhere('last_name', 'like', "%{$query}%")
                           ->orWhere('email', 'like', "%{$query}%")
                           ->orWhere('phone', 'like', "%{$query}%");
              });
        })->with(['user'])->paginate($perPage);
    }

    public function approve($id, $adminId, $data = [])
    {
        $loan = $this->find($id);
        
        $updateData = array_merge($data, [
            'status' => 'approved',
            'approved_by' => $adminId,
            'approved_at' => Carbon::now()->toDateString(),
        ]);
        
        $loan->update($updateData);
        return $loan;
    }

    public function reject($id, $adminId)
    {
        $loan = $this->find($id);
        
        $loan->update([
            'status' => 'rejected',
            'approved_by' => $adminId,
            'approved_at' => now(),
        ]);
        
        return $loan;
    }

    public function disburse($id, $adminId, $data = [])
    {
        $loan = $this->find($id);
        
        $updateData = array_merge($data, [
            'status' => 'approved',
            'disbursed_at' => now(),
            'start_date' => now(),
            'end_date' => now()->addMonths($loan->term_months),
            'remaining_balance' => $loan->amount + ($loan->amount * $loan->interest_rate / 100),
        ]);
        
        $loan->update($updateData);
        return $loan;
    }

    public function getOverdueLoans($perPage = 15)
    {
        return $this->model->where('status', 'active')
            ->where('end_date', '<', now())
            ->where('remaining_balance', '>', 0)
            ->with(['user'])
            ->paginate($perPage);
    }

    public function getActiveLoans($perPage = 15)
    {
        return $this->model->where('status', 'active')
            ->with(['user', 'repayments'])
            ->paginate($perPage);
    }

    public function getStatistics($period = 'monthly')
    {
        $startDate = match($period) {
            'daily' => now()->subDay(),
            'weekly' => now()->subWeek(),
            'monthly' => now()->subMonth(),
            'yearly' => now()->subYear(),
            default => now()->subMonth(),
        };
        
        $stats = $this->model->select(
            DB::raw('COUNT(*) as total_loans'),
            DB::raw('SUM(amount) as total_amount'),
            DB::raw('AVG(amount) as average_amount'),
            DB::raw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved_count'),
            DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_count'),
            DB::raw('SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected_count'),
            DB::raw('SUM(CASE WHEN status = "disbursed" THEN 1 ELSE 0 END) as disbursed_count'),
            DB::raw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_count'),
            DB::raw('SUM(CASE WHEN status = "defaulted" THEN 1 ELSE 0 END) as defaulted_count')
        )->where('created_at', '>=', $startDate)->first();
        
        return $stats ? $stats->toArray() : [];
    }

    public function getLoanStats($userId = null)
    {
        $query = $this->model;
        
        if ($userId) {
            $query = $query->where('user_id', $userId);
        }
        
        return [
            'total_loans' => $query->count(),
            'total_amount' => $query->sum('amount'),
            'average_amount' => $query->avg('amount'),
            'active_loans' => $query->whereIn('status', ['active', 'disbursed'])->count(),
            'pending_loans' => $query->where('status', 'pending')->count(),
            'overdue_loans' => $query->where('status', 'active')
                ->where('end_date', '<', now())
                ->where('remaining_balance', '>', 0)
                ->count(),
            'repaid_amount' => $query->sum('total_paid'),
            'outstanding_amount' => $query->sum('remaining_balance'),
        ];
    }

    public function filter($filters, $perPage = 15)
    {
        $query = $this->model->with(['user']);
        
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
            if (!empty($filters['member_acc_id'])) {
            $query->whereHas('user', function($q) use ($filters) {
                $q->where('user_name', $filters['member_acc_id']);
                $q->OrWhere('order_id', $filters['member_acc_id']);
            });
        }  
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        
        if (!empty($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }
        
        if (!empty($filters['min_amount'])) {
            $query->where('amount', '>=', $filters['min_amount']);
        }
        
        if (!empty($filters['max_amount'])) {
            $query->where('amount', '<=', $filters['max_amount']);
        }
        
        if (!empty($filters['purpose'])) {
            $query->where('purpose', $filters['purpose']);
        }
        
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        
        return $query->orderBy($sortBy, $sortOrder)->paginate($perPage);
    }
}