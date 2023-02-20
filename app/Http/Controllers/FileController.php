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
        return FileResource::collection($user->files);
    }

    public function upload(UploadRequest $request): JsonResponse
    {
        $data = $request->validated();
        $result = $this->service->upload($data);
        if ($result == null) {
            return response()->json(status: 500);
        }
        return response()->json(['data' => new FileResource($result)]);
    }
}
