<?php

namespace App\Http\Requests\Admin;

use App\Models\Promocode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PromoCodeEditRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id') ?? $this->route('promocode');

        return [
            'type'       => ['required', Rule::in(array_keys(Promocode::TYPES))],
            'start_date' => ['required', 'date'],
            'end_date'   => ['required', 'date', 'after_or_equal:start_date'],
            'discount'   => ['required', 'integer', 'min:0', 'max:100'],

            'plans'      => ['required_without:packages', 'array', 'min:1'],
            'plans.*'    => ['integer', 'exists:plans,id'],

            'packages'   => ['required_without:plans', 'array', 'min:1'],
            'packages.*' => ['integer', 'exists:packages,id'],

            'code'       => [
                'required',
                'string',
                'max:64',
                Rule::unique('promocodes', 'code')
                    ->ignore($id)
                    ->whereNull('deleted_at'),
            ],
        ];
    }
}
