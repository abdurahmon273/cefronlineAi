<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ApiLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $raw = trim((string) $this->input('email_or_mobile_number'));

        // Email bo‘lsa
        if (preg_match('/[a-zA-Z@]/', $raw)) {

            $this->merge([
                'type' => 0,

                'mobile_number' => null,

                'email' => strtolower($raw),
            ]);

            return;
        }

        // Telefon bo‘lsa
        $digits = preg_replace('/\D+/', '', $raw);

        if (strlen($digits) > 9) {
            $digits = substr($digits, -9);
        }

        $normalized = strlen($digits) === 9
            ? ('+998' . $digits)
            : $digits;

        $this->merge([
            'type' => 1,

            'mobile_number' => $normalized,

            'email' => null,
        ]);
    }

    public function rules(): array
    {
        return [

            'email_or_mobile_number' => 'required',

            'type' => 'required|in:0,1',

            'mobile_number' => 'required_if:type,1|nullable|regex:/^\+998\d{9}$/',

            'email' => 'required_if:type,0|nullable|email',

            'password' => 'required|string|min:6',

            'device_id' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [

            'email_or_mobile_number.required' =>
                'Email yoki telefon raqam kiritilmadi',

            'mobile_number.required_if' =>
                'Telefon raqam kiritilmadi',

            'mobile_number.regex' =>
                'Telefon raqam noto‘g‘ri formatda',

            'email.required_if' =>
                'Email kiritilmadi',

            'email.email' =>
                'Email format noto‘g‘ri',

            'password.required' =>
                'Parol kiritilmadi',

            'password.min' =>
                'Parol kamida 6 ta belgidan iborat bo‘lishi kerak',
        ];
    }
}
