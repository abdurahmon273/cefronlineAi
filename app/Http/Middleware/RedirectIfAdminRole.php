<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectIfAdminRole
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if ($user) {
            // roles() relationship bor deb hisoblayapman
            $isAdminLike = $user->roles()
                ->whereIn('id', [1, 3, 4]) // admin, course_creator, extra_admin
                ->exists();

            if ($isAdminLike) {
                return redirect()->route('admin.dashboard'); // yoki redirect('/admin')
            }
        }

        return $next($request);
    }
}
