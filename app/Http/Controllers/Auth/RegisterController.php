<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Jobs\Auth\StoreUserJob;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function __invoke(RegisterRequest $request)
    {
        $data = $request->validated();
        StoreUserJob::dispatch($data);

        return response()->json([
            'success' => true,
        ]);
    }
}
