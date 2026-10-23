<?php

namespace App\Http\Requests;

use App\Models\Shablon;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreShablonRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('shablon_create');
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
