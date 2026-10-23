<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Video;
use Illuminate\Auth\Access\Gate;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class HomeController
{
    public function index()
    {

        $user=auth()->user();
        if (!$user->can('dashboard_access')) {
            if (!$user->can('user_access')) {
                return redirect()->route('admin.courses.index')->with('success', 'ok');
            }else{
                return redirect()->route('admin.users.index')->with('success', 'ok');
            }
        }

        return view('home');
    }
}
