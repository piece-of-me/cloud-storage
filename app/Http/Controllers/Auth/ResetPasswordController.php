<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Jobs\Auth\ResetPasswordJob;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    public function __invoke(ResetPasswordRequest $request)
    {
        $data = $request->validated();
        ResetPasswordJob::dispatch($data);

        return response()->json([
            'success' => true,
        ]);
    }
}
