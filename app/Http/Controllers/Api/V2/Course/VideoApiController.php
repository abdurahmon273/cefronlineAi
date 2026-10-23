<?php

namespace App\Http\Controllers\Api\V2\Course;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StoreVideoRequest;
use App\Http\Requests\UpdateVideoRequest;
use App\Http\Resources\Admin\VideoResource;
use App\Models\Video;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VideoApiController extends Controller
{
    use MediaUploadingTrait;

    /**
     * @OA\Get(
     *     path="/v2/videos/{id}",
     *     operationId="getV2VideosByLesson",
     *     tags={"Courses API"},
     *     summary="Get lesson videos",
     *     description="Returns all videos that belong to the selected lesson.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Lesson id",
     *         @OA\Schema(type="integer", example=4)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Videos fetched successfully",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=15),
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="name", type="string", example="Lesson 1 Video"),
     *                 @OA\Property(property="link", type="string", example="https://www.youtube.com/watch?v=abc123"),
     *                 @OA\Property(property="description", type="string", example="Introduction video"),
     *                 @OA\Property(property="lesson", type="string", example="Introduction"),
     *                 @OA\Property(property="course", type="string", example="IELTS Foundation Course"),
     *                 @OA\Property(property="lesson_id", type="integer", example=4),
     *                 @OA\Property(property="course_id", type="integer", example=1),
     *                 @OA\Property(property="saved", type="boolean", example=true),
     *                 @OA\Property(property="liked", type="boolean", example=false),
     *                 @OA\Property(property="view_count", type="integer", example=24),
     *                 @OA\Property(
     *                     property="files",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="original_url", type="string", example="https://example.com/storage/video/file.pdf")
     *                     )
     *                 )
     *             )
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
        return VideoResource::collection(
            Video::with(['lesson', 'savedLesson'])
                ->withCount('videoWatched')
                ->where('lesson_id', $id)
                ->orderBy('order_id')
                ->get()
        );
    }

//    /**
//     * @OA\Get(
//     *     path="/v2/info/{id}",
//     *     operationId="getV2VideoInfo",
//     *     tags={"Courses API"},
//     *     summary="Get single video details",
//     *     description="Returns the detailed information for a single video. Requires bearer token.",
//     *     security={{"bearerAuth":{}}},
//     *     @OA\Parameter(
//     *         name="id",
//     *         in="path",
//     *         required=true,
//     *         description="Video id",
//     *         @OA\Schema(type="integer", example=15)
//     *     ),
//     *     @OA\Response(
//     *         response=200,
//     *         description="Video details fetched",
//     *         @OA\JsonContent(
//     *             oneOf={
//     *                 @OA\Schema(
//     *                     type="object",
//     *                     @OA\Property(property="success", type="boolean", example=true),
//     *                     @OA\Property(property="message", type="string", example="videolar keldi"),
//     *                     @OA\Property(property="id", type="integer", example=15),
//     *                     @OA\Property(property="status", type="boolean", example=true),
//     *                     @OA\Property(property="description", type="string", example="Introduction video"),
//     *                     @OA\Property(property="link", type="string", example="https://www.youtube.com/watch?v=abc123"),
//     *                     @OA\Property(property="lesson_id", type="integer", example=4),
//     *                     @OA\Property(
//     *                         property="files",
//     *                         type="array",
//     *                         @OA\Items(
//     *                             type="object",
//     *                             @OA\Property(property="uuid", type="string", example="b1a2c3d4"),
//     *                             @OA\Property(property="original_url", type="string", example="https://example.com/storage/video/file.pdf")
//     *                         )
//     *                     )
//     *                 ),
//     *                 @OA\Schema(
//     *                     type="object",
//     *                     @OA\Property(property="success", type="boolean", example=false),
//     *                     @OA\Property(property="errors", type="string", example="video not found")
//     *                 )
//     *             }
//     *         )
//     *     ),
//     *     @OA\Response(
//     *         response=401,
//     *         description="Unauthenticated"
//     *     )
//     * )
//     */
    public function info($id)
    {
        $video=Video::find($id);
        if ($video) {
            return response()->json([
                'success' => true,
                "message"=>'videolar keldi',
                'id' => $video->id,
                'status' => $video->status,
                'description' => $video->description,
                'link' => $video->link,
                'lesson_id' => $video->lesson_id,
                'files' => $video->files,
            ]);
        }else{
            return response()->json([
                'success' => false,
                'errors' => 'video not found',
            ]);
        }
    }

}
