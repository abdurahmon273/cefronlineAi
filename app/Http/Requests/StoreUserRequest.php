<?php

namespace App\Http\Requests;

use App\Models\User;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('user_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'min:2',
                'max:30',
                'required',
            ],
            'surname' => [
//                'string',
//                'min:2',
//                'max:30',
//                'required',
            ],
            'email' => [
                'nullable',
                'email',
                'required_without:mobil_number',
                Rule::unique('users', 'email')->whereNull('deleted_at'),
            ],
            'password' => [
                'required',
            ],
            'roles.*' => [
                'integer',
            ],
            'roles' => [
                'required',
                'array',
            ],
            'birth_date' => [
//                'required',
//                'date_format:' . config('panel.date_format'),
            ],
            'gender' => [
//                'required',
            ],
            'image' => [
//                'required',
            ],
            'mobil_number' => [
                'nullable',
                'required_without:email',
                'regex:/^\+\d{12}$/',
                Rule::unique('users', 'mobil_number')->whereNull('deleted_at'),
            ],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'email' => $this->filled('email') ? trim((string) $this->email) : null,
            'mobil_number' => $this->filled('mobil_number') ? trim((string) $this->mobil_number) : null,
        ]);
    }
}
