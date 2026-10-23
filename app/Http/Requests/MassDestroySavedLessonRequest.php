<?php

namespace App\Http\Requests;

use App\Models\SavedLesson;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroySavedLessonRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('saved_lesson_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:saved_lessons,id',
        ];
    }
}
