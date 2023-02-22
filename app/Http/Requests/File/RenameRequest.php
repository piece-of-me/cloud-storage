<?php

namespace App\Http\Requests\File;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RenameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
