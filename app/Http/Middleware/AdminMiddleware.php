<?php
// app/Http/Middleware/AdminMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    { 
        Log::info($request->user());
        if (!$request->user() || $request->user()->tokenCan('admin') === false) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.',
                'timestamp' => now()->toISOString(),
                'version' => 'v1',
            ], 401);
        }

        return $next($request);
    }
       
}