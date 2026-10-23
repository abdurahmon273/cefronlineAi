<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UserRegisterApiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $raw = trim((string) $this->input('mobile_number_or_email'));

        if (preg_match('/[a-zA-Z@]/', $raw)) {
            $this->merge([
                'type' => 0,
                'email' => strtolower($raw),
                'mobile_number' => null,
            ]);

            return;
        }

        $digits = preg_replace('/\D+/', '', $raw);

        if (strlen($digits) > 9) {
            $digits = substr($digits, -9);
        }

        $normalized = strlen($digits) === 9 ? '+998' . $digits : $digits;

        $this->merge([
            'type' => 1,

            'mobile_number' => $normalized,

            'email' => null,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',

            'mobile_number_or_email' => 'required|string',
            'type' => 'required|in:0,1',

            'mobile_number' => [
                'nullable',
                'required_if:type,1',
                'string',
                'size:13',
                'starts_with:+998',
            ],


            'email' => [
                'nullable',
                'required_if:type,0',
                'email',
            ],

            'password' => 'required|string|min:6|confirmed',

            'birth_date' => 'nullable|date',
            'gender' => 'nullable',
            'city_id' => 'nullable|exists:cities,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Ism kiritilmadi',
            'surname.required' => 'Familya kiritilmadi',

            'mobile_number_or_email.required' => 'Telefon raqam yoki email kiriting',

            'mobile_number.required_if' => 'Telefon raqam kiriting',
            'mobile_number.size' => 'Telefon raqam noto‘g‘ri',
            'mobile_number.starts_with' => 'Telefon raqam noto‘g‘ri',
            'mobile_number.unique' => 'Bu telefon raqam allaqachon mavjud',

            'email.required_if' => 'Email kiriting',
            'email.email' => 'Email formati noto‘g‘ri',
            'email.unique' => 'Bu email allaqachon mavjud',

            'password.required' => 'Parol kiritilmadi',
            'password.min' => "Parol 6 tadan ko'p bo'lishi kerak",
            'password.confirmed' => "Parol bir xil bo'lishi kerak",

            'birth_date.date' => 'Tug‘ilgan sana noto‘g‘ri',
            'city_id.exists' => 'Shaxar mavjud emas',

            'image.image' => 'Rasm noto‘g‘ri formatda',
            'image.mimes' => 'Rasm jpg, jpeg, png yoki webp bo‘lishi kerak',
            'image.max' => 'Rasm hajmi katta',
        ];
    }
}
