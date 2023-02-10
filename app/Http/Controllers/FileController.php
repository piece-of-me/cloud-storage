<?php

namespace App\Http\Controllers;

use App\Http\Requests\File\UploadRequest;
use App\Models\File;
use App\Models\User;
use App\Service\FileService;
use Illuminate\Support\Facades\Auth;

class FileController extends Controller
{
    private FileService $service;

    public function __construct(FileService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
//        $user = User::find(Auth::user()->id);
//        $files = $user->files;
        dd('upload file');
    }

    public function upload(UploadRequest $request)
    {
//        composer require tymon/jwt-auth:"dev-develop"
        $data = $request->validated();
        $this->service->upload($data);
    }
}
