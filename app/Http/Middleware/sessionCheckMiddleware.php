<?php

namespace App\Http\Middleware;

use App\Models\PremiumToUsers;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class sessionCheckMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user()) {
            $user = auth()->user();
            $sessionId = session()->getId();

            if (!( $user->session_id == $sessionId) ) {

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('user.dashboard')->with('error','You are logged out, because you are using another device!');
            }
        }

        return $next($request);
    }
}
