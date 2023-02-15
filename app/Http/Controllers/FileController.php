<?php

namespace App\Http\Controllers;

use App\Http\Requests\File\UploadRequest;
use App\Http\Resources\FileResource;
use App\Models\File;
use App\Models\User;
use App\Service\FileService;
use Illuminate\Support\Facades\Auth;
use \Illuminate\Http\JsonResponse;

class FileController extends Controller
{
    private FileService $service;

    public function __construct(FileService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $user = Auth::user();
//        $files = $user;
       return FileResource::collection($user->files);

    }

    public function upload(UploadRequest $request): JsonResponse
    {
        $data = $request->validated();
        $success = $this->service->upload($data);
        return response()->json(['success' => $success]);
    }
}
