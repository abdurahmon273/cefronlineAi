<?php

namespace App\Http\Controllers\Api\V2\Profile;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\AboutUs\AddressResource;
use App\Http\Resources\Admin\AboutUs\PhoneNumberResource;
use App\Http\Resources\Admin\AboutUs\TelegramResource;
use App\Models\Information;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class AboutUsApiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/v2/informations",
     *     summary="Get contact and about information",
     *     description="Returns public contact links and address information for the authenticated profile section.",
     *     tags={"Profile"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Information list returned successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="tel",
     *                 type="array",
     *                 description="Phone numbers from information records where type = 0.",
     *                 @OA\Items(type="string", example="+998901234567")
     *             ),
     *             @OA\Property(
     *                 property="url",
     *                 type="array",
     *                 description="Social or website links from information records where type = 1.",
     *                 @OA\Items(type="string", example="https://t.me/tm_school")
     *             ),
     *             @OA\Property(
     *                 property="address",
     *                 type="array",
     *                 description="Address values from information records where type = 2.",
     *                 @OA\Items(type="string", example="https://maps.app.goo.gl/sokBWnC57YwSLSJx6")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */

    public function informations()
    {
        $informations=Information::all();

        return [
            'tel'=>$informations->where('type','0')->pluck('information_link'),
            'url'=>$informations->where('type','1')->pluck('information_link'),
            'address'=>$informations->where('type','2')->pluck('information_link'),
        ];
    }
}
