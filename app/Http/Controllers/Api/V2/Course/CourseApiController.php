<?php

namespace App\Http\Controllers\Api\V2\Course;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Http\Resources\V2\Course\CourseResource;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\PremiumToUsers;
use App\Models\TotalVideoView;
use App\Models\Video;
use App\Models\VideoWached;
use App\Models\VideoWatched;
use App\Services\PremiumApiService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class CourseApiController extends Controller
{
    use MediaUploadingTrait;

    /**
     * @OA\Get(
     *     path="/v2/courses",
     *     operationId="getV2Courses",
     *     tags={"Courses API"},
     *     summary="Get courses list",
     *     description="Returns the published courses list with lesson count, course image, first lesson title, and watched percentage.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Courses fetched successfully",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="success", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Kurslar keldi"),
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="IELTS Foundation Course"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2026-05-24T10:00:00.000000Z"),
     *                 @OA\Property(property="lessons_count", type="integer", example=12),
     *                 @OA\Property(property="images", type="string", example="https://example.com/storage/1/course-cover.jpg"),
     *                 @OA\Property(property="lesson_title", type="string", example="Introduction"),
     *                 @OA\Property(property="percentage", type="number", format="float", example=58.33)
     *             )
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
        return CourseResource::collection(Course::with(['lessons.video'])->where('published',1)->orderBy('order_id')->get());
    }


    /**
     * @OA\Post(
     *     path="/v2/view_video",
     *     operationId="storeV2ViewedVideo",
     *     tags={"Courses API"},
     *     summary="Mark video as watched",
     *     description="Stores a watched video record for the authenticated user, increments counters, and returns the watched percentage for the selected course.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"video_id","lesson_id","course_id"},
     *             @OA\Property(property="video_id", type="integer", example=15),
     *             @OA\Property(property="lesson_id", type="integer", example=4),
     *             @OA\Property(property="course_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Video watch saved",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="The video watch record has been saved successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=101),
     *                 @OA\Property(property="video_id", type="integer", example=15),
     *                 @OA\Property(property="course_id", type="integer", example=1),
     *                 @OA\Property(property="lesson_id", type="integer", example=4),
     *                 @OA\Property(property="user_id", type="integer", example=9),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2026-05-24T10:10:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2026-05-24T10:10:00.000000Z")
     *             ),
     *             @OA\Property(property="percentage", type="number", format="float", example=41.67)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation failed or video already watched",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             oneOf={
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="errors",
     *                         type="object",
     *                         @OA\Property(property="video_id", type="array", @OA\Items(type="string", example="Video ID is required.")),
     *                         @OA\Property(property="lesson_id", type="array", @OA\Items(type="string", example="Lesson ID is required.")),
     *                         @OA\Property(property="course_id", type="array", @OA\Items(type="string", example="Course ID is required."))
     *                     )
     *                 ),
     *                 @OA\Schema(
     *                     @OA\Property(property="errors", type="string", example="The video has already been watched.")
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function percentage(Request $request)
    {
        // Validate request data
        $validation = Validator::make($request->all(), [
            'video_id' => 'required|exists:videos,id',
            'lesson_id' => 'required|exists:lessons,id',
            'course_id' => 'required|exists:courses,id',
        ], [
            'video_id.required' => 'Video ID is required.',
            'lesson_id.required' => 'Lesson ID is required.',
            'course_id.required' => 'Course ID is required.',
            'video_id.exists' => 'The specified video does not exist.',
            'lesson_id.exists' => 'The specified lesson does not exist.',
            'course_id.exists' => 'The specified course does not exist.',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validation->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        $userId = Auth::id();

        $video=Video::find($request->input('video_id'));
        $video->update(['view_count'=>$video->view_count+1]);

        $TotalViewOfVideo=TotalVideoView::where('date',Carbon::now()->format('Y-m-d'))->first();
        if ($TotalViewOfVideo) {
            $TotalViewOfVideo->update(['view_count'=>$TotalViewOfVideo->view_count+1]);
        }else{
            TotalVideoView::create([
                'date'=>Carbon::now()->format('Y-m-d'),
                'view_count'=>1,
            ]);
        }

        // Check if the video has already been watched by the user
        $existingWatch = VideoWached::where([
            'course_id' => $request->course_id,
            'lesson_id' => $request->lesson_id,
            'video_id' => $request->video_id,
            'user_id' => $userId
        ])->first();

        if ($existingWatch) {

            return response()->json([
                'success' => false,
                'errors' => 'The video has already been watched.'
            ]);
        }

        // Save the watched video record
        $watchRecord = VideoWached::create([
            'video_id' => $request->video_id,
            'course_id' => $request->course_id,
            'lesson_id' => $request->lesson_id,
            'user_id' => $userId
        ]);

        // Calculate the percentage of videos watched in the course
        $watchedCount = VideoWached::where('course_id', $request->course_id)
            ->where('user_id', $userId)
            ->count();

        $totalVideos = Video::whereHas('lesson', function ($query) use ($request) {
            $query->where('course_id', $request->course_id);
        })->count();

        $percentage = ($totalVideos > 0) ? ($watchedCount / $totalVideos) * 100 : 0;

        return response()->json([
            'success' => true,
            'message' => 'The video watch record has been saved successfully.',
            'data' => $watchRecord,
            'percentage' => round($percentage, 2) // Rounded to two decimal places
        ]);
    }

}
