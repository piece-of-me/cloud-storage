<?php

namespace App\Http\Controllers;

class MainIndexController extends Controller
{
    public function __invoke()
    {
        return view('main');
    }
}
