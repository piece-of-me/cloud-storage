<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Jobs\Auth\ResetPasswordJob;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    public function index()
    {
        return view('auth.reset');
    }

    public function reset(ResetPasswordRequest $request)
    {
        $data = $request->validated();
        ResetPasswordJob::dispatch($data);

        return view('auth.reset_success', $data);
    }
}
