<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Http\Resources\Admin\CourseResource;
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

    public function index()
    {
        abort_if(Gate::denies('course_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $existingPlan = PremiumToUsers::where('user_id', auth()->user()->id)
            ->where(function ($query) {
                $query->where('start_time', '<=', now())
                    ->where('end_time', '>=', now());
            })
            ->orderByDesc('id')
            ->first();

        if (!$existingPlan) {
            $premiumApi = new PremiumApiService();
            return $premiumApi->unPremium();
        }

        return CourseResource::collection(Course::with(['lessons.video'])->where('published',1)->orderBy('order_id')->get());
    }

    public function store(StoreCourseRequest $request)
    {
        $course = Course::create($request->all());

        if ($request->input('image', false)) {
            $course->addMedia(storage_path('tmp/uploads/' . basename($request->input('image'))))->toMediaCollection('image');
        }

        return (new CourseResource($course))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
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


    public function show(Course $course)
    {
        abort_if(Gate::denies('course_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new CourseResource($course);
    }

    public function update(UpdateCourseRequest $request, Course $course)
    {
        $course->update($request->all());

        if ($request->input('image', false)) {
            if (! $course->image || $request->input('image') !== $course->image->file_name) {
                if ($course->image) {
                    $course->image->delete();
                }
                $course->addMedia(storage_path('tmp/uploads/' . basename($request->input('image'))))->toMediaCollection('image');
            }
        } elseif ($course->image) {
            $course->image->delete();
        }

        return (new CourseResource($course))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Course $course)
    {
        abort_if(Gate::denies('course_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $course->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
