<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;



class AuthController extends Controller
{
    /**
     * @OA\Get(
     *     path="/test",
     *     summary="Test API",
     *     tags={"Test"},
     *     @OA\Response(
     *         response=200,
     *         description="Success"
     *     )
     * )
     */
    public function test()
    {
        return response()->json([
            'message' => 'Swagger ishladi'
        ]);
    }
}
