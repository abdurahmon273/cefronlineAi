<?php

namespace App\Http\Controllers\Api\V2\Course;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StoreLessonRequest;
use App\Http\Requests\UpdateLessonRequest;
use App\Http\Resources\V2\Course\LessonResource;
use App\Models\Lesson;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LessonV2ApiController extends Controller
{
    use MediaUploadingTrait;

    /**
     * @OA\Get(
     *     path="/v2/course/show/{id}",
     *     operationId="getV2CourseShow",
     *     tags={"Courses API"},
     *     summary="Get course lessons",
     *     description="Returns all lessons that belong to the selected course.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Course id",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lessons fetched successfully",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="status", type="boolean", example=true),
     *                         @OA\Property(property="success", type="boolean", example=true),
     *                         @OA\Property(property="message", type="string", example="Darslar muvaffaqiyatli keldi"),
     *                         @OA\Property(property="id", type="integer", example=4),
     *                         @OA\Property(property="name", type="string", example="Introduction"),
     *                         @OA\Property(property="course_id", type="integer", example=1),
     *                         @OA\Property(property="files", type="string", example="https://example.com/storage/lesson/file.pdf")
     *                     )
     *                 ),
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="success", type="boolean", example=false),
     *                     @OA\Property(property="errors", type="string", example="Bunda lessonlar mavjud emas!")
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

        $lessons = Lesson::with([
            'course',
            'video' => function ($query) {
                $query->with(['lesson', 'savedLesson'])
                    ->withCount('videoWatched')
                    ->orderBy('order_id');
            },
        ])->where('course_id', $id)->orderBy('order_id')->get();

        if ($lessons->isNotEmpty()) {

            return LessonResource::collection($lessons);

        } else {
            return response()->json([
                'success' => false,
                'errors' => "Bunda lessonlar mavjud emas!"
            ]);
        }
    }


}
