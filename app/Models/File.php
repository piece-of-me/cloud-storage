<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

/**
 * @property int id
 * @property string name
 * @property int type_id
 * @property int size
 * @property string extension
 * @property string path
 * @property null|int parent_id
 * @property int views
 * @property int downloads
 * @property null|string public_hash
 * @property null|\DateTime created_at
 * @property null|\DateTime updated_at
 *
 * @property Collection files
 */
class File extends Model
{
    use HasFactory;

    const FILE = 1;
    const FOLDER = 2;
    const IMAGE = 3;

    protected $table = 'files';
    protected $guarded = false;

    public static function getFileTypeId(string $mime): int
    {
        $imagesMime = [
            'image/bmp',
            'image/cgm',
            'image/g3fax',
            'image/gif',
            'image/ief',
            'image/jpeg',
            'image/ktx',
            'image/pjpeg',
            'image/png',
            'image/prs.btif',
            'image/svg+xml',
            'image/tiff',
            'image/vnd.adobe.photoshop',
            'image/vnd.dece.graphic',
            'image/vnd.djvu',
            'image/vnd.dvb.subtitle',
            'image/vnd.dwg',
            'image/vnd.dxf',
            'image/vnd.fastbidsheet',
            'image/vnd.fpx',
            'image/vnd.fst',
            'image/vnd.fujixerox.edmics-mmr',
            'image/vnd.fujixerox.edmics-rlc',
            'image/vnd.ms-modi',
            'image/vnd.net-fpx',
            'image/vnd.wap.wbmp',
            'image/vnd.xiff',
            'image/webp',
            'image/x-citrix-jpeg',
            'image/x-citrix-png',
            'image/x-cmu-raster',
            'image/x-cmx',
            'image/x-freehand',
            'image/x-icon',
            'image/x-pcx',
            'image/x-pict',
            'image/x-png',
            'image/x-portable-anymap',
            'image/x-portable-bitmap',
            'image/x-portable-graymap',
            'image/x-portable-pixmap',
            'image/x-rgb',
            'image/x-xbitmap',
            'image/x-xpixmap',
            'image/x-xwindowdump',
        ];

        return in_array($mime, $imagesMime) ? self::IMAGE : self::FILE;
    }

    public function increaseNumberOfDownloads(): void
    {
        try {
            DB::beginTransaction();
            $this->update([
                'downloads' => $this->downloads + 1,
            ]);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Не удалось обновить поле "downloads" для файла (ID: ' . $this->id . ' ; NAME: ' . $this->name . ') - ' . $exception->getMessage());
        }
    }

    public function increaseNumberOfViews(): void
    {
        try {
            DB::beginTransaction();
            $this->update([
                'views' => $this->views + 1,
            ]);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Не удалось обновить поле "views" для файла (ID: ' . $this->id . ' ; NAME: ' . $this->name . ') - ' . $exception->getMessage());
        }
    }


    public function owner() {
        return $this->belongsToMany(User::class, 'user_files', 'file_id', 'user_id');
    }
}
