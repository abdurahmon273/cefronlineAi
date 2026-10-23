<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SwaggerLoginController extends Controller
{
    private const SWAGGER_ADMIN_EMAIL = 'swageradmin@gmail.com';
    private const SESSION_KEY = 'swagger_authenticated_user_id';

    public function showLoginForm(Request $request)
    {
        if ($this->hasValidSwaggerSession($request)) {
            return redirect('/api/documentation');
        }

        return view('auth.swagger-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($request->email !== self::SWAGGER_ADMIN_EMAIL) {
            return back()
                ->withErrors(['email' => 'To‘g‘ri kelmadi'])
                ->withInput($request->only('email'));
        }

        $user = User::query()
            ->where('email', self::SWAGGER_ADMIN_EMAIL)
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return back()
                ->withErrors(['email' => 'To‘g‘ri kelmadi'])
                ->withInput($request->only('email'));
        }

        $request->session()->put(self::SESSION_KEY, $user->id);
        $request->session()->regenerate();

        return redirect('/api/documentation');
    }

    public function logout(Request $request)
    {
        $request->session()->forget(self::SESSION_KEY);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('swagger.login');
    }

    private function hasValidSwaggerSession(Request $request): bool
    {
        $userId = $request->session()->get(self::SESSION_KEY);

        if (! $userId) {
            return false;
        }

        return User::query()
            ->whereKey($userId)
            ->where('email', self::SWAGGER_ADMIN_EMAIL)
            ->exists();
    }
}
