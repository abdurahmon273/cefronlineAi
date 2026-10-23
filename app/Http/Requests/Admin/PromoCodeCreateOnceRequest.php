<?php

namespace App\Http\Requests\Admin;

use App\Models\Promocode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class PromoCodeCreateOnceRequest extends FormRequest
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
            'discount'   => ['required', 'integer', 'min:0', 'max:100'],
            'plans'      => ['nullable', 'array'],
            'plans.*'    => ['integer', 'exists:plans,id'],
            'packages'   => ['nullable', 'array'],
            'packages.*' => ['integer', 'exists:packages,id'],
            'code'       => ['required', 'min:8', 'max:12', 'unique:promocodes,code'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $plans = $this->input('plans', []);
            $packages = $this->input('packages', []);

            if (empty($plans) && empty($packages)) {
                $validator->errors()->add('plans', 'Plan yoki Package dan kamida bittasi tanlanishi kerak.');
                $validator->errors()->add('packages', 'Plan yoki Package dan kamida bittasi tanlanishi kerak.');
            }
        });
    }
}
