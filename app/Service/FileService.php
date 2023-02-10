<?php

namespace App\Service;

use App\Jobs\Auth\StoreUserJob;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FileService
{
    public function upload(array $data)
    {
        try {
            DB::beginTransaction();
            $user = Auth::user();

            dd($user);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
        }
    }
}