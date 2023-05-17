<?php

namespace App\Http\Helpers\FileSystem;

use App\Models\File;
use App\Jobs\DeleteTemporaryArchiveJob;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class Folder
{
    private ZipArchive $zip;
    private File $model;
    private string $zipPath;
    private string $zipName;

    /**
     * @throws \Exception
     */
    public function __construct(File $model)
    {
        $this->model = $model;
        $this->zipName = $model->name . '.zip';
        $this->zipPath = 'storage/' . $model->owner->first()->login . '/' . $this->zipName;
        $this->zip = new ZipArchive();

        if ($this->zip->open($this->zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \Exception('Не удалось создать zip-архив \'' . $this->zipPath . '\'');
        }
        DeleteTemporaryArchiveJob::dispatchAfterResponse($this->zipPath);
    }

    public function download(): BinaryFileResponse
    {
        $this->_collectFilesInArchive($this->model->path);
        $this->zip->close();
        $this->model->increaseNumberOfDownloads();
        return response()->download($this->zipPath, headers: ['Content-Type' => 'application/zip', 'File-Name' => $this->zipName]);
    }

    private function _collectFilesInArchive(string $path, string $pathPrefix = ''): void
    {
        $files = Storage::disk('public')->files($path);
        foreach ($files as $curFile) {
            $this->zip->addFile(public_path('storage/' . $curFile), $pathPrefix . basename($curFile));
        }
        $directories = Storage::disk('public')->directories($path);

        foreach ($directories as $curDirectory) {
            $pathParts = explode('/', $curDirectory);
            $directoryName = array_pop($pathParts);
            $numFiles = $this->zip->numFiles;
            $this->_collectFilesInArchive($curDirectory, $pathPrefix . $directoryName . '/');
            if ($numFiles == $this->zip->numFiles) {
                $this->zip->addEmptyDir($pathPrefix . $directoryName);
            }
        }
    }
}