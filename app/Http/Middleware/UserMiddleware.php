<?php
// app/Http/Middleware/UserMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user() || $request->user()->tokenCan('user') === false) {
            return response()-> json([
                'success' => false,
                'message' => 'Unauthorized. User access required.',
                'timestamp' => now()->toISOString(),
                'version' => 'v1',
            ], 401);
        }

        return $next($request);
    }
}