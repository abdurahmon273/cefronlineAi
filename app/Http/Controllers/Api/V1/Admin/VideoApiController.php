<?php

namespace App\Http\Controllers\Api\V1\Admin;

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

    public function index($id)
    {
        abort_if(Gate::denies('video_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return VideoResource::collection(
            Video::with(['lesson', 'savedLesson'])
                ->withCount('videoWatched')
                ->where('lesson_id', $id)
                ->orderBy('order_id')
                ->get()
        );
    }

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

    public function store(StoreVideoRequest $request)
    {
        $video = Video::create($request->all());

        foreach ($request->input('files', []) as $file) {
            $video->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('files');
        }

        return (new VideoResource($video->loadCount('videoWatched')))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Video $video)
    {
        abort_if(Gate::denies('video_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new VideoResource($video->load(['lesson','savedLesson'])->loadCount('videoWatched'));
    }

    public function update(UpdateVideoRequest $request, Video $video)
    {
        $video->update($request->all());

        if (count($video->files) > 0) {
            foreach ($video->files as $media) {
                if (! in_array($media->file_name, $request->input('files', []))) {
                    $media->delete();
                }
            }
        }
        $media = $video->files->pluck('file_name')->toArray();
        foreach ($request->input('files', []) as $file) {
            if (count($media) === 0 || ! in_array($file, $media)) {
                $video->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('files');
            }
        }

        return (new VideoResource($video->loadCount('videoWatched')))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Video $video)
    {
        abort_if(Gate::denies('video_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $video->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
