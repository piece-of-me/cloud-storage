<?php

namespace Database\Seeders;

use App\Models\File;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FileTypesSeeder extends Seeder
{
    private const FILE_TYPES = [
        File::FILE => 'file',
        File::FOLDER => 'folder',
        File::IMAGE => 'image'
    ];

    public function run(): void
    {
        foreach (self::FILE_TYPES as $fileType) {
            DB::table('file_types')->insert([
                'name' => $fileType,
            ]);
        }
    }
}
