<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|string|email|unique:users,email',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Поле "Электронная почта" обязательно для заполнения',
            'email.string' => 'Необходимо указать корректное значение для поля "Электронная почта"',
            'email.email' => 'Поле "Электронная почта" должно содержать корректный адрес электронной почты',
            'email.unique' => 'Пользователь с указанным адресом электронной почты уже существует',
        ];
    }
}