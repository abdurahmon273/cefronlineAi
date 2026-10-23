<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MarketController extends Controller
{
    public function index($type=0)
    {
        $user = auth()->user();
        $hasSubscription = $user ? (bool) $user->activePremium() : false;

        if (!$hasSubscription) {
            $type = 0;
        }

        return view('user.market', compact('type', 'hasSubscription'));
    }
}
