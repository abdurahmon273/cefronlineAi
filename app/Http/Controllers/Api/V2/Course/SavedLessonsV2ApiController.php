<?php

namespace App\Http\Controllers\Api\V2\Course;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSavedLessonRequest;
use App\Http\Requests\UpdateSavedLessonRequest;
use App\Http\Resources\Admin\UserResource;
use App\Http\Resources\Admin\VideoResource;
use App\Http\Resources\SavedLessonsApiResourseCollection;
use App\Http\Resources\V2\Course\SavedLessonResource;
use App\Models\News;
use App\Models\SavedLesson;
use App\Models\User;
use App\Models\Video;
use Carbon\Carbon;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SavedLessonsV2ApiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/v2/saved-lessons",
     *     operationId="getV2SavedLessons",
     *     tags={"Courses API"},
     *     summary="Get saved videos",
     *     description="Returns the authenticated user's saved videos grouped by save date. Requires bearer token.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Saved lessons fetched successfully",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(
     *                         property="data",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="group_date", type="string", example="24-05-2026"),
     *                             @OA\Property(
     *                                 property="data",
     *                                 type="array",
     *                                 @OA\Items(
     *                                     type="object",
     *                                     @OA\Property(property="id", type="integer", example=12),
     *                                     @OA\Property(property="saved", type="string", example="Saqlangan"),
     *                                     @OA\Property(property="liked", type="string", example="Like bor"),
     *                                     @OA\Property(property="created_at", type="string", format="date", example="2026-05-24"),
     *                                     @OA\Property(
     *                                         property="video",
     *                                         type="object",
     *                                         @OA\Property(property="id", type="integer", example=15),
     *                                         @OA\Property(property="status", type="boolean", example=true),
     *                                         @OA\Property(property="name", type="string", example="Lesson 1 Video"),
     *                                         @OA\Property(property="link", type="string", example="https://www.youtube.com/watch?v=abc123"),
     *                                         @OA\Property(property="description", type="string", example="Introduction video"),
     *                                         @OA\Property(property="lesson", type="string", example="Introduction"),
     *                                         @OA\Property(property="course", type="string", example="IELTS Foundation Course"),
     *                                         @OA\Property(property="lesson_id", type="integer", example=4),
     *                                         @OA\Property(property="course_id", type="integer", example=1),
     *                                         @OA\Property(property="saved", type="boolean", example=true),
     *                                         @OA\Property(property="liked", type="boolean", example=true),
     *                                         @OA\Property(property="view_count", type="integer", example=24),
     *                                         @OA\Property(
     *                                             property="files",
     *                                             type="array",
     *                                             @OA\Items(
     *                                                 type="object",
     *                                                 @OA\Property(property="original_url", type="string", example="https://example.com/storage/video/file.pdf")
     *                                             )
     *                                         )
     *                                     )
     *                                 )
     *                             )
     *                         )
     *                     )
     *                 ),
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="messages", type="string", example="saqlanganlar mavjud emas")
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
     *     )
     * )
     */
    public function index()
    {
        try {
            $savesLesson=SavedLesson::with(['video' => function ($query) {
                $query->withCount('videoWatched');
            }])->where('user_id',auth('sanctum')->id())->where('saved',1)->orderByDesc('id')->get();

            if ($savesLesson->count()>0){
                $grouped = $savesLesson->groupBy(function($item) {
                    return $item->created_at->format('d-m-Y');
                });
                // Ma'lumotlarni kerakli formatda qayta ishlash
                $formatted = $grouped->map(function ($items, $date) {
                    return [
                        'group_date' => $date,
                        'data' => $items->map(function ($item) {
                            $video=$item->video;
                            return [
                                'id'=>$item->id,
                                'saved'=>$item->saved ? 'Saqlangan' : 'Saqlanmagan',
                                'liked'=>$item->liked ? 'Like bor' : 'Like yoq',
                                'video'=>$video ? new VideoResource($video): null,
                                'created_at' => $item->created_at->toDateString(),
//                        'user'=>new UserResource($user),
                            ];
                        })->values(),
                    ];
                })->values();

                return \response()->json(['data' => $formatted]);
            }else{
                return \response()->json(['messages' => 'saqlanganlar mavjud emas']);
            }

        }catch (\Exception $exception){
            Log::channel('telegram')->error(" Api error:\n\n ".$this->escapeForTelegram($exception->getMessage()));

            return \response()->json(['messages' => 'saqlanganlar mavjud emas,xatolik mavjud ']);

        }


//        if (!$savesLesson){
//            return response()->json(['message' => 'Resource not found'], 404);
//        }

//        return new SavedLessonsApiResourseCollection($savesLesson);
    }

    /**
     * @OA\Post(
     *     path="/v2/saved-lessons-store/{id}",
     *     operationId="storeV2SavedLesson",
     *     tags={"Courses API"},
     *     summary="Save or unsave a video",
     *     description="Creates or updates the authenticated user's saved video state for a specific video.",
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
     *             @OA\Property(property="saved", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Save status updated",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="success", type="boolean", example=true),
     *                     @OA\Property(property="message", type="string", example="Video saqlanganlarga saqlandi"),
     *                     @OA\Property(
     *                         property="data",
     *                         type="object",
     *                         @OA\Property(property="success", type="boolean", example=true),
     *                         @OA\Property(property="id", type="integer", example=12),
     *                         @OA\Property(property="saved", type="string", example="Saqlangan"),
     *                         @OA\Property(
     *                             property="video",
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=15),
     *                             @OA\Property(property="status", type="boolean", example=true),
     *                             @OA\Property(property="name", type="string", example="Lesson 1 Video"),
     *                             @OA\Property(property="link", type="string", example="https://www.youtube.com/watch?v=abc123"),
     *                             @OA\Property(property="description", type="string", example="Introduction video"),
     *                             @OA\Property(property="lesson", type="string", example="Introduction"),
     *                             @OA\Property(property="course", type="string", example="IELTS Foundation Course"),
     *                             @OA\Property(property="lesson_id", type="integer", example=4),
     *                             @OA\Property(property="course_id", type="integer", example=1),
     *                             @OA\Property(property="saved", type="boolean", example=true),
     *                             @OA\Property(property="view_count", type="integer", example=24)
     *                         ),
     *                         @OA\Property(property="created_at", type="string", format="date", example="2026-05-24")
     *                     )
     *                 ),
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="success", type="boolean", example=true),
     *                     @OA\Property(property="message", type="string", example="Video saqlanganlardan ochirildi")
     *                 ),
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="success", type="boolean", example=true),
     *                     @OA\Property(property="message", type="string", example="Saqlanganlarga tushdi"),
     *                     @OA\Property(
     *                         property="data",
     *                         type="object",
     *                         @OA\Property(property="success", type="boolean", example=true),
     *                         @OA\Property(property="id", type="integer", example=12),
     *                         @OA\Property(property="saved", type="string", example="Saqlangan"),
     *                         @OA\Property(
     *                             property="video",
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=15),
     *                             @OA\Property(property="status", type="boolean", example=true),
     *                             @OA\Property(property="name", type="string", example="Lesson 1 Video"),
     *                             @OA\Property(property="link", type="string", example="https://www.youtube.com/watch?v=abc123"),
     *                             @OA\Property(property="description", type="string", example="Introduction video"),
     *                             @OA\Property(property="lesson", type="string", example="Introduction"),
     *                             @OA\Property(property="course", type="string", example="IELTS Foundation Course"),
     *                             @OA\Property(property="lesson_id", type="integer", example=4),
     *                             @OA\Property(property="course_id", type="integer", example=1),
     *                             @OA\Property(property="saved", type="boolean", example=true),
     *                             @OA\Property(property="view_count", type="integer", example=24)
     *                         ),
     *                         @OA\Property(property="created_at", type="string", format="date", example="2026-05-24")
     *                     )
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
     *         response=404,
     *         description="Video not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="errors", type="string", example="Video mavjud emas")
     *         )
     *     )
     * )
     */
    public function store(StoreSavedLessonRequest $request,$id)
    {
        if (Video::where('id',$id)->first()) {
            $saved=SavedLesson::where('video_id',$id)->where('user_id',Auth::id())->first();
            if ($saved) {
                $s=$request->saved;
                $saved->update([
                    'saved'=>$s,
                    'video_id'=>$id
                ]);

                if ($s==1) {
                    return \response()->json([
                        'success'=>true,
                        'message'=>'Video saqlanganlarga saqlandi',
                        'data' => new SavedLessonResource($saved)
                    ]);
                }else{
                    return \response()->json([
                        'success'=>true,
                        'message'=>'Video saqlanganlardan ochirildi',
                    ]);
                }

            }else{

            $savedLesson = SavedLesson::create([
                'user_id' => Auth::id(),
                'video_id' => $id,
                'saved' => $request->saved,
            ]);
            if ($savedLesson) {
                return response()->json([
                    'success' => true,
                    "message" => "Saqlanganlarga tushdi",
                    'data' => new SavedLessonResource($savedLesson)
                ]);
            } else
                return response()->json([
                    'success' => false,
                    'errors' => "Malumot yaratilmadi"
                ]);
                }
        }else{
            return response()->json([
                'success' => false,
                'errors' => "Video mavjud emas"
            ]);
        }
    }



    public function escapeForTelegram($text)
    {
        $specials = ['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'];

        foreach ($specials as $char) {
            $text = str_replace($char, ' ' . $char, $text);
        }

        return $text;
    }
}
