<?php
namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\BaseController;
use Illuminate\Http\Request;
use App\Models\Role;

class PermissionApiController extends BaseController
{
    public function index(Request $request)
    {
        try {
            $roles = Role::with('modules')->get()->map(function($r) {
                return [
                    'id' => $r->id,
                    'name' => $r->name,
                    'key' => $r->key,
                    'modules' => $r->modules->map(fn($m) => ['id'=>$m->id,'name'=>$m->name,'key'=>$m->key])->values(),
                ];
            });

            return $this->success($roles, 'Roles and permissions retrieved');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
