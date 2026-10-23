<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class AuthGates
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if ($user) {
            // Foydalanuvchi uchun unikal kesh kalitini yaratamiz
            $cacheKey = 'user_permissions_' . $user->id;

            // Rollar va ruxsatnomalarni keshdan olamiz yoki bazadan olib keshga saqlaymiz
            $permissionsData = Cache::remember($cacheKey, now()->addMinutes(60), function () use ($user) {
                // Foydalanuvchining rollarini va ruxsatnomalarini olish
                $user_roles = $user->roles()->with('permissions')->get();

                $user_roles_massiv = $user_roles->pluck('id')->toArray();
                $permissionsArray = [];

                // Har bir roldagi ruxsatnomalarni yig'amiz
                foreach ($user_roles as $role) {
                    foreach ($role->permissions as $permission) {
                        $permissionsArray[$permission->title][] = $role->id;
                    }
                }

                // Keshga saqlanadigan ma'lumotlarni qaytaramiz
                return [
                    'permissions' => $permissionsArray,
                    'roles' => $user_roles_massiv
                ];
            });

            // Keshdan olingan rollarni va ruxsatnomalarni qayta ishlaymiz
            $user_roles_massiv = $permissionsData['roles'];
            foreach ($permissionsData['permissions'] as $title => $roles) {
                Gate::define($title, function ($user) use ($roles, $user_roles_massiv) {
                    // Foydalanuvchi rollari va ruxsatnoma rollarini solishtiramiz
                    return count(array_intersect($user_roles_massiv, $roles)) > 0;
                });
            }
        }

        // So'rovni keyingi middleware orqali o'tkazamiz
        return $next($request);
    }
}
