<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyLessonRequest;
use App\Http\Requests\StoreLessonRequest;
use App\Http\Requests\StoreVideoRequest;
use App\Http\Requests\UpdateLessonRequest;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Video;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class LessonController extends Controller
{
    use MediaUploadingTrait;

    public function index2($id)
    {
        abort_if(Gate::denies('lesson_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $lessons = Lesson::with(['course', 'media'])
            ->where('course_id', $id)
            ->orderBy('order_id')
            ->get();

        return view('admin.lessons.index', compact('lessons', 'id'));
    }

    public function create2($id)
    {
        abort_if(Gate::denies('lesson_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.lessons.create', ['course_id' => $id]);
    }

    public function store(StoreLessonRequest $request)
    {
        $lesson = Lesson::create($request->all());

        $this->handleMedia($lesson, $request->input('files', []), 'files');

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $lesson->id]);
        }

        return redirect()->route('admin.lessons.index2', ['id' => $lesson->course_id]);
    }

    public function store2(StoreVideoRequest $request)
    {

        $video = Video::create($request->only(['link', 'description', 'lesson_id']));

        $this->handleMedia($video, $request->input('files', []), 'files');

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $video->id]);
        }

        return redirect()->route('admin.lessons.create2', ['id' => $video->lesson_id]);
    }

    public function edit(Lesson $lesson)
    {
        abort_if(Gate::denies('lesson_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.lessons.edit', compact('lesson'));
    }

    public function update(UpdateLessonRequest $request, Lesson $lesson)
    {
        $lesson->update($request->only(['name', 'course_id', 'status']));

        $this->syncMedia($lesson, $request->input('files', []));

        return redirect()->route('admin.lessons.index2', ['id' => $lesson->course_id]);
    }

    public function show(Lesson $lesson)
    {
        abort_if(Gate::denies('lesson_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $lesson->load(['course', 'video']);

        return view('admin.lessons.show', compact('lesson'));
    }

    public function destroy(Lesson $lesson)
    {
        abort_if(Gate::denies('lesson_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $lesson->delete();

        return back();
    }

    public function massDestroy(MassDestroyLessonRequest $request)
    {
        Lesson::whereIn('id', $request->input('ids', []))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('lesson_create') && Gate::denies('lesson_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model = new Lesson();
        $model->id = $request->input('crud_id', 0);
        $model->exists = true;
        $media = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }

    private function handleMedia($model, array $files, $collection)
    {
        foreach ($files as $file) {
            $model->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection($collection);
        }
    }

    private function syncMedia($model, array $files)
    {
        $existingMedia = $model->files ? $model->files->pluck('file_name')->toArray() : [];

        foreach ($model->files as $media) {
            if (!in_array($media->file_name, $files)) {
                $media->delete();
            }
        }

        foreach ($files as $file) {
            if (!in_array($file, $existingMedia)) {
                $model->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('files');
            }
        }
    }
}
