<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|string|email|exists:users,email',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Поле "Электронная почта" обязательно для заполнения',
            'email.string' => 'Необходимо указать корректное значение для поля "Электронная почта"',
            'email.email' => 'Поле "Электронная почта" должно содержать корректный адрес электронной почты',
            'email.exists' => 'Пользователь с указанным адресом электронной почты не найден',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
