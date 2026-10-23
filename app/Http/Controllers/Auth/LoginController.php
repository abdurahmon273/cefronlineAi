<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Login sahifasi
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Login qilish (email + role tekshiruvi)
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::query()
            ->where('email', $request->email)
            ->whereHas('roles', function ($q) {
                $q->whereIn('id', [
                    1, // admin
                    3,4  // course_creator , extra_admin
                ]);
            })
            ->first();

        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user, $request->boolean('remember'));

            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors([
            'email' => 'Email yoki parol noto‘g‘ri',
        ])->withInput($request->only('email'));
    }

    /**
     * Logout
     */
    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
