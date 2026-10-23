<?php

namespace App\Http\Controllers\Api\V2\News;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StoreNewsRequest;
use App\Http\Requests\UpdateNewsRequest;
use App\Http\Resources\Admin\NewsResource;
use App\Http\Resources\NewsApiResourceCollection;
use App\Models\News;
use App\Models\PremiumToUsers;
use App\Services\PremiumApiService;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NewsApiController extends Controller
{
    use MediaUploadingTrait;

    /**
     * @OA\Get(
     *     path="/v2/newss",
     *     operationId="getV2NewsList",
     *     tags={"News API"},
     *     summary="Get news list",
     *     description="Returns news items grouped by creation date.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="News fetched successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="group_date", type="string", example="25-05-2026"),
     *                     @OA\Property(
     *                         property="data",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="title", type="string", example="New IELTS batch opened"),
     *                             @OA\Property(property="description", type="string", example="Registration is now available for the next group."),
     *                             @OA\Property(property="link", type="string", nullable=true, example="https://example.com/form"),
     *                             @OA\Property(property="status", type="boolean", example=true),
     *                             @OA\Property(property="button_text", type="string", nullable=true, example="Join now"),
     *                             @OA\Property(property="images", type="string", nullable=true, example="https://example.com/storage/1/news-cover.jpg"),
     *                             @OA\Property(property="web_image", type="string", nullable=true, example="https://example.com/storage/1/news-cover.jpg"),
     *                             @OA\Property(property="created_time", type="string", example="14:35")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=402,
     *         description="User is not premium",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="UnPremium")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     )
     * )
     */
    public function index()
    {


        $news=News::with('qabul')->orderBy('order_id')->get();
        $grouped = $news->groupBy(function($item) {
            return $item->created_at->format('d-m-Y');
        });
        // Ma'lumotlarni kerakli formatda qayta ishlash
        $formatted = $grouped->map(function ($items, $date) {
            return [
                'group_date' => $date,
                'data' => $items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'title' => $item->title,
                        'description' => $item->description,
                        'link' => $item->link ?? null,
                        'status' => $item->application_button,
                        'button_text' => $item->button_text,
                        'images' =>$item->image ? $item->image->url : null,
                        'web_image' =>$item->web_image ? $item->web_image->url : null,
                        'created_time' => $item->created_at->format('H:i'),
                    ];
                })->values(),
            ];
        })->values();

        return \response()->json(['data' => $formatted]);
//        return  NewsResource::collection(News::with('qabul')->orderByDesc('id')->get());
//        return new NewsApiResourceCollection(News::with('qabul')->orderByDesc('id')->get());
    }

    /**
     * @OA\Get(
     *     path="/v2/newss/show/{id}",
     *     operationId="getV2NewsShow",
     *     tags={"News API"},
     *     summary="Get single news item",
     *     description="Returns the details of a selected news item.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="News id",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="News item fetched successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="title", type="string", example="New IELTS batch opened"),
     *             @OA\Property(property="description", type="string", example="Registration is now available for the next group."),
     *             @OA\Property(property="link", type="string", nullable=true, example="https://example.com/form"),
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="video_link", type="string", nullable=true, example="https://www.youtube.com/watch?v=abc123"),
     *             @OA\Property(property="images", type="string", nullable=true, example="https://example.com/storage/1/news-cover.jpg"),
     *             @OA\Property(property="created_time", type="string", example="14:35")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="News item not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="errors", type="string", example="Yangilik mavjud emas")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $news=News::find($id);
        if ($news){

        return new NewsResource($news);

        }else{
            return response()->json([
                'success'=>false,
                'errors'=>"Yangilik mavjud emas"
            ]);
        }
    }

}
