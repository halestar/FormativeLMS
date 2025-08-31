<?php

namespace App\Console\Commands;

use halestar\LaravelDropInCms\Models\SystemBackup;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class FreshDb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fablms:fresh {--no-optimize}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes the database, reseed it and restores the cms.';
	protected $keepDirs =
		[
			'app',
			'framework',
			'logs',
		];

    /**
     * Execute the console command.
     */
    public function handle()
    {
	    //first, is there CMS data backed up?
	    $cmsSave = new SystemBackup();
	    if(!Storage::disk('local')
		    ->exists('cms-backup.json'))
	    {
		    //save the cms data. to the file.
		    $this->info('Backing up CMS Data...');
		    $cmsData = $cmsSave->getBackupData();
		    Storage::disk('local')
			    ->put('cms-backup.json', $cmsData);
	    }
	    else
	    {
		    $this->info('CMS Data backed up previously, using this data.');
	    }
	    $this->info("Refreshing and Seeding the DB");
        //resfresh the db
        $this->call('migrate:fresh', ['--seed' => true]);
	    $this->info('Removing all local files');
	    //delete all the work files.
	    $allDirs = File::directories(storage_path('/'));
	    foreach($allDirs as $dir)
	    {
		    $dirName = basename($dir);
		    if(!in_array($dirName, $this->keepDirs))
		    {
			    $this->info('Deleting Folder ' . $dirName);
			    File::deleteDirectory($dir);
		    }
	    }
	    $this->info('Restoring CMS Data...');
	    $cmsData = Storage::disk('local')
	                      ->get('cms-backup.json');
        $cmsSave->restore($cmsData);
	    //delete the file
	    Storage::disk('local')
	           ->delete('cms-backup.json');
	    if($this->option('no-optimize'))
        {
            $this->call('optimize:clear');
            $this->info('All optimizations are cleared.');
        }
    }
}
