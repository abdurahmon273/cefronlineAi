<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('user_edit');
    }

    public function rules()
    {
        $userId = $this->route('user')?->id ?? $this->route('user');

        return [
            'name' => [
                'string',
                'min:2',
                'max:30',
                'required',
            ],
            'surname' => [],
            'email' => [
                'nullable',
                'email',
                'required_without:mobil_number',
                Rule::unique('users', 'email')->ignore($userId)->whereNull('deleted_at'),
            ],
            'password' => [
                'nullable',
            ],
            'roles.*' => [
                'integer',
            ],
            'roles' => [
                'required',
                'array',
            ],
            'birth_date' => [],
            'gender' => [],
            'image' => [],
            'mobil_number' => [
                'nullable',
                'required_without:email',
                'regex:/^\+\d{12}$/',
                Rule::unique('users', 'mobil_number')->ignore($userId)->whereNull('deleted_at'),
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
