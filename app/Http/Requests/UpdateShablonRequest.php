<?php

namespace App\Http\Requests;

use App\Models\Shablon;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateShablonRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('shablon_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            'description' => [
                'required',
            ],
        ];
    }
}
