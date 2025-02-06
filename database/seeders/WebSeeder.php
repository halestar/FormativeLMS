<?php

namespace Database\Seeders;

use halestar\LaravelDropInCms\Models\SystemBackup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class WebSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if(Storage::disk('local')->exists('web_backup.json'))
        {
            $webCMS = new SystemBackup();
            $webCMS->restore(Storage::disk('local')->get('web_backup.json'));
        }
    }
}
