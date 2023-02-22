<?php

namespace App\Http\Controllers;

use App\Http\Requests\File\CreateRequest;
use App\Http\Requests\File\RenameRequest;
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

    public function create(CreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $result = $this->service->createFolder($data);
        if (!isset($result)) {
            return response()->json(status: 500);
        }
        return response()->json(['folder' => new FileResource($result)]);
    }

    public function upload(UploadRequest $request): JsonResponse
    {
        $data = $request->validated();
        list($newFile, $updatedFolders) = $this->service->uploadFile($data);

        if (isset($newFile, $updatedFolders)) {
            return response()->json([
                'file' => new FileResource($newFile),
                'updatedFolders' => sizeof($updatedFolders) > 0 ? FileResource::collection($updatedFolders) : null,
            ]);
        }
        return response()->json(status: 500);
    }

    public function rename(File $file, RenameRequest $request): JsonResponse
    {
        $data = $request->validated();
        $updatedFile = $this->service->rename($file, $data['name']);
        return response()->json(['file' => new FileResource($updatedFile)]);
    }

    public function delete(File $file): JsonResponse
    {
        $files = $this->service->delete($file);
        if (!isset($files)) {
            return response()->json(status: 500);
        }
        return response()->json(['updatedFolders' => FileResource::collection(collect($files))]);
    }
}
