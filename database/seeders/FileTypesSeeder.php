<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FileTypesSeeder extends Seeder
{
    private const FILE_TYPES = ['file', 'folder', 'image'];

    public function run(): void
    {
        foreach (self::FILE_TYPES as $fileType) {
            DB::table('file_types')->insert([
                'name' => $fileType,
            ]);
        }
    }
}
