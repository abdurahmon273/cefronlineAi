<?php

namespace App\Http\Requests\Api\V2\Profile;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateApiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'       => 'required|string|max:255',
            'surname'    => 'required|string|max:255',
            'birth_date' => 'nullable|date',
            'city_id'    => 'nullable|integer|exists:cities,id',
            'image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'    => 'Ism kiritilmadi',
            'surname.required' => 'Familya kiritilmadi',
            'birth_date.date'  => 'Tug\'ilgan sana noto\'g\'ri',
            'city_id.integer'  => 'Shahar ID son bo\'lishi kerak',
            'city_id.exists'   => 'Shahar topilmadi',
            'image.max'        => 'Rasm hajmi 2 MB dan oshmasligi kerak',
            'image.image'      => 'Fayl rasm bo\'lishi kerak',
            'image.mimes'      => 'Rasm faqat jpg, jpeg, png yoki webp formatida bo\'lishi kerak',
        ];
    }
}
