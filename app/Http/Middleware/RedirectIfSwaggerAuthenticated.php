<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfSwaggerAuthenticated
{
    private const SWAGGER_ADMIN_EMAIL = 'swageradmin@gmail.com';
    private const SESSION_KEY = 'swagger_authenticated_user_id';

    public function handle(Request $request, Closure $next): Response
    {
        $userId = $request->session()->get(self::SESSION_KEY);

        if (! $userId) {
            return $next($request);
        }

        $hasAccess = User::query()
            ->whereKey($userId)
            ->where('email', self::SWAGGER_ADMIN_EMAIL)
            ->exists();

        if ($hasAccess) {
            return redirect('/api/documentation');
        }

        $request->session()->forget(self::SESSION_KEY);

        return $next($request);
    }
}
