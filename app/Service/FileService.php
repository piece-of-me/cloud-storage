<?php

namespace App\Service;

use App\Models\File;
use App\Models\UserFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FileService
{
    public function upload(array $data): ?File
    {
        $result = null;
        if (!isset($data['type'])) {
            $data['type'] = File::getFileTypeId($data['file']->getClientMimeType());
        }
        try {
            DB::beginTransaction();

            switch ($data['type']) {
                case File::FILE:
                case File::IMAGE:
                    $result = $this->_uploadFile($data);
                    break;
                case File::FOLDER:
                    $result = $this->_createFolder($data);
                    break;
            }

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
        }

        return $result;
    }

    private function _createFolder(array $data): ?File
    {
        $user = Auth::user();
        $parent = isset($data['parent_id']) ? File::find($data['parent_id']) : null;
        $path = $user->login . '/' . (isset($parent) ? $parent->path . '/' . $data['name'] : $data['name']);
        Storage::disk('public')->makeDirectory($path);
        $folderInfo = [
            'name' => $data['name'],
            'type_id' => $data['type'],
            'size' => 0,
            'path' => $path,
            'parent_id' => $parent->id ?? null,
        ];
        $newFolder = File::firstOrCreate($folderInfo);
        UserFile::create([
            'user_id' => $user->id,
            'file_id' => $newFolder->id,
        ]);
        return $newFolder;
    }

    private function _uploadFile(array $data): ?File
    {
        $user = Auth::user();
        $file = $data['file'];
        $parent = isset($data['parent_id']) ? File::find($data['parent_id']) : null;
        $folder = $user->login . '/' . (isset($parent) ? $parent->path . '/' : '');
        $fileName = $file->getClientOriginalName();
        Storage::disk('public')->putFileAs($folder, $file, $fileName, 'public');
        $info = [
            'name' => $data['name'],
            'type_id' => $data['type'],
            'size' => $file->getSize(),
            'extension' => $file->extension(),
            'path' => $folder . $data['name'],
            'parent_id' => $parent->id ?? null,
        ];
        $newFile = File::firstOrCreate($info);
        UserFile::create([
            'user_id' => $user->id,
            'file_id' => $newFile->id,
        ]);
        $files = [];
        if (isset($newFile->parent_id)) {
            $this->_updateParentFoldersSize($newFile->parent_id, $newFile->size, $files);
        }
        return $newFile;
    }

    private function _updateParentFoldersSize(int $parentId, int $size, array &$files, string $operation = 'upload'): void
    {
        $parent = File::find($parentId);
        $parent->update(['size' => $operation == 'upload' ? $parent->size + $size : $parent->size - $size]);
        if (isset($parent->parent_id)) {
            $this->_updateParentFoldersSize($parent->parent_id, $size, $files);
        }
    }
}