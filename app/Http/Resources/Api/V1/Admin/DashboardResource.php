<?php
// app/Http/Resources/Api/V1/Admin/DashboardResource.php
namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'stats' => $this->resource['stats'] ?? [],
            'analytics' => $this->resource['analytics'] ?? [],
            'recent_activities' => $this->resource['recent_activities'] ?? [],
            'performance_metrics' => $this->resource['performance_metrics'] ?? [],
            'timestamp' => now()->toISOString(),
        ];
    }

    public function with($request)
    {
        return [
            'success' => true,
            'message' => 'Dashboard data retrieved successfully',
            'version' => 'v1',
        ];
    }
}