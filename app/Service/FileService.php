<?php

namespace App\Service;

use App\Jobs\Auth\StoreUserJob;
use App\Models\File;
use App\Models\UserFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FileService
{
    public function upload(array $data): bool
    {
        $result = false;
        try {
            DB::beginTransaction();
//            $user = Auth::user();

            switch ($data['type']) {
                case File::FILE:
                    break;
                case File::FOLDER:
                    $result = $this->_createFolder($data);
                    break;
                case File::IMAGE:
                    break;
            }

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
        }

        return $result;
    }

    private function _createFolder(array $data): bool
    {
        try {
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
        } catch (\Exception $exception) {
            return false;
        }
        return true;
    }
}