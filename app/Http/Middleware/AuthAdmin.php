<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('/admin/login');
        }

        $user = Auth::user();

        if (
            $user->is_admin ||
            $user->roles()->whereIn('id', [1, 3, 4])->exists()
        ) {
            return $next($request);
        }

        abort(403, 'Admin huquqi yo‘q');


    }
}
