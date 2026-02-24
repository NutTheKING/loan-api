<?php
namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationApiController extends BaseController
{
    public function index(Request $request)
    {
        try {
            $rows = DB::table('notifications')
                ->orderBy('created_at', 'desc')
                ->limit($request->input('limit', 20))
                ->get()
                ->map(function ($n) {
                    return [
                        'id' => $n->id,
                        'type' => $n->type,
                        'data' => json_decode($n->data, true),
                        'read_at' => $n->read_at,
                        'created_at' => $n->created_at,
                    ];
                });

            return $this->success($rows, 'Notifications retrieved');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
