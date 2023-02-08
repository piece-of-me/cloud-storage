<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class MainIndexController extends Controller
{
    public function __invoke()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login.index');
        }

        return view('main');
    }
}
