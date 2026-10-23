<?php

namespace App\Http\Controllers\Api\V2\News;

use App\Http\Controllers\Controller;
use App\Http\Resources\QabulApiResourse;
use App\Models\News;
use App\Models\Qabul;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QabulApiController extends Controller
{

    /**
     * @OA\Post(
     *     path="/v2/newss/join/{news}",
     *     operationId="postV2NewsJoin",
     *     tags={"News API"},
     *     summary="Join news application",
     *     description="Creates a join/application record for the selected news item using the authenticated barier auth token  if available.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="news",
     *         in="path",
     *         required=true,
     *         description="News id",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Join record created",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="success", type="boolean", example=true),
     *                     @OA\Property(
     *                         property="data",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=15),
     *                         @OA\Property(property="news_id", type="integer", example=1),
     *                         @OA\Property(property="user_id", type="integer", nullable=true, example=9)
     *                     )
     *                 ),
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="success", type="boolean", example=false),
     *                     @OA\Property(property="errors", type="string", example="Yangilikni idsi kelmadi")
     *                 )
     *             }
     *         )
     *     )
     * )
     */

    public function join($id)
    {
        $news=News::where('id',$id)->first();
        if ($news){
            $qabul=Qabul::create([
                'news_id' => $news->id,
                'user_id'=>Auth::id()
            ]);
        return response()->json([
            'success' => true,
            'data'=>New QabulApiResourse($qabul)
        ]);
        }else{
            return response()->json([
                'success' => false,
                'errors'=>'Yangilikni idsi kelmadi'
            ]);
        }

    }

}
