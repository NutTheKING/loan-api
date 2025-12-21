<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AdminTypeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string ...$adminRoles
     * @return Response
     */
    public function handle(Request $request, Closure $next, ...$adminRoles): Response
    {
        $admin = $request->user();
         Log::warning('Unauthorized admin access attempt', $admin);
        // Check if user exists and is an admin
        if (!$admin || !$admin->isAdmin()) {
            Log::warning('Unauthorized admin access attempt', [
                'user_id' => $admin?->id,
                'ip' => $request->ip(),
                'route' => $request->route()?->getName(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Admin access required'
            ], 403);
        }

        Log::info($admin->admin->role);
        
        // Check if admin has required role
        if (!in_array($admin->admin->role, $adminRoles)) {
            Log::warning('Insufficient admin privileges', [
                'user_id' => $admin->id,
                'current_role' => $admin->admin->role,
                'required_roles' => $adminRoles,
                'ip' => $request->ip(),
                'route' => $request->route()?->getName(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Insufficient admin privileges. Required roles: ' . implode(', ', $adminRoles)
            ], 403);
        }
        
        return $next($request);
    }
}