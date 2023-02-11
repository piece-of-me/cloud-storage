<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $request)
    {
        $data = $request->validated();

        if (Auth::attempt($data)) {
            $request->session()->regenerate();
            return response()->json([
                'success' => true,
            ])->header('TOKEN', Auth::user()->createToken("API TOKEN")->plainTextToken);
        }

        return response()->json([
            'email' => ['Пользователь с указанными данными не найден'],
        ], 422);
    }
}
