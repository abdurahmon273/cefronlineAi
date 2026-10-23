<?php

namespace App\Services;

use App\Models\Promocode;
use Illuminate\Database\QueryException;

class PremiumApiService
{
    public function unPremium()
    {
        return response()->json([
            'success'  => false,
            'message' => 'UnPremium',
        ], 402);
    }
}
