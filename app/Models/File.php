<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property string name
 * @property int size
 * @property string path
 * @property null|int parent_id
 * @property int views
 * @property int downloads
 * @property null|string public_hash
 * @property null|\DateTime created_at
 * @property null|\DateTime updated_at
 */
class File extends Model
{
    use HasFactory;

    const FILE = 1;
    const FOLDER = 2;
    const IMAGE = 3;

    protected $table = 'files';
    protected $guarded = false;
}
