<?php
namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\BaseController;
use Illuminate\Http\Request;

class ConfigApiController extends BaseController
{
    public function index(Request $request)
    {
        try {
            $config = [
                'app_name' => config('app.name'),
                'env' => config('app.env'),
                'timezone' => config('app.timezone'),
                'debug' => config('app.debug'),
            ];

            return $this->success($config, 'Configuration retrieved');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
