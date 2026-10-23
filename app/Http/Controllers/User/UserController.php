<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Information;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {

        return view('user.home');
    }
    public function components()
    {
        return view('user.components.components');
    }
    public function profile()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }


        return view('user.profile');
    }
    public function contact()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $informations = Information::all();

        $data = [
            'tel' => $informations->where('type', '0')->pluck('information_link')->first(),
            'url' => $informations->where('type', '1')->pluck('information_link')->first(),
            'address' => $informations->where('type', '2')->pluck('information_link')->first(),
        ];

        return view('user.contact.index', $data);
    }
    public function lessons()
    {
        return view('user.lessons.index');
    }

    public function favorite()
    {
        return view('user.favorite');
    }
    public function lessonsDetail($id)
    {
        return view('user.lessons.detail', compact('id'));
    }
    public function lesson($id, $video_id = null)
    {
        return view('user.lessons.lesson', compact('id', 'video_id'));
    }

    public function premium()
    {
        return view('user.premium');
    }


    public function AdminRedirectLogin($id)
    {
        $user = User::findOrFail($id);

        $uniq_token = (string) Str::uuid();

        $user->update([
            'session_id' => $uniq_token,
        ]);

        auth()->logout();

        $callbackUrl = URL::temporarySignedRoute(
            'telegram.callback',
            now()->addMinutes(10),
            [
                'uniq_token' => $uniq_token,
            ]
        );

        return redirect()->away($callbackUrl);
    }
}
