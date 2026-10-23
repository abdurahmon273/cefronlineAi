<?php

namespace App\Http\Requests\Admin;

use App\Models\Promocode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PromoCodeCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type'       => ['required', Rule::in(array_keys(Promocode::TYPES))],
            'start_date' => ['required', 'date'],
            'end_date'   => ['required', 'date', 'after_or_equal:start_date'],
            'discount'   => ['required', 'integer', 'min:1', 'max:100'],

            'plans'      => ['required_without:packages', 'array'],
            'plans.*'    => ['integer', 'exists:plans,id'],

            'packages'   => ['required_without:plans', 'array'],
            'packages.*' => ['integer', 'exists:packages,id'],

            'amount'     => ['required', 'integer', 'min:1', 'max:1000'],
        ];
    }

}
