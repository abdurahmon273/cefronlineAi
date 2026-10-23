<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UserRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $raw = trim((string) $this->input('mobile_number_or_email'));

        // Email bo‘lsa
        if (preg_match('/[a-zA-Z@]/', $raw)) {
            $this->merge([
                'type' => 0,
                'mobile_number' => null,
                'email' => strtolower($raw),
            ]);
            return;
        }

        // Phone bo‘lsa
        $digits = preg_replace('/\D+/', '', $raw);

        if (strlen($digits) > 9) {
            $digits = substr($digits, -9);
        }

        $normalized = strlen($digits) === 9 ? ('+998' . $digits) : $digits;

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

            'mobile_number_or_email' => 'required|string',
            'type' => 'required|in:0,1',

            // PHONE
            'mobile_number' => [
                'nullable',
                'required_if:type,1',
                'string',
                'size:13',
                'starts_with:+998',
            ],

            // EMAIL
            'email' => [
                'nullable',
                'required_if:type,0',
                'email',
            ],

            'password' => 'required|string|min:6|max:64',
            'password_confirmation' => 'required|same:password',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Ism kiritish majburiy',

            'mobile_number_or_email.required' => 'Telefon yoki email kiriting',

            'mobile_number.required_if' => 'Telefon raqam kiriting',
            'mobile_number.size' => 'Telefon raqam noto‘g‘ri',
            'mobile_number.starts_with' => 'Telefon raqam noto‘g‘ri',
            'mobile_number.unique' => 'Bu telefon raqam allaqachon mavjud',

            'email.required_if' => 'Email kiriting',
            'email.email' => 'Email formati noto‘g‘ri',
            'email.unique' => 'Bu email allaqachon mavjud',

            'password.required' => 'Parol kiriting',
            'password.min' => 'Parol kamida 6 ta belgidan iborat bo‘lishi kerak',

            'password_confirmation.required' => 'Parolni tasdiqlang',
            'password_confirmation.same' => 'Parollar mos emas',
        ];
    }
}
