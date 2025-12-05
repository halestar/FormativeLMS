<?php

namespace App\Console\Commands;

use App\Classes\Settings\StorageSettings;
use App\Enums\WorkStoragesInstances;
use App\Models\Utilities\TemporaryFiler;
use Illuminate\Console\Command;

class CleanupWorkFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fablms:cleanup-work-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will delete any work files not attached to something and all temporary filers that have exceeded the expiration time.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
		$storageSettings = app(StorageSettings::class);
		//first, we get all the stale temporary filers.
	    $filers = TemporaryFiler::where('updated_at', '<', now()->subMinutes(config('lms.temp-filer-expiration')))->get();
		//we go through each one to ensure all the files are deleted.
		foreach($filers as $filer)
			$filer->delete();
		//now make a list of all the individual connections
	    $connections = [];
		foreach(WorkStoragesInstances::cases() as $instance)
		{
			$connection = $storageSettings->getWorkConnection($instance);
			if($connection)
				$connections[$connection->id] = $connection;
		}
		//now we clean up the connections
	    foreach($connections as $connection)
			$connection->cleanUp();
    }
}
