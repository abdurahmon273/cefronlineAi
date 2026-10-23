<?php

namespace App\Http\Middleware;

use App\Models\PremiumToUsers;
use Closure;
use Illuminate\Http\Request;
use Stevebauman\Location\Facades\Location;
use Symfony\Component\HttpFoundation\Response;

class PremiumExpired
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
            $today = now();

            $existingPlan = PremiumToUsers::where('user_id', $user->id)
                ->where(function ($query) use ($today) {
                    $query->where('start_time', '<=', $today)
                        ->where('end_time', '>=', $today);
                })
                ->orderByDesc('id')
                ->first();
            if (!$existingPlan) {
                return redirect()->route('user.market')->with('error','Upgrade your plan!');
            }
        }

        return $next($request);
    }
}
