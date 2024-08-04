<?php

namespace App\Console\Commands;

use halestar\LaravelDropInCms\Models\SystemBackup;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class FreshDb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fablms:fresh-db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes the database, reseed it and restores the cms.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //save the cms data.
        $cmsSave = new SystemBackup();
        $cmsData = $cmsSave->getBackupData();
        //resfresh the db
        $this->call('migrate:fresh', ['--seed' => true]);
        $cmsSave->restore($cmsData);
    }
}
