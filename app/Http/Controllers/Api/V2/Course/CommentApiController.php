<?php

namespace App\Http\Controllers\Api\V2\Course;

use App\Http\Controllers\Api\V2ApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\V2\Course\CommentResource;
use App\Models\Comment;
use App\Models\Video;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CommentApiController extends V2ApiController
{
    /**
     * @OA\Get(
     *     path="/v2/comments/{id}",
     *     operationId="getV2VideoComments",
     *     tags={"Courses API"},
     *     summary="Get comments by video",
     *     description="Returns comments for the selected video.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Video id",
     *         @OA\Schema(type="integer", example=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comments fetched successfully",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="message", type="string", example="Commentlar keldi"),
     *                     @OA\Property(
     *                         property="data",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=8),
     *                             @OA\Property(property="user_id", type="integer", nullable=true, example=9),
     *                             @OA\Property(property="fullname", type="string", example="Ali Valiyev"),
     *                             @OA\Property(property="date", type="string", format="date", example="2026-05-25"),
     *                             @OA\Property(property="image", type="string", example="https://example.com/storage/users/avatar.jpg"),
     *                             @OA\Property(property="comment", type="string", example="Very helpful lesson")
     *                         )
     *                     )
     *                 ),
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="success", type="boolean", example=false),
     *                     @OA\Property(property="errors", type="string", example="Comments mavjud emas")
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     )
     * )
     */
    public function index($id)
    {
        $comments=Comment::with(['video' => function ($query) {
            $query->withCount('videoWatched');
        }, 'user'])->where('video_id', $id)->get();

        if ($comments->isNotEmpty()) {

        return $this->success(CommentResource::collection($comments) ,'Commentlar keldi');

        }else{
            return response()->json([
                'success' => false,
                'errors' => 'Comments mavjud emas',
            ]);
        }
    }

    /**
     * @OA\Post(
     *     path="/v2/comments-store/{id}",
     *     operationId="storeV2VideoComment",
     *     tags={"Courses API"},
     *     summary="Store comment for a video",
     *     description="Creates a new comment for the given video using the authenticated user.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Video id",
     *         @OA\Schema(type="integer", example=15)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"comment"},
     *             @OA\Property(property="comment", type="string", example="This explanation was clear and easy to follow.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comment stored",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="success", type="boolean", example=true),
     *                     @OA\Property(property="message", type="string", example="Comment jo'natildi"),
     *                     @OA\Property(
     *                         property="comment",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=8),
     *                         @OA\Property(property="user_id", type="integer", nullable=true, example=9),
     *                         @OA\Property(property="fullname", type="string", example="Ali Valiyev"),
     *                         @OA\Property(property="date", type="string", format="date", example="2026-05-25"),
     *                         @OA\Property(property="image", type="string", example="https://example.com/storage/users/avatar.jpg"),
     *                         @OA\Property(property="comment", type="string", example="This explanation was clear and easy to follow.")
     *                     )
     *                 ),
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="success", type="boolean", example=false),
     *                     @OA\Property(property="errors", type="string", example="Malumot yaratilmadi")
     *                 ),
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="success", type="boolean", example=false),
     *                     @OA\Property(property="errors", type="string", example="Bu video mavjud emas")
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="comment", type="array", @OA\Items(type="string", example="The comment field is required."))
     *             )
     *         )
     *     )
     * )
     */
    public function store(StoreCommentRequest $request, $id)
    {
        $video=Video::where('id', $id)->first();
        if ($video){
            $comment = Comment::create([
                'comment' => $request->comment,
                'video_id' => $id,
                'user_id' => Auth::id(),
            ]);
            if ($comment) {
                return \response()->json([
                    'success' => true,
                    'message'=>"Comment jo'natildi",
                    'comment' => new CommentResource($comment)
                ]);
            } else {
                return \response()->json([
                    'success' => false,
                    'errors' => 'Malumot yaratilmadi'
                ]);
            }
        }else{
            return response()->json([
                'success' => false,
                'errors' => 'Bu video mavjud emas',
            ]);
        }

    }

}
