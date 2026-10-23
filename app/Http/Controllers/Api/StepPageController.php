<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StepPageResource;
use App\Models\StepPage;
use Illuminate\Http\Request;

class StepPageController extends V2ApiController
{

    /**
     * @OA\Get(
     *     path="/v2/stepPages",
     *     operationId="getStepPages",
     *     tags={"Not Auth Pages"},
     *     summary="Get all step pages",
     *     description="Returns step pages ordered by order_id",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Welcome"),
     *                 @OA\Property(property="description", type="string", example="Introduction page"),
     *                 @OA\Property(property="order_id", type="integer", example=1),
     *                 @OA\Property(
     *                     property="image_url",
     *                     type="string",
     *                     nullable=true,
     *                     example="https://example.com/storage/step-pages/image.png"
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        return StepPageResource::collection(
            StepPage::orderBy('order_id')->get()
        );
    }


}
