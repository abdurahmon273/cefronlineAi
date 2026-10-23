<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroySavedLessonRequest;
use App\Http\Requests\StoreSavedLessonRequest;
use App\Http\Requests\UpdateSavedLessonRequest;
use App\Models\SavedLesson;
use App\Models\User;
use App\Models\Video;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SavedLessonsController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('saved_lesson_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $savedLessons = SavedLesson::with(['user', 'video'])->get();

        return view('admin.savedLessons.index', compact('savedLessons'));
    }

    public function create()
    {
        abort_if(Gate::denies('saved_lesson_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $videos = Video::pluck('link', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.savedLessons.create', compact('users', 'videos'));
    }

    public function store(StoreSavedLessonRequest $request)
    {
        $savedLesson = SavedLesson::create($request->all());

        return redirect()->route('admin.saved-lessons.index');
    }

    public function edit(SavedLesson $savedLesson)
    {
        abort_if(Gate::denies('saved_lesson_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $videos = Video::pluck('link', 'id')->prepend(trans('global.pleaseSelect'), '');

        $savedLesson->load('user', 'video');

        return view('admin.savedLessons.edit', compact('savedLesson', 'users', 'videos'));
    }

    public function update(UpdateSavedLessonRequest $request, SavedLesson $savedLesson)
    {
        $savedLesson->update($request->all());

        return redirect()->route('admin.saved-lessons.index');
    }

    public function show(SavedLesson $savedLesson)
    {
        abort_if(Gate::denies('saved_lesson_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $savedLesson->load('user', 'video');

        return view('admin.savedLessons.show', compact('savedLesson'));
    }

    public function destroy(SavedLesson $savedLesson)
    {
        abort_if(Gate::denies('saved_lesson_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $savedLesson->delete();

        return back();
    }

    public function massDestroy(MassDestroySavedLessonRequest $request)
    {
        $savedLessons = SavedLesson::find(request('ids'));

        foreach ($savedLessons as $savedLesson) {
            $savedLesson->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
