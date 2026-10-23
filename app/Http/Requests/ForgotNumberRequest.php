<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ForgotNumberRequest extends FormRequest
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
                'mobile_number' => null,
                'email' => strtolower($raw),
            ]);

            return;
        }

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
            'mobile_number_or_email' => 'required|string',
            'type' => 'required|in:0,1',

            'mobile_number' => [
                'nullable',
                'required_if:type,1',
                'string',
                'size:13',
                'starts_with:+998',
                'exists:users,mobil_number',
            ],

            'email' => [
                'nullable',
                'required_if:type,0',
                'email',
                'exists:users,email',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'mobile_number_or_email.required' => 'Telefon raqam yoki email kiriting',

            'mobile_number.required_if' => 'Telefon raqam kiriting',
            'mobile_number.size' => 'Telefon raqam formati noto‘g‘ri',
            'mobile_number.starts_with' => 'Telefon raqam formati noto‘g‘ri',
            'mobile_number.exists' => 'Telefon raqam topilmadi',

            'email.required_if' => 'Email kiriting',
            'email.email' => 'Email formati noto‘g‘ri',
            'email.exists' => 'Email topilmadi',
        ];
    }
}
