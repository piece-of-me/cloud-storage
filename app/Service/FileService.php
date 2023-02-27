<?php

namespace App\Service;

use App\Models\File;
use App\Models\UserFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File as FileSystem;
use Illuminate\Http\Exceptions\HttpResponseException;

class FileService
{
    private const INCREASE_SIZE = 1;
    private const DECREASE_SIZE = 2;

    public function createFolder(array $data): ?File
    {
        $newFolder = null;
        try {
            DB::beginTransaction();

            $user = Auth::user();
            $parent = isset($data['parent_id']) ? File::find($data['parent_id']) : null;
            $path = isset($parent) ? $parent->path . '/' . $data['name'] : $user->login . '/' . $data['name'];
            if (Storage::disk('public')->exists($path)) {
                throw new HttpResponseException(response()->json([
                    'message' => 'Папка с таким именем уже существует',
                ], 500));
            }
            $folderInfo = [
                'name' => $data['name'],
                'type_id' => File::FOLDER,
                'size' => 0,
                'path' => $path,
                'parent_id' => $parent->id ?? null,
            ];
            $newFolder = File::firstOrCreate($folderInfo);
            Storage::disk('public')->makeDirectory($path);
            UserFile::create([
                'user_id' => $user->id,
                'file_id' => $newFolder->id,
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            if ($exception instanceof HttpResponseException) {
                throw $exception;
            }
            Log::error('Ошибка создания папки - ' . $exception->getMessage());
        }
        return $newFolder;
    }

    public function uploadFile(array $data): ?array
    {
        $result = null;
        if (!isset($data['type'])) {
            $data['type'] = File::getFileTypeId($data['file']->getClientMimeType());
        }
        try {
            DB::beginTransaction();

            $user = Auth::user();
            $file = $data['file'];
            $parent = isset($data['parent_id']) ? File::find($data['parent_id']) : null;
            $folder = isset($parent) ? $parent->path . '/' : $user->login . '/';
            $fileName = $file->getClientOriginalName();
            if (Storage::disk('public')->exists($folder . $fileName)) {
                throw new HttpResponseException(response()->json([
                    'message' => 'Такой файл уже существует',
                ], 500));
            }
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
            $result = [$newFile, $files];

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Ошибка загрузки файла - ' . $exception->getMessage());
            if ($exception instanceof HttpResponseException) {
                throw $exception;
            }
        }
        return $result;
    }

    public function rename(File $file, string $newName): File
    {
        if ($file->type_id == 2) {
            $newPath = preg_replace('/[А-Яa-я\w\s-]+$/', $newName, $file->path);
            $errorMessage = 'Папка с таким именем уже существует';
        } else {
            if (false === strpos($newName, '.')) {
                $newName .= '.' . $file->extension;
            }
            $newPath = preg_replace('/[А-Яa-я\w\s\.-]+$/', $newName, $file->path);
            $errorMessage = 'Файл с таким именем уже существует';
        }

        if (Storage::disk('public')->exists($newPath)) {
            throw new HttpResponseException(response()->json([
                'message' => $errorMessage,
            ], 500));
        }

        Storage::disk('public')->move($file->path, $newPath);
        $file->update([
            'path' => $newPath,
            'name' => $newName,

        ]);
        return $file;
    }

    public function move(File $file, ?File $newParent): ?array
    {
        $result = null;
        try {
            DB::beginTransaction();

            $files = [];
            $newPath = (isset($newParent) ? $newParent->path : Auth::user()->login) . '/' . $file->name;

            if (Storage::disk('public')->exists($newPath)) {
                throw new HttpResponseException(response()->json([
                    'message' => $file->type_id === File::FOLDER ? 'Папка уже существует' : 'Файл уже существует',
                ], 500));
            }

            Storage::disk('public')->move($file->path, $newPath);
            if (isset($file->parent_id)) {
                $this->_updateParentFoldersSize($file->parent_id, $file->size, $files, self::DECREASE_SIZE);
            }
            $file->update([
                'parent_id' => $newParent?->id,
                'path' => $newPath,
            ]);
            if (isset($newParent)) {
                $this->_updateParentFoldersSize($file->parent_id, $file->size, $files);
            }

            $result = [$file, $files];
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Ошибка при перемещении файла - ' . $exception->getMessage());
            if ($exception instanceof HttpResponseException) {
                throw $exception;
            }
        }
        return $result;
    }

    public function copy(File $fileToCopy, ?File $newParent): ?array
    {
        $result = null;
        try {
            DB::beginTransaction();

            $files = [];
            $user = Auth::user();
            $newPath = (isset($newParent) ? $newParent->path : $user->login) . '/' . $fileToCopy->name;
            $newParentId = $newParent?->id;

            if (Storage::disk('public')->exists($newPath)) {
                throw new HttpResponseException(response()->json([
                    'message' => $fileToCopy->type_id === File::FOLDER ? 'Папка уже существует' : 'Файл уже существует',
                ], 500));
            }

            FileSystem::copyDirectory(storage_path('public/' . $fileToCopy->path), storage_path('public/' . $newPath));
            if ($fileToCopy->type_id == File::FOLDER) {
                $info = [
                    'name' => $fileToCopy->name,
                    'type_id' => $fileToCopy->type_id,
                    'size' => $fileToCopy->size,
                    'path' => $fileToCopy->path,
                    'parent_id' => $newParentId,
                ];
            } else {
                $info = [
                    'name' => $fileToCopy->name,
                    'type_id' => $fileToCopy->type_id,
                    'size' => $fileToCopy->size,
                    'extension' => $fileToCopy->extension,
                    'path' => $fileToCopy->path,
                    'parent_id' => $newParentId,
                ];
            }
            $file = File::create($info);
            UserFile::create([
                'user_id' => $user->id,
                'file_id' => $file->id,
            ]);

            if ($fileToCopy->type_id == File::FOLDER) {
                $this->_copyNestedFiles($file, $fileToCopy, $files);
            }
            $updatedFiles = [];
            if ($file->size > 0 && isset($file->parent_id)) {
                $this->_updateParentFoldersSize($file->parent_id, $file->size, $updatedFiles);
            }
            $files[] = $file;
            $result = [$files, $updatedFiles];
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Ошибка при копировании файла - ' . $exception->getMessage());
            if ($exception instanceof HttpResponseException) {
                throw $exception;
            }
        }

        return $result;
    }

    public function delete(File $file): ?array
    {
        $files = [];
        try {
            DB::beginTransaction();
            if ($file->size > 0 && isset($file->parent_id)) {
                $this->_updateParentFoldersSize($file->parent_id, $file->size, $files, self::DECREASE_SIZE);
            }
            if ($file->type_id == File::FOLDER) {
                Storage::disk('public')->deleteDirectory($file->path);
                $this->_deleteNestedFiles($file);
            } else {
                Storage::disk('public')->delete($file->path);
            }
            $relationship = UserFile::where('user_id', '=', Auth::id())->where('file_id', '=', $file->id)->get();
            $relationship->first()->delete();
            $file->delete();
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Ошибка удаления файла - ' . $exception->getMessage());
            return null;
        }
        return $files;
    }

    private function _updateParentFoldersSize(int $parentId, int $size, array &$files, int $operation = self::INCREASE_SIZE): void
    {
        $parent = File::find($parentId);
        $files[] = $parent;
        $size = $operation === self::INCREASE_SIZE ? $parent->size + $size : $parent->size - $size;
        $parent->update(['size' => max($size, 0)]);
        if (isset($parent->parent_id)) {
            $this->_updateParentFoldersSize($parent->parent_id, $size, $files, $operation);
        }
    }

    private function _copyNestedFiles(File $newFile, File $oldFile, array &$files): void
    {
        $user = Auth::user();
        $attachedFiles = $user->files->filter(fn($curFile) => $curFile->parent_id == $oldFile->id);
        $attachedFiles->each(function ($attachedFile) use ($user, $newFile, $oldFile, &$files) {
            if ($attachedFile->type_id == File::FOLDER) {
                $this->_copyNestedFiles($newFile, $oldFile, $files);
            }
            $createdFile = File::create([
                'name' => $attachedFile->name,
                'type_id' => $attachedFile->type_id,
                'size' => $attachedFile->size,
                'extension' => $attachedFile->extension,
                'path' => $attachedFile->path,
                'parent_id' => $newFile->id,
            ]);
            UserFile::create([
                'user_id' => $user->id,
                'file_id' => $createdFile->id,
            ]);
            $files[] = $createdFile;
        });
    }

    private function _deleteNestedFiles(File $file): void
    {
        $user = Auth::user();
        $attachedFiles = $user->files->filter(fn($curFile) => $curFile->parent_id == $file->id);
        $attachedFiles->each(function ($attachedFile) use ($user) {
            if ($attachedFile->type_id == File::FOLDER) {
                $this->_deleteNestedFiles($attachedFile);
            }
            $relationship = UserFile::where('user_id', '=', $user->id)->where('file_id', '=', $attachedFile->id)->get();
            $relationship->first()->delete();
            $attachedFile->delete();
        });
    }
}
