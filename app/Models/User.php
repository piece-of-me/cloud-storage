<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int id
 * @property string login
 * @property string email
 * @property string password
 * @property \DateTime created_at
 * @property \DateTime updated_at
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';
    protected $guarded = false;

    public function files() {
        return $this->belongsToMany(File::class, 'user_files', 'user_id', 'file_id');
    }

//    public function getFilesAttribute()
//    {
//        $files = $this->allFiles()->get();
//        $groupedFilesByParentId = $files->groupBy('parent_id');
//        $root = $groupedFilesByParentId->pull('');
//
//        $root->map(function ($file) use ($groupedFilesByParentId) {
//            if ($file->type_id !== File::FOLDER) return $file;
//            if ($groupedFilesByParentId->has($file->id)) {
//                $file->content = $this->_prepareSubFolder($file->id, $groupedFilesByParentId);
//            }
//            return $file;
//        });
//        return $root;
//    }
//
//    private function _prepareSubFolder(int $parentId, Collection $groupedFilesByParentId): Collection
//    {
//        $root = $groupedFilesByParentId->pull($parentId);
//        if (!$root instanceof Collection) return $root;
//        $root->map(function ($file) use ($groupedFilesByParentId) {
//            if ($file->type_id !== File::FOLDER) return $file;
//            if ($groupedFilesByParentId->has($file->id)) {
//                $file->content = $this->_prepareSubFolder($file->id, $groupedFilesByParentId);
//            }
//            return $file;
//        });
//        return $root;
//    }
}
