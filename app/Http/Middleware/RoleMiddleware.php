<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        $isTokenValid = $request->user();
        $message = !$isTokenValid ?  'Token tidak valid!' : "Hanya untuk role $role!";
        $statusCode = !$isTokenValid ? 401 : 403;

        if (!$isTokenValid || !$request->user()->hasRole($role)) {
            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => $message,
                'data' => null,
                'errors' => [
                    'code' => $isTokenValid ? 'FORBIDDEN' : 'UNAUTHENTICATED',
                    'details' => null
                ]
            ], $statusCode));
        }
        return $next($request);
    }
}
