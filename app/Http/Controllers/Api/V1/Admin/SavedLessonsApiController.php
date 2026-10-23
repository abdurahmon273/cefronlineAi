<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSavedLessonRequest;
use App\Http\Requests\UpdateSavedLessonRequest;
use App\Http\Resources\Admin\SavedLessonResource;
use App\Http\Resources\Admin\UserResource;
use App\Http\Resources\Admin\VideoResource;
use App\Http\Resources\SavedLessonsApiResourseCollection;
use App\Models\News;
use App\Models\SavedLesson;
use App\Models\User;
use App\Models\Video;
use Carbon\Carbon;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SavedLessonsApiController extends Controller
{
    public function index()
    {
        $savesLesson=SavedLesson::with(['video' => function ($query) {
            $query->withCount('videoWatched');
        }])->where('user_id',Auth::id())->where('saved',1)->orderByDesc('id')->get();

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

//        if (!$savesLesson){
//            return response()->json(['message' => 'Resource not found'], 404);
//        }

//        return new SavedLessonsApiResourseCollection($savesLesson);
    }

    public function store(StoreSavedLessonRequest $request,$id)
    {
        if (Video::where('id',$id)->first()) {
            $saved=SavedLesson::where('video_id',$id)->where('user_id',Auth::id())->first();
            if ($saved) {
                $s=$request->saved;
                $saved->update([
                    'saved'=>$s,
                    'liked'=>$request->liked,
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
                'liked' => $request->liked,
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

    public function show(SavedLesson $savedLesson)
    {
        abort_if(Gate::denies('saved_lesson_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new SavedLessonResource($savedLesson->load(['user', 'video']));
    }

    public function update(UpdateSavedLessonRequest $request, SavedLesson $savedLesson)
    {
        $savedLesson->update($request->all());

        return (new SavedLessonResource($savedLesson))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(SavedLesson $savedLesson)
    {
        abort_if(Gate::denies('saved_lesson_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $savedLesson->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
