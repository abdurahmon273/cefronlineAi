<?php

namespace App\Http\Requests;

use App\Models\SavedLesson;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateSavedLessonRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('saved_lesson_edit');
    }

    public function rules()
    {
        return [
            'user_id' => [
                'required',
                'integer',
            ],
            'video_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
