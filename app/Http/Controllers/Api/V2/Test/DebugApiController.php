<?php

namespace App\Http\Controllers\Api\V2\Test;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stevebauman\Location\Facades\Location;

class DebugApiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/debug/ip",
     *     tags={"Test"},
     *     summary="is_uzbekistan diagnostikasi",
     *     description="So'rov bilan kelgan IP va proxy header'larni hamda ular bo'yicha aniqlangan davlatni qaytaradi. Vaqtinchalik diagnostika uchun, auth talab qilmaydi.",
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="request_ip", type="string", example="84.54.64.1"),
     *             @OA\Property(property="remote_addr", type="string", nullable=true, example="127.0.0.1"),
     *             @OA\Property(
     *                 property="proxy_headers",
     *                 type="object",
     *                 @OA\Property(property="x-forwarded-for", type="string", nullable=true, example="84.54.64.1"),
     *                 @OA\Property(property="x-real-ip", type="string", nullable=true, example="84.54.64.1"),
     *                 @OA\Property(property="cf-connecting-ip", type="string", nullable=true, example=null)
     *             ),
     *             @OA\Property(
     *                 property="location",
     *                 nullable=true,
     *                 oneOf={
     *                     @OA\Schema(
     *                         type="object",
     *                         @OA\Property(property="countryName", type="string", example="Uzbekistan"),
     *                         @OA\Property(property="countryCode", type="string", example="UZ"),
     *                         @OA\Property(property="ip", type="string", example="84.54.64.1")
     *                     ),
     *                     @OA\Schema(type="boolean", example=false)
     *                 }
     *             ),
     *             @OA\Property(property="is_uzbekistan", type="boolean", example=true)
     *         )
     *     )
     * )
     */
    public function ip(Request $request)
    {
        $ip = $request->ip();
        $location = Location::get($ip);

        return [
            'request_ip' => $ip,
            'remote_addr' => $request->server('REMOTE_ADDR'),
            'proxy_headers' => [
                'x-forwarded-for' => $request->header('X-Forwarded-For'),
                'x-real-ip' => $request->header('X-Real-IP'),
                'cf-connecting-ip' => $request->header('CF-Connecting-IP'),
            ],
            'location' => $location ? [
                'countryName' => $location->countryName,
                'countryCode' => $location->countryCode,
                'ip' => $location->ip,
            ] : false,
            'is_uzbekistan' => $location && $location->countryName == 'Uzbekistan',
        ];
    }
}
