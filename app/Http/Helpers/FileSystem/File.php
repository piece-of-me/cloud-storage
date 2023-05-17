<?php

namespace App\Http\Helpers\FileSystem;

use App\Models\File as FileModel;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class File
{
    private FileModel $model;

    public function __construct(FileModel $model)
    {
        $this->model = $model;
    }

    public function download(): StreamedResponse
    {
        $this->model->increaseNumberOfDownloads();
        return Storage::disk('public')->download($this->model->path);
    }
}